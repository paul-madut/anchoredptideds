<?php
/**
 * Anchored Peptides theme functions.
 *
 * @package AnchoredPeptides
 */
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'AP_VERSION', '1.0.0' );
define( 'AP_DIR', get_template_directory() );
define( 'AP_URI', get_template_directory_uri() );

/**
 * Shared Google Fonts URL — used by the theme AND the homepage plugin so the
 * type system is defined once. (Newsreader serif + Hanken Grotesk sans.)
 *
 * Per-site override: the `ap_fonts_url` option lets a generated brand swap the
 * whole type system at deploy time; the shipped default keeps AP unchanged.
 */
function ap_fonts_url() {
    $default = 'https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;0,6..72,600;1,6..72,400;1,6..72,600&display=swap';
    $url = get_option( 'ap_fonts_url', '' );
    return ( is_string( $url ) && '' !== $url && 0 === strpos( $url, 'https://' ) ) ? $url : $default;
}

function ap_setup() {
    load_theme_textdomain( 'anchored-peptides', AP_DIR . '/languages' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', array(
        'height'      => 34,
        'width'       => 180,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'woocommerce', array(
        'thumbnail_image_width' => 600,
        'single_image_width'    => 1000,
    ) );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    register_nav_menus( array(
        'primary' => esc_html__( 'Primary', 'anchored-peptides' ),
        'footer'  => esc_html__( 'Footer', 'anchored-peptides' ),
    ) );
}
add_action( 'after_setup_theme', 'ap_setup' );

function ap_scripts() {
    wp_enqueue_style( 'ap-fonts', ap_fonts_url(), array(), null );
    wp_enqueue_style( 'ap-tokens', AP_URI . '/assets/css/tokens.css', array(), AP_VERSION );
    wp_enqueue_style( 'ap-main', AP_URI . '/assets/css/main.css', array( 'ap-tokens' ), AP_VERSION );
    wp_enqueue_script( 'ap-main', AP_URI . '/assets/js/main.js', array( 'jquery' ), AP_VERSION, true );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'ap_scripts' );

require_once AP_DIR . '/inc/brand.php';
require_once AP_DIR . '/inc/woocommerce.php';
require_once AP_DIR . '/inc/meta-box.php';
require_once AP_DIR . '/inc/variations-generator.php';
require_once AP_DIR . '/inc/customizer.php';

/**
 * Render the brand logo. Mirrors the homepage plugin's ap_get_logo() so the
 * same WP option (ap_logo_id) controls the navbar, footer and homepage.
 */
function ap_render_logo( $height = 34 ) {
    $logo_id = get_option( 'ap_logo_id' );
    if ( $logo_id ) {
        $url = wp_get_attachment_image_url( $logo_id, 'medium' );
        if ( ! $url ) $url = wp_get_attachment_image_url( $logo_id, 'full' );
        if ( $url ) {
            return '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" style="height:' . intval( $height ) . 'px;width:auto;">';
        }
    }
    // Fallback: anchor mark + wordmark.
    $name = get_bloginfo( 'name' ) ?: 'Anchored Peptides';
    return '<span class="ap-logo">'
        . '<svg width="' . intval( $height ) . '" height="' . intval( $height ) . '" viewBox="0 0 40 40" fill="none" stroke="#3E412E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="20" cy="9" r="3.5"/><line x1="20" y1="12.5" x2="20" y2="31"/><line x1="13" y1="16" x2="27" y2="16"/><path d="M11 24c0 6 9 9.5 9 9.5s9-3.5 9-9.5"/></svg>'
        . '<span style="font-family:var(--ap-serif);font-weight:600;font-size:19px;color:var(--ap-ink);line-height:1;">' . esc_html( $name ) . '</span>'
        . '</span>';
}

/**
 * AJAX cart-count fragment so the navbar badge updates without reload.
 */
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
    $count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
    $fragments['span.ap-cart-count'] = '<span class="ap-cart-count">' . (int) $count . '</span>';
    return $fragments;
} );

/**
 * Helper: pull an Anchored Peptides product meta value with a fallback.
 *
 * Backward-compat: if an `_ap_*` key is empty, fall back to the matching
 * Natty Vision `_nv_*` key. This lets a WooCommerce product export from the
 * NV site import straight into Anchored Peptides and render correctly with
 * NO meta-key transformation. New AP products use `_ap_*` and take priority.
 */
function ap_meta( $product_id, $key, $default = '' ) {
    $val = get_post_meta( $product_id, $key, true );
    if ( $val === '' && strpos( $key, '_ap_' ) === 0 ) {
        $val = get_post_meta( $product_id, '_nv_' . substr( $key, 4 ), true );
    }
    return ( $val !== '' ) ? $val : $default;
}

/**
 * Render a star rating row from a 0–5 value.
 */
function ap_stars( $rating ) {
    $rating = max( 0, min( 5, (float) $rating ) );
    $full   = floor( $rating );
    $half   = ( $rating - $full ) >= 0.5;
    $out    = '';
    for ( $i = 0; $i < 5; $i++ ) {
        if ( $i < $full )        $out .= '★';
        elseif ( $i === (int) $full && $half ) $out .= '⯨';
        else                     $out .= '☆';
    }
    return $out;
}
