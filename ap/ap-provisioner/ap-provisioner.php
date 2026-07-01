<?php
/**
 * Plugin Name: AP Provisioner
 * Description: One-click site provisioning endpoint. Pre-installed on every target WordPress install; the CRM's "Activate" button POSTs a brand config + asset URLs and this plugin installs/activates the theme + plugins, applies branding options, sideloads logo/hero, imports the WooCommerce catalog, and scaffolds pages — turning a blank WP into a finished branded peptide store.
 * Version: 1.0.0
 * Author: Anchored Peptides
 *
 * Security: the REST route requires an authenticated user with `manage_options`
 * (supply a WordPress Application Password via HTTP Basic auth). If the
 * `ap_provision_secret` option is set at image-bake time, a matching
 * `secret` field is additionally required.
 *
 * @package APProvisioner
 */
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'AP_PROV_VERSION', '1.0.0' );
define( 'AP_PROV_JOB_OPTION', 'ap_provision_job' );

/* =========================================================
 * REST ROUTES
 * ========================================================= */
add_action( 'rest_api_init', function () {
	register_rest_route( 'ap-provision/v1', '/build', array(
		'methods'             => 'POST',
		'callback'            => 'ap_prov_build',
		'permission_callback' => 'ap_prov_permission',
	) );
	register_rest_route( 'ap-provision/v1', '/status', array(
		'methods'             => 'GET',
		'callback'            => 'ap_prov_status',
		'permission_callback' => 'ap_prov_permission',
	) );
	// Lightweight readiness probe — lets the CRM confirm the plugin is present
	// and WooCommerce is available before it ever ships a build.
	register_rest_route( 'ap-provision/v1', '/ping', array(
		'methods'             => 'GET',
		'callback'            => 'ap_prov_ping',
		'permission_callback' => 'ap_prov_permission',
	) );
} );

/**
 * Only authenticated admins may provision. Application Passwords authenticate
 * the Basic-auth request as that user, so this capability check is the gate.
 */
function ap_prov_permission() {
	return current_user_can( 'manage_options' );
}

function ap_prov_ping() {
	return new WP_REST_Response( array(
		'ok'          => true,
		'version'     => AP_PROV_VERSION,
		'woocommerce' => class_exists( 'WooCommerce' ),
		'wp_version'  => get_bloginfo( 'version' ),
		'php'         => PHP_VERSION,
	), 200 );
}

function ap_prov_status() {
	$job = get_option( AP_PROV_JOB_OPTION, array() );
	return new WP_REST_Response( is_array( $job ) ? $job : array(), 200 );
}

/* =========================================================
 * BUILD — the whole provisioning pipeline (idempotent).
 * Runs synchronously and writes progress into AP_PROV_JOB_OPTION
 * after every step, so the /status route reflects progress even
 * if the client connection drops mid-run.
 * ========================================================= */
