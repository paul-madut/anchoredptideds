<?php
/**
 * Plugin Name: Anchored Peptides — Coming Soon
 * Description: A branded, animated coming-soon page that captures waitlist emails (stored in WordPress + best-effort push to Omnisend). Logged-out visitors see the coming-soon page; logged-in admins still see the full site, so nothing is removed. Toggle on/off under "Coming Soon".
 * Version: 1.1.1
 * Author: Anchored Peptides
 */
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'APCS_DIR', plugin_dir_path( __FILE__ ) );
define( 'APCS_URL', plugin_dir_url( __FILE__ ) );

/* =========================================================
 * 1. SUBSCRIBER STORAGE — a private custom post type.
 *    Each waitlist email is one post (title = email). Simple,
 *    dependency-free, browsable in wp-admin, CSV-exportable.
 * ========================================================= */
add_action( 'init', function () {
	register_post_type( 'apcs_subscriber', array(
		'labels' => array(
			'name'          => 'Waitlist',
			'singular_name' => 'Subscriber',
			'menu_name'     => 'Waitlist',
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => 'apcs-settings',
		'capability_type'     => 'post',
		'capabilities'        => array( 'create_posts' => 'do_not_allow' ), // only added via the form
		'map_meta_cap'        => true,
		'supports'            => array( 'title' ),
		'exclude_from_search' => true,
	) );
} );

function apcs_email_exists( $email ) {
	$q = new WP_Query( array(
		'post_type'      => 'apcs_subscriber',
		'title'          => $email,
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'post_status'    => 'any',
	) );
	return $q->have_posts();
}

function apcs_subscriber_count() {
	$c = wp_count_posts( 'apcs_subscriber' );
	return (int) ( ( $c->publish ?? 0 ) + ( $c->private ?? 0 ) + ( $c->draft ?? 0 ) );
}

/* =========================================================
 * 2. EMAIL CAPTURE — AJAX endpoint (logged-out friendly).
 * ========================================================= */
add_action( 'wp_ajax_apcs_subscribe', 'apcs_handle_subscribe' );
add_action( 'wp_ajax_nopriv_apcs_subscribe', 'apcs_handle_subscribe' );
function apcs_handle_subscribe() {
	// Honeypot — bots fill hidden fields.
	if ( ! empty( $_POST['apcs_hp'] ) ) {
		wp_send_json_success( array( 'message' => "You're on the list." ) ); // silently accept, store nothing
	}
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'apcs_subscribe' ) ) {
		wp_send_json_error( array( 'message' => 'Your session expired — please refresh and try again.' ), 403 );
	}
	$email = isset( $_POST['email'] ) ? sanitize_email( strtolower( trim( wp_unslash( $_POST['email'] ) ) ) ) : '';
	if ( ! $email || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'Please enter a valid email address.' ), 400 );
	}
	if ( apcs_email_exists( $email ) ) {
		wp_send_json_success( array( 'message' => "You're already anchored in.", 'dupe' => true ) );
	}

	$id = wp_insert_post( array(
		'post_type'   => 'apcs_subscriber',
		'post_title'  => $email,
		'post_status' => 'publish',
	), true );

	if ( is_wp_error( $id ) ) {
		wp_send_json_error( array( 'message' => 'Something went wrong — please try again.' ), 500 );
	}

	update_post_meta( $id, '_apcs_date', current_time( 'mysql' ) );
	update_post_meta( $id, '_apcs_source', sanitize_text_field( wp_unslash( $_POST['source'] ?? 'coming-soon' ) ) );
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	if ( $ip ) update_post_meta( $id, '_apcs_ip', $ip );

	/**
	 * Hook for forwarding to an ESP server-side (e.g. Omnisend/MailPoet API).
	 * do_action( 'apcs_subscriber_added', $email, $id );
	 */
	do_action( 'apcs_subscriber_added', $email, $id );

	wp_send_json_success( array( 'message' => "You're anchored in." ) );
}

