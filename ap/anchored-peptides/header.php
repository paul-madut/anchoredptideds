<?php
/**
 * Anchored Peptides header — announcement marquee + sticky nav.
 * @package AnchoredPeptides
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$ap_shop_url = ( function_exists( 'wc_get_page_permalink' ) ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
$ap_cart_url = ( function_exists( 'wc_get_cart_url' ) ) ? wc_get_cart_url() : home_url( '/cart/' );
$ap_acct_url = ( function_exists( 'wc_get_page_permalink' ) ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' );

// Marquee phrases — overridable in Customizer (one per line).
$ap_marquee_raw = get_theme_mod( 'ap_marquee', "Batch before 2 PM\nTrusted by 20,000+ researchers\nFor research use only\nShips from Canada\nThird-party HPLC tested" );
$ap_marquee = array_filter( array_map( 'trim', explode( "\n", $ap_marquee_raw ) ) );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php if ( get_theme_mod( 'ap_announce_enabled', true ) && ! empty( $ap_marquee ) ) : ?>
<div class="ap-announce" aria-hidden="true">
    <div class="ap-announce-track">
        <?php // Print twice for a seamless -50% loop.
        for ( $i = 0; $i < 2; $i++ ) :
            foreach ( $ap_marquee as $phrase ) : ?>
                <span class="ap-announce-item"><?php echo esc_html( $phrase ); ?></span>
            <?php endforeach;
        endfor; ?>
    </div>
</div>
<?php endif; ?>

<header class="ap-nav">
    <div class="ap-nav-i">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
            <?php echo ap_render_logo( 34 ); ?>
        </a>

        <nav class="ap-nav-links" aria-label="<?php esc_attr_e( 'Primary', 'anchored-peptides' ); ?>">
            <?php
            if ( has_nav_menu( 'primary' ) ) {
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'items_wrap'     => '%3$s',
                    'walker'         => new AP_Nav_Walker(),
                    'fallback_cb'    => 'ap_default_menu',
                ) );
            } else {
                ap_default_menu();
            }
            ?>
        </nav>

        <div class="ap-nav-right">
            <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                <a href="<?php echo esc_url( $ap_acct_url ); ?>" aria-label="<?php esc_attr_e( 'Account', 'anchored-peptides' ); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                </a>
                <a href="<?php echo esc_url( $ap_cart_url ); ?>" class="ap-cart" aria-label="<?php esc_attr_e( 'Cart', 'anchored-peptides' ); ?>">
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    <span class="ap-cart-count"><?php echo (int) ( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
                </a>
            <?php endif; ?>
            <button class="ap-burger" aria-label="<?php esc_attr_e( 'Menu', 'anchored-peptides' ); ?>" onclick="document.querySelector('.ap-nav-links').classList.toggle('open')">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>
<?php
/**
 * Default nav when no menu is assigned.
 */
function ap_default_menu() {
    $shop = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
    ?>
    <a href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Shop', 'anchored-peptides' ); ?></a>
    <a href="<?php echo esc_url( home_url( '/learn/' ) ); ?>"><?php esc_html_e( 'Learn', 'anchored-peptides' ); ?></a>
    <a href="<?php echo esc_url( home_url( '/coa-library/' ) ); ?>"><?php esc_html_e( 'COAs & Testing', 'anchored-peptides' ); ?></a>
    <?php
}

/**
 * Walker — outputs simple <a> children matching .ap-nav-links markup.
 */
class AP_Nav_Walker extends Walker_Nav_Menu {
    public function start_lvl( &$output, $depth = 0, $args = null ) {}
    public function end_lvl( &$output, $depth = 0, $args = null ) {}
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $cls = in_array( 'current-menu-item', (array) $item->classes, true ) ? ' class="active"' : '';
        $output .= '<a href="' . esc_url( $item->url ) . '"' . $cls . '>' . esc_html( $item->title ) . '</a>';
    }
    public function end_el( &$output, $item, $depth = 0, $args = null ) {}
}
