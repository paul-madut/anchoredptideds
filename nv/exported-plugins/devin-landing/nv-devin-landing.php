<?php
/**
 * Plugin Name: Natty Vision — Devin Ascension Guide
 * Plugin URI:  https://nattyvision.com
 * Description: Serves the Devin ascension protocol guide page at /devin, bypassing theme entirely.
 * Version:     1.0.0
 * Author:      Natty Vision
 * License:     GPL-2.0+
 * Text Domain: nv-devin-landing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the custom rewrite rule: /devin → our handler.
 */
function nvdl_add_rewrite_rule() {
    add_rewrite_rule(
        '^devin/?$',
        'index.php?nvdl_landing=1',
        'top'
    );
}
add_action( 'init', 'nvdl_add_rewrite_rule' );

/**
 * Register the query var.
 */
function nvdl_add_query_var( $vars ) {
    $vars[] = 'nvdl_landing';
    return $vars;
}
add_filter( 'query_vars', 'nvdl_add_query_var' );

/**
 * Serve the raw HTML and exit before WordPress loads theme chrome.
 */
function nvdl_serve_landing() {
    if ( (int) get_query_var( 'nvdl_landing' ) !== 1 ) {
        return;
    }

    $file = plugin_dir_path( __FILE__ ) . 'landing.html';

    if ( ! file_exists( $file ) ) {
        status_header( 404 );
        wp_die( 'Landing page file not found.' );
    }

    status_header( 200 );
    nocache_headers();
    header( 'Content-Type: text/html; charset=UTF-8' );

    readfile( $file );
    exit;
}
add_action( 'template_redirect', 'nvdl_serve_landing' );

/**
 * Flush rewrite rules on activation so /devin works immediately.
 */
function nvdl_activate() {
    nvdl_add_rewrite_rule();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'nvdl_activate' );

/**
 * Clean up rewrite rules on deactivation.
 */
function nvdl_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'nvdl_deactivate' );