/* =========================================================
 * 2b. OMNISEND SYNC — server-side push (reliable; runs even if a
 *     visitor blocks the JS snippet). No-op until an API key is set.
 * ========================================================= */
add_action( 'apcs_subscriber_added', 'apcs_push_omnisend', 10, 2 );
function apcs_push_omnisend( $email, $post_id ) {
	$key = trim( (string) get_option( 'apcs_omnisend_key', '' ) );
	if ( ! $key ) return;
	$payload = array(
		'identifiers' => array( array(
			'type'     => 'email',
			'id'       => $email,
			'channels' => array( 'email' => array( 'status' => 'subscribed', 'statusDate' => gmdate( 'c' ) ) ),
		) ),
		'tags' => array( 'waitlist', 'coming-soon' ),
	);
	$res = wp_remote_post( 'https://api.omnisend.com/v3/contacts', array(
		'headers' => array( 'X-API-KEY' => $key, 'Content-Type' => 'application/json' ),
		'body'    => wp_json_encode( $payload ),
		'timeout' => 6,
	) );
	if ( is_wp_error( $res ) ) {
		update_post_meta( $post_id, '_apcs_omnisend', 'error: ' . $res->get_error_message() );
		return;
	}
	$code = (int) wp_remote_retrieve_response_code( $res );
	update_post_meta( $post_id, '_apcs_omnisend', $code < 300 ? 'synced' : ( 'http ' . $code . ': ' . substr( wp_remote_retrieve_body( $res ), 0, 200 ) ) );
}

/* =========================================================
 * 3. COMING-SOON TAKEOVER — public sees the page, admins see the site.
 * ========================================================= */
add_action( 'template_redirect', function () {
	if ( ! get_option( 'apcs_enabled', '1' ) ) return;

	// Admins preview the coming-soon page with ?apcs_preview=1; otherwise they see the real site.
	$force_preview = isset( $_GET['apcs_preview'] ) && current_user_can( 'manage_options' );
	if ( current_user_can( 'manage_options' ) && ! $force_preview ) return;

	// Never take over admin/login/ajax/cron/rest/feeds.
	if ( is_admin() || wp_doing_ajax() || ( defined( 'DOING_CRON' ) && DOING_CRON )
		|| ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_feed() || is_robots() ) return;

	status_header( 200 );
	nocache_headers();
	include APCS_DIR . 'coming-soon-template.php';
	exit;
}, 0 );

/* =========================================================
 * 4. ADMIN — settings page: toggle, count, CSV export.
 * ========================================================= */
add_action( 'admin_menu', function () {
	add_menu_page( 'Coming Soon', 'Coming Soon', 'manage_options', 'apcs-settings', 'apcs_settings_page', 'dashicons-megaphone', 31 );
} );