function ap_prov_build( WP_REST_Request $req ) {
	$cfg = $req->get_json_params();
	if ( ! is_array( $cfg ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'Invalid JSON body.' ), 400 );
	}

	// Optional shared-secret gate (defense in depth on top of the auth check).
	$secret = get_option( 'ap_provision_secret', '' );
	if ( '' !== $secret && ( ! isset( $cfg['secret'] ) || ! hash_equals( $secret, (string) $cfg['secret'] ) ) ) {
		return new WP_REST_Response( array( 'ok' => false, 'error' => 'Bad provisioning secret.' ), 403 );
	}

	@set_time_limit( 0 );
	@ini_set( 'memory_limit', '512M' );
	ignore_user_abort( true );

	$job = array(
		'status'    => 'running',
		'started'   => current_time( 'mysql' ),
		'finished'  => null,
		'live_url'  => home_url( '/' ),
		'warnings'  => array(),
		'log'       => array(),
		'counts'    => array(),
	);
	$log  = function ( $msg ) use ( &$job ) { $job['log'][] = $msg; update_option( AP_PROV_JOB_OPTION, $job ); };
	$warn = function ( $msg ) use ( &$job ) { $job['warnings'][] = $msg; };

	ap_prov_load_upgrader_deps();

	// 1) Ensure WooCommerce (install from w.org if requested & missing).
	if ( ! empty( $cfg['ensure_woocommerce'] ) && ! class_exists( 'WooCommerce' ) ) {
		$log( 'Installing WooCommerce…' );
		$err = ap_prov_ensure_woocommerce();
		if ( $err ) $warn( 'WooCommerce: ' . $err );
	}

	// 2) Theme + plugins from the bundle URLs.
	$bundle = isset( $cfg['bundle'] ) && is_array( $cfg['bundle'] ) ? $cfg['bundle'] : array();
	if ( ! empty( $bundle['theme'] ) ) {
		$log( 'Installing theme…' );
		$err = ap_prov_install_theme( (string) $bundle['theme'], 'anchored-peptides' );
		if ( $err ) $warn( 'Theme: ' . $err );
	}
	foreach ( array(
		'homepage'    => 'anchored-peptides-homepage/anchored-peptides-homepage.php',
		'coming_soon' => 'anchored-peptides-coming-soon/anchored-peptides-coming-soon.php',
	) as $key => $plugin_file ) {
		if ( empty( $bundle[ $key ] ) ) continue;
		$log( "Installing plugin: {$key}…" );
		$err = ap_prov_install_plugin( (string) $bundle[ $key ], $plugin_file );
		if ( $err ) $warn( "Plugin {$key}: " . $err );
	}

	// 3) Brand options — name, palette, fonts, copy.
	$log( 'Applying branding…' );
	if ( ! empty( $cfg['brand_name'] ) ) update_option( 'blogname', sanitize_text_field( $cfg['brand_name'] ) );
	if ( isset( $cfg['tokens'] ) && is_array( $cfg['tokens'] ) ) update_option( 'ap_brand_tokens', wp_json_encode( $cfg['tokens'] ) );
	if ( ! empty( $cfg['fonts_url'] ) && 0 === strpos( (string) $cfg['fonts_url'], 'https://' ) ) update_option( 'ap_fonts_url', esc_url_raw( $cfg['fonts_url'] ) );
	if ( isset( $cfg['copy'] ) && is_array( $cfg['copy'] ) ) {
		foreach ( $cfg['copy'] as $k => $v ) {
			$k = preg_replace( '/[^a-z0-9_]/', '', strtolower( (string) $k ) );
			if ( '' === $k ) continue;
			update_option( 'ap_copy_' . $k, wp_kses_post( (string) $v ) );
		}
	}

	// 4) Sideload logo + hero image → shared options.
	if ( ! empty( $cfg['logo_url'] ) ) {
		$log( 'Sideloading logo…' );
		$id = ap_prov_sideload_media( (string) $cfg['logo_url'], true );
		if ( is_wp_error( $id ) ) $warn( 'Logo: ' . $id->get_error_message() );
		else update_option( 'ap_logo_id', (int) $id );
	}
	if ( ! empty( $cfg['hero_image_url'] ) ) {
		$log( 'Sideloading hero image…' );
		$id = ap_prov_sideload_media( (string) $cfg['hero_image_url'], false );
		if ( is_wp_error( $id ) ) $warn( 'Hero: ' . $id->get_error_message() );
		else update_option( 'ap_hero_image_id', (int) $id );
	}

	// 5) Pages / categories / homepage (idempotent scaffold).
	if ( function_exists( 'aph_activate' ) ) { $log( 'Scaffolding pages + categories…' ); aph_activate(); }

	// 6) Import the shared product catalog.
	if ( ! empty( $cfg['products_csv_url'] ) ) {
		if ( class_exists( 'WooCommerce' ) ) {
			$log( 'Importing product catalog…' );
			$res = ap_prov_import_products( (string) $cfg['products_csv_url'] );
			if ( is_wp_error( $res ) ) $warn( 'Catalog: ' . $res->get_error_message() );
			else $job['counts'] = $res;
		} else {
			$warn( 'Catalog skipped: WooCommerce not active.' );
		}
	}

	// 7) Coming-soon toggle. The plugin defaults to ENABLED, so a finished store
	//    must explicitly turn it off (option `apcs_enabled`, owned by that plugin).
	if ( isset( $cfg['coming_soon'] ) ) {
		update_option( 'apcs_enabled', $cfg['coming_soon'] ? '1' : '0' );
	}

	flush_rewrite_rules();

	$job['status']   = empty( $job['warnings'] ) ? 'complete' : 'complete_with_warnings';
	$job['finished'] = current_time( 'mysql' );
	update_option( AP_PROV_JOB_OPTION, $job );

	return new WP_REST_Response( array(
		'ok'       => true,
		'status'   => $job['status'],
		'live_url' => $job['live_url'],
		'warnings' => $job['warnings'],
		'counts'   => $job['counts'],
	), 200 );
}

/* =========================================================
 * HELPERS
 * ========================================================= */

/** Pull in the core upgrader / plugin / media includes we need. */
function ap_prov_load_upgrader_deps() {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/misc.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/theme.php';
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
}

/** Install (overwriting) a theme from a zip URL and switch to it. Returns error string or ''. */
function ap_prov_install_theme( $zip_url, $stylesheet ) {
	$skin     = new Automatic_Upgrader_Skin();
	$upgrader = new Theme_Upgrader( $skin );
	$result   = $upgrader->install( $zip_url, array( 'overwrite_package' => true ) );
	if ( is_wp_error( $result ) ) return $result->get_error_message();
	if ( false === $result ) return implode( '; ', (array) $skin->get_errors()->get_error_messages() ) ?: 'install failed';
	if ( ! wp_get_theme( $stylesheet )->exists() ) return "theme '{$stylesheet}' not found after install";
	switch_theme( $stylesheet );
	return '';
}

