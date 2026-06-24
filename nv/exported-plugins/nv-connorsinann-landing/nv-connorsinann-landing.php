<?php
/**
 * Plugin Name: Natty Vision — Connor Sinann Guide
 * Plugin URI:  https://nattyvision.com
 * Description: Serves the Connor Sinann transformation guide page at /connorsinann, bypassing theme entirely.
 * Version:     1.0.0
 * Author:      Natty Vision
 * License:     GPL-2.0+
 * Text Domain: nv-connorsinann-landing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function nvcsg_add_rewrite_rule() {
    add_rewrite_rule(
        '^connorsinann/?$',
        'index.php?nvcsg_landing=1',
        'top'
    );
}
add_action( 'init', 'nvcsg_add_rewrite_rule' );

function nvcsg_add_query_var( $vars ) {
    $vars[] = 'nvcsg_landing';
    return $vars;
}
add_filter( 'query_vars', 'nvcsg_add_query_var' );

function nvcsg_serve_landing() {
    if ( (int) get_query_var( 'nvcsg_landing' ) !== 1 ) {
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
add_action( 'template_redirect', 'nvcsg_serve_landing' );

function nvcsg_activate() {
    nvcsg_add_rewrite_rule();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'nvcsg_activate' );

function nvcsg_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'nvcsg_deactivate' );
