<?php
/**
 * Natty Vision WooCommerce hooks.
 *
 * @package NattyVision
 */
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'WooCommerce' ) ) return;

remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

add_action( 'after_setup_theme', function () {
    remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
    remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
    remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
}, 11 );

add_action( 'woocommerce_before_main_content', function () {
    echo '<main class="nv-shop-main"><div class="nv-container">';
}, 10 );
add_action( 'woocommerce_after_main_content', function () {
    echo '</div></main>';
}, 10 );

/**
 * Nuke social share / like buttons.
 */
add_action( 'init', function () {
    // Jetpack
    remove_filter( 'the_content', 'sharing_display', 19 );
    remove_filter( 'the_excerpt', 'sharing_display', 19 );
    if ( class_exists( 'Jetpack_Likes' ) ) {
        remove_filter( 'the_content', array( Jetpack_Likes::init(), 'post_likes' ), 30 );
    }
    // WooCommerce native share template
    remove_action( 'woocommerce_share', 'woocommerce_share', 10 );
}, 99 );

/**
 * Late-stage scrub: strip share markup from rendered content
 * in case some plugin re-injected after our priority-99 removal.
 */
add_filter( 'the_content', function ( $content ) {
    if ( ! is_product() && ! is_singular( 'product' ) ) {
        return $content;
    }
    $content = preg_replace( '/<div[^>]*class="[^"]*sharedaddy[^"]*"[^>]*>.*?<\/div>/is', '', $content );
    $content = preg_replace( '/<div[^>]*class="[^"]*sd-sharing[^"]*"[^>]*>.*?<\/div>/is', '', $content );
    $content = preg_replace( '/<div[^>]*class="[^"]*jetpack-likes[^"]*"[^>]*>.*?<\/div>/is', '', $content );
    $content = preg_replace( '/<div[^>]*class="[^"]*jp-likes[^"]*"[^>]*>.*?<\/div>/is', '', $content );
    return $content;
}, 999 );

/**
 * Remove default single-product summary actions — we re-render manually.
 */
add_action( 'wp', function () {
    if ( is_product() ) {
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
    }
} );

/**
 * For variable products: suppress WooCommerce's global availability text
 * since we display per-variant stock via pills instead.
 */
add_filter( 'woocommerce_get_availability_text', function ( $availability, $product ) {
    if ( $product->is_type( 'variable' ) ) {
        return '';
    }
    return $availability;
}, 10, 2 );