/** Install (overwriting) a plugin from a zip URL and activate it. Returns error string or ''. */
function ap_prov_install_plugin( $zip_url, $plugin_file ) {
	$skin     = new Automatic_Upgrader_Skin();
	$upgrader = new Plugin_Upgrader( $skin );
	$result   = $upgrader->install( $zip_url, array( 'overwrite_package' => true ) );
	if ( is_wp_error( $result ) ) return $result->get_error_message();
	if ( false === $result ) return implode( '; ', (array) $skin->get_errors()->get_error_messages() ) ?: 'install failed';
	$activated = activate_plugin( $plugin_file );
	if ( is_wp_error( $activated ) ) return $activated->get_error_message();
	return '';
}

/** Ensure WooCommerce is installed + active (from the w.org repo). Returns error string or ''. */
function ap_prov_ensure_woocommerce() {
	$api = plugins_api( 'plugin_information', array( 'slug' => 'woocommerce', 'fields' => array( 'sections' => false ) ) );
	if ( is_wp_error( $api ) || empty( $api->download_link ) ) return 'could not resolve WooCommerce download';
	$err = ap_prov_install_plugin( $api->download_link, 'woocommerce/woocommerce.php' );
	return $err;
}

/**
 * Sideload a remote image into the media library. When $is_svg_ok is true,
 * SVGs are permitted and scrubbed with the homepage plugin's sanitizer.
 * Returns attachment ID (int) or WP_Error.
 */
function ap_prov_sideload_media( $url, $is_svg_ok ) {
	$svg_filter = function ( $m ) { $m['svg'] = 'image/svg+xml'; return $m; };
	if ( $is_svg_ok ) add_filter( 'upload_mimes', $svg_filter );

	$tmp = download_url( $url );
	if ( is_wp_error( $tmp ) ) { if ( $is_svg_ok ) remove_filter( 'upload_mimes', $svg_filter ); return $tmp; }

	$name = basename( parse_url( $url, PHP_URL_PATH ) );
	if ( ! $name || false === strpos( $name, '.' ) ) $name = 'asset-' . wp_generate_password( 6, false ) . '.png';
	$file = array( 'name' => $name, 'tmp_name' => $tmp );

	$id = media_handle_sideload( $file, 0 );
	@unlink( $tmp );
	if ( $is_svg_ok ) remove_filter( 'upload_mimes', $svg_filter );

	if ( ! is_wp_error( $id ) && $is_svg_ok && function_exists( 'aph_sanitize_svg_file' ) ) {
		$path = get_attached_file( $id );
		if ( $path && preg_match( '/\.svgz?$/i', $path ) ) aph_sanitize_svg_file( $path );
	}
	return $id;
}

/**
 * Import a WooCommerce products CSV from a URL, reusing WooCommerce's own
 * auto column-mapping so standard export headers map correctly. Product images
 * referenced by URL in the CSV are sideloaded by the importer automatically.
 * Returns a counts array or WP_Error.
 */
function ap_prov_import_products( $csv_url ) {
	$tmp = download_url( $csv_url );
	if ( is_wp_error( $tmp ) ) return $tmp;

	$importer_class   = WC_ABSPATH . 'includes/import/class-wc-product-csv-importer.php';
	$controller_class = WC_ABSPATH . 'includes/admin/importers/class-wc-product-csv-importer-controller.php';
	if ( ! file_exists( $importer_class ) || ! file_exists( $controller_class ) ) {
		@unlink( $tmp );
		return new WP_Error( 'ap_prov_no_importer', 'WooCommerce CSV importer not available.' );
	}
	include_once $importer_class;
	include_once $controller_class;

	// Read headers to build the auto-mapping (WC's own heuristics).
	$handle = fopen( $tmp, 'r' );
	$raw_headers = $handle ? fgetcsv( $handle, 0, ',' ) : array();
	if ( $handle ) fclose( $handle );
	$raw_headers = array_map( 'trim', (array) $raw_headers );

	$controller = new WC_Product_CSV_Importer_Controller();
	$mapping    = $controller->auto_map_columns( $raw_headers );

	$importer = new WC_Product_CSV_Importer( $tmp, array(
		'mapping'          => $mapping,
		'parse'            => true,
		'update_existing'  => true,
		'delimiter'        => ',',
		'prevent_timeouts' => false,
	) );
	$result = $importer->import();
	@unlink( $tmp );

	return array(
		'imported' => isset( $result['imported'] ) ? count( $result['imported'] ) : 0,
		'updated'  => isset( $result['updated'] ) ? count( $result['updated'] ) : 0,
		'skipped'  => isset( $result['skipped'] ) ? count( $result['skipped'] ) : 0,
		'failed'   => isset( $result['failed'] ) ? count( $result['failed'] ) : 0,
	);
}