function apcs_settings_page() {
	if ( isset( $_POST['apcs_save'] ) && check_admin_referer( 'apcs_settings' ) ) {
		update_option( 'apcs_enabled', isset( $_POST['apcs_enabled'] ) ? '1' : '0' );
		update_option( 'apcs_omnisend_key', sanitize_text_field( wp_unslash( $_POST['apcs_omnisend_key'] ?? '' ) ) );
		echo '<div class="updated"><p>Settings saved.</p></div>';
	}
	$enabled = get_option( 'apcs_enabled', '1' );
	$count   = apcs_subscriber_count();
	$recent  = get_posts( array( 'post_type' => 'apcs_subscriber', 'posts_per_page' => 15, 'post_status' => 'any' ) );
	$export  = wp_nonce_url( admin_url( 'admin-post.php?action=apcs_export' ), 'apcs_export' );
	$preview = home_url( '/?apcs_preview=1' );
	?>
	<div class="wrap">
		<h1>Coming Soon</h1>
		<form method="post" style="background:#fff;border:1px solid #dcd5c4;border-radius:8px;padding:16px 20px;max-width:640px;">
			<?php wp_nonce_field( 'apcs_settings' ); ?>
			<p style="font-size:14px;">
				<label><input type="checkbox" name="apcs_enabled" value="1" <?php checked( $enabled, '1' ); ?>>
				<strong>Enable Coming Soon mode</strong></label>
			</p>
			<p class="description">When enabled, logged-out visitors see the coming-soon page. You (and any logged-in admin) still see the full store, so nothing is removed. Preview it anytime: <a href="<?php echo esc_url( $preview ); ?>" target="_blank">open coming-soon preview →</a></p>

			<hr style="margin:18px 0;border:none;border-top:1px solid #eee;">
			<?php $omni_key = get_option( 'apcs_omnisend_key', '' ); ?>
			<p style="font-size:14px;margin:0 0 4px;"><label><strong>Omnisend API key</strong> <?php if ( $omni_key ) : ?><span style="color:#2d6a4f;font-weight:600;">● Connected</span><?php endif; ?></label></p>
			<input type="text" name="apcs_omnisend_key" value="<?php echo esc_attr( $omni_key ); ?>" placeholder="paste your Omnisend API key" style="width:440px;max-width:100%;font-family:monospace;" autocomplete="off">
			<p class="description">Optional. When set, every new signup is also pushed to Omnisend (subscribed, tagged <code>waitlist</code>) — reliably, server-side. Get your key in <strong>Omnisend → Store settings → Integrations &amp; API → API keys</strong>. The WordPress list above always works regardless.</p>

			<p class="submit"><input type="submit" name="apcs_save" class="button-primary" value="Save"></p>
		</form>

		<h2 style="margin-top:28px;">Waitlist <span style="color:#2d6a4f;">(<?php echo (int) $count; ?>)</span></h2>
		<p><a href="<?php echo esc_url( $export ); ?>" class="button">⬇ Export all as CSV</a></p>
		<table class="widefat striped" style="max-width:760px;">
			<thead><tr><th>Email</th><th>Joined</th><th>Source</th><th>Omnisend</th></tr></thead>
			<tbody>
			<?php if ( $recent ) : foreach ( $recent as $s ) :
				$omni = get_post_meta( $s->ID, '_apcs_omnisend', true );
				$omni_label = $omni === 'synced' ? '✓ synced' : ( $omni ? '⚠ ' . $omni : '—' ); ?>
				<tr>
					<td><?php echo esc_html( $s->post_title ); ?></td>
					<td><?php echo esc_html( get_post_meta( $s->ID, '_apcs_date', true ) ); ?></td>
					<td><?php echo esc_html( get_post_meta( $s->ID, '_apcs_source', true ) ); ?></td>
					<td style="<?php echo $omni === 'synced' ? 'color:#2d6a4f' : ( $omni ? 'color:#b3261e' : 'color:#888' ); ?>"><?php echo esc_html( $omni_label ); ?></td>
				</tr>
			<?php endforeach; else : ?>
				<tr><td colspan="4">No sign-ups yet.</td></tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}

add_action( 'admin_post_apcs_export', function () {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'apcs_export' ) ) wp_die( 'Denied.' );
	$subs = get_posts( array( 'post_type' => 'apcs_subscriber', 'posts_per_page' => -1, 'post_status' => 'any', 'orderby' => 'date', 'order' => 'ASC' ) );
	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=anchored-peptides-waitlist-' . gmdate( 'Y-m-d' ) . '.csv' );
	$out = fopen( 'php://output', 'w' );
	fputcsv( $out, array( 'email', 'joined', 'source', 'ip' ) );
	foreach ( $subs as $s ) {
		fputcsv( $out, array(
			$s->post_title,
			get_post_meta( $s->ID, '_apcs_date', true ),
			get_post_meta( $s->ID, '_apcs_source', true ),
			get_post_meta( $s->ID, '_apcs_ip', true ),
		) );
	}
	fclose( $out );
	exit;
} );

/* Default ON when first activated. */
register_activation_hook( __FILE__, function () {
	if ( get_option( 'apcs_enabled', null ) === null ) add_option( 'apcs_enabled', '1' );
} );
