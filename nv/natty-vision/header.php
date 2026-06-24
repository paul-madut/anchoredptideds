<?php
/**
 * Natty Vision header — matches homepage plugin nav exactly.
 * @package NattyVision
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php if ( get_theme_mod( 'nv_announce_enabled', true ) ) : ?>
    <div class="nv-announce">
        <strong>10% off</strong> all orders with code <strong>NATTY</strong> at checkout
    </div>
<?php endif; ?>

<nav class="nv-nav-bar">
    <div class="nv-nav-i">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nv-nl" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
            <?php echo natty_render_logo( 38 ); ?>
        </a>

        <div class="nv-nr-group">
            <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="nv-nr-cart" aria-label="<?php esc_attr_e( 'Cart', 'natty-vision' ); ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                        <path d="M3 6h18"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                    <span class="nv-cart-count"><?php echo (int) ( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
                </a>
                <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="nv-nr"><?php echo is_user_logged_in() ? 'My Account' : 'Login'; ?></a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<?php
/**
 * Walker stub — outputs simple <a> children matching homepage `.nc a` markup.
 */
class Natty_Nav_Walker extends Walker_Nav_Menu {
    public function start_lvl( &$output, $depth = 0, $args = null ) {}
    public function end_lvl( &$output, $depth = 0, $args = null ) {}
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $cls = in_array( 'current-menu-item', $item->classes, true ) ? ' on' : '';
        $output .= '<a href="' . esc_url( $item->url ) . '" class="' . esc_attr( trim( $cls ) ) . '">' . esc_html( $item->title ) . '</a>';
    }
    public function end_el( &$output, $item, $depth = 0, $args = null ) {}
}
