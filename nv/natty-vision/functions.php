<?php
/**
 * Natty Vision functions
 * @package NattyVision
 */
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'NV_VERSION', '1.3.0' );
define( 'NV_DIR', get_template_directory() );
define( 'NV_URI', get_template_directory_uri() );

function natty_setup() {
    load_theme_textdomain( 'natty-vision', NV_DIR . '/languages' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', array(
        'height'      => 38,
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
        'primary' => esc_html__( 'Primary', 'natty-vision' ),
        'footer'  => esc_html__( 'Footer', 'natty-vision' ),
    ) );
}
add_action( 'after_setup_theme', 'natty_setup' );

function natty_scripts() {
    // Match homepage plugin font stack exactly.
    wp_enqueue_style(
        'natty-fonts-google',
        'https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Mono:wght@300;400;500&display=swap',
        array(), null
    );
    wp_enqueue_style(
        'natty-fonts-fontshare',
        'https://api.fontshare.com/v2/css?f[]=neue-montreal@400,500,700&display=swap',
        array(), null
    );
    wp_enqueue_style( 'natty-main', NV_URI . '/assets/css/main.css', array(), NV_VERSION );
    wp_enqueue_script( 'natty-main', NV_URI . '/assets/js/main.js', array( 'jquery' ), NV_VERSION, true );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'natty_scripts' );

require_once NV_DIR . '/inc/woocommerce.php';
require_once NV_DIR . '/inc/meta-box.php';
require_once NV_DIR . '/inc/variations-generator.php';
require_once NV_DIR . '/inc/customizer.php';

/**
 * Render the brand logo SVG. Mirrors the homepage plugin's `nv_get_logo()`
 * so the same WP option (`nv_logo_id`) controls both.
 */
function natty_render_logo( $height = 38 ) {
    $logo_id = get_option( 'nv_logo_id' );
    if ( $logo_id ) {
        $url = wp_get_attachment_image_url( $logo_id, 'medium' );
        if ( ! $url ) {
            $url = wp_get_attachment_image_url( $logo_id, 'full' );
        }
        if ( $url ) {
            return '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" style="height:' . intval( $height ) . 'px;width:auto;">';
        }
    }
    // Fallback SVG identical to the homepage plugin.
    return '<svg viewBox="0 0 180 36" xmlns="http://www.w3.org/2000/svg" style="height:' . intval( $height ) . 'px;width:auto"><g fill="#1a1e1c"><rect x="0" y="0" width="12" height="20"/><rect x="12" y="0" width="12" height="8"/><rect x="24" y="0" width="12" height="36"/><rect x="0" y="28" width="12" height="8"/><rect x="12" y="20" width="12" height="16"/></g><text x="44" y="25" font-family="Neue Montreal,Helvetica,sans-serif" font-size="21" font-weight="500" fill="#1a1e1c" letter-spacing="-0.02em">' . esc_html( get_bloginfo( 'name' ) ) . '</text></svg>';
}

/**
 * Default fallback nav matching homepage plugin.
 */
function natty_default_menu() {
    $shop_url = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
    ?>
    <div class="nv-nc">
        <a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Product', 'natty-vision' ); ?></a>
        <a href="#"><?php esc_html_e( 'Pricing', 'natty-vision' ); ?></a>
        <a href="#"><?php esc_html_e( 'Company', 'natty-vision' ); ?></a>
        <a href="#"><?php esc_html_e( 'Blog', 'natty-vision' ); ?></a>
        <a href="#"><?php esc_html_e( 'Changelog', 'natty-vision' ); ?></a>
    </div>
    <?php
}

/**
 * AJAX cart count fragment.
 */
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
    $count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    $fragments['span.nv-cart-count'] = '<span class="nv-cart-count">' . (int) $count . '</span>';
    return $fragments;
} );

/**
 * Helper: pull a Natty Vision meta value with sensible fallback.
 */
function nv_meta( $product_id, $key, $default = '' ) {
    $val = get_post_meta( $product_id, $key, true );
    return ( $val !== '' ) ? $val : $default;
}

/**
 * Open Graph / Twitter Card tags are emitted by AIOSEO (the active SEO plugin),
 * so the theme no longer outputs its own — that was causing duplicate og tags.
 * Jetpack's Open Graph is disabled here for the same reason.
 */
add_filter( 'jetpack_enable_open_graph', '__return_false' );

/**
 * Keep link previews to a single image: stop emitting the site logo as the
 * apple-touch-icon / tile image so iMessage (and others) don't pair the logo
 * beside the product og:image. The browser favicon (rel="icon") is kept.
 */
add_filter( 'site_icon_meta_tags', function ( $tags ) {
    return array_values( array_filter( $tags, function ( $tag ) {
        return strpos( $tag, 'apple-touch-icon' ) === false
            && strpos( $tag, 'msapplication-TileImage' ) === false;
    } ) );
} );
