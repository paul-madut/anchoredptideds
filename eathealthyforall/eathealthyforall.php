<?php
/**
 * Plugin Name:       EatHealthyForAll
 * Description:        Runs the EatHealthyForAll single-page app on WordPress. Use the [ehfa] shortcode, the /ehfa launch URL, or serve it as the site front page. Self-contained React bundle in an isolated iframe — no theme conflicts.
 * Version:           1.0.0
 * Author:            Anchored
 * License:           GPL-2.0-or-later
 * Text Domain:       ehfa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EHFA_VERSION', '1.0.0' );
define( 'EHFA_DIR', plugin_dir_path( __FILE__ ) );
define( 'EHFA_URL', plugin_dir_url( __FILE__ ) );
define( 'EHFA_SLUG', 'ehfa' );
define( 'EHFA_TITLE', 'EatHealthyForAll' );
define( 'EHFA_FILE', 'apps/app.html' );

/** Static URL to the bundle (served by the web server, not PHP). */
function EHFA_asset_url() {
	return EHFA_URL . EHFA_FILE;
}

/** Read and stream the bundle full-screen. */
function EHFA_serve_bundle() {
	$path = EHFA_DIR . EHFA_FILE;
	if ( ! file_exists( $path ) ) {
		status_header( 404 );
		nocache_headers();
		echo 'App bundle not found.';
		exit;
	}
	status_header( 200 );
	header( 'Content-Type: text/html; charset=utf-8' );
	header( 'X-Content-Type-Options: nosniff' );
	readfile( $path );
	exit;
}

/* ---- Shortcode: [ehfa] (responsive iframe) ---- */
function EHFA_shortcode( $atts ) {
	$atts = shortcode_atts(
		array( 'height' => '85vh', 'width' => '100%' ),
		$atts,
		EHFA_SLUG
	);
	return sprintf(
		'<iframe src="%1$s" title="%2$s" loading="lazy" '
		. 'style="width:%3$s;height:%4$s;border:0;display:block;margin:0 auto;" '
		. 'allow="fullscreen; clipboard-write" allowfullscreen></iframe>',
		esc_url( EHFA_asset_url() ),
		esc_attr( EHFA_TITLE ),
		esc_attr( $atts['width'] ),
		esc_attr( $atts['height'] )
	);
}
add_action( 'init', function () {
	add_shortcode( EHFA_SLUG, 'EHFA_shortcode' );
} );

/* ---- Launch URL: /ehfa (full-screen) ---- */
add_action( 'init', function () {
	add_rewrite_rule( '^' . EHFA_SLUG . '/?$', 'index.php?EHFA_launch=1', 'top' );
} );
add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'EHFA_launch';
	return $vars;
} );

/* ---- Front-page takeover (opt-in setting) ---- */
add_action( 'template_redirect', function () {
	if ( get_query_var( 'EHFA_launch' ) ) {
		EHFA_serve_bundle();
	}
	if ( get_option( 'EHFA_as_front', '0' ) === '1' && is_front_page() ) {
		EHFA_serve_bundle();
	}
} );

/* ---- Activation: flush rewrites ---- */
register_activation_hook( __FILE__, function () {
	add_rewrite_rule( '^' . 'ehfa' . '/?$', 'index.php?EHFA_launch=1', 'top' );
	flush_rewrite_rules();
} );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

/* ---- Settings page (Settings -> EatHealthyForAll) ---- */
add_action( 'admin_menu', function () {
	add_options_page( EHFA_TITLE, EHFA_TITLE, 'manage_options', EHFA_SLUG, 'EHFA_settings_page' );
} );
add_action( 'admin_init', function () {
	register_setting( 'EHFA_group', 'EHFA_as_front', array( 'sanitize_callback' => 'EHFA_sanitize_front' ) );
} );
function EHFA_sanitize_front( $val ) {
	$on = ( '1' === $val ) ? '1' : '0';
	// Front-page rule is virtual; flush so it takes effect immediately.
	flush_rewrite_rules();
	return $on;
}
function EHFA_settings_page() {
	$as_front = get_option( 'EHFA_as_front', '0' );
	echo '<div class="wrap"><h1>' . esc_html( EHFA_TITLE ) . '</h1>';
	echo '<form method="post" action="options.php">';
	settings_fields( 'EHFA_group' );
	echo '<table class="form-table"><tr><th scope="row">Serve as front page</th><td>';
	printf(
		'<label><input type="checkbox" name="EHFA_as_front" value="1" %s> Show this app at the site root (<code>%s</code>)</label>',
		checked( $as_front, '1', false ),
		esc_html( home_url( '/' ) )
	);
	echo '</td></tr></table>';
	submit_button();
	echo '</form>';
	echo '<hr><h2>Other ways to use it</h2><table class="widefat striped" style="max-width:680px"><tbody>';
	printf( '<tr><td><strong>Shortcode</strong></td><td><code>[%s]</code></td></tr>', esc_html( EHFA_SLUG ) );
	printf( '<tr><td><strong>Launch URL</strong></td><td><a href="%1$s" target="_blank" rel="noopener">%1$s</a></td></tr>', esc_url( home_url( '/' . EHFA_SLUG ) ) );
	echo '</tbody></table>';
	echo '<p style="color:#666">If a URL 404s, visit <strong>Settings &rarr; Permalinks</strong> and click <strong>Save Changes</strong> once.</p>';
	echo '</div>';
}
