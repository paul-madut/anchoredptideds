<?php
/**
 * Anchored Peptides WooCommerce hooks.
 * @package AnchoredPeptides
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
    echo '<main class="ap-shop-main"><div class="ap-container">';
}, 10 );
add_action( 'woocommerce_after_main_content', function () {
    echo '</div></main>';
}, 10 );

/**
 * Remove default single-product summary actions — the template re-renders them.
 */
add_action( 'wp', function () {
    if ( function_exists( 'is_product' ) && is_product() ) {
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
 * For variable products: suppress WooCommerce's global availability text since
 * per-variant stock is shown via the strength pills.
 */
add_filter( 'woocommerce_get_availability_text', function ( $availability, $product ) {
    if ( $product->is_type( 'variable' ) ) return '';
    return $availability;
}, 10, 2 );

/**
 * Strip social-share markup on product pages (in case a plugin injects it).
 */
add_filter( 'the_content', function ( $content ) {
    if ( ! function_exists( 'is_product' ) || ! is_product() ) return $content;
    $content = preg_replace( '/<div[^>]*class="[^"]*sharedaddy[^"]*"[^>]*>.*?<\/div>/is', '', $content );
    return $content;
}, 999 );

/**
 * Canonical product card — reused by the shop grid, homepage best-sellers,
 * and "you may also like". Keeps the card markup defined once.
 */
function ap_render_product_card( $product, $eyebrow_override = '' ) {
    if ( is_numeric( $product ) ) $product = wc_get_product( $product );
    if ( ! $product ) return;

    $pid       = $product->get_id();
    $permalink = get_permalink( $pid );
    $badge     = ap_meta( $pid, '_ap_badge' ); // ap_meta() falls back to _nv_ for migrated catalogs
    $thumb_id  = get_post_thumbnail_id( $pid );
    $thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';

    // Eyebrow = override → _ap_eyebrow (or _nv_eyebrow) → first category.
    $eyebrow = $eyebrow_override;
    if ( ! $eyebrow ) $eyebrow = ap_meta( $pid, '_ap_eyebrow' );
    if ( ! $eyebrow ) {
        $terms   = wp_get_post_terms( $pid, 'product_cat', array( 'fields' => 'names' ) );
        $terms   = array_filter( $terms, function ( $n ) { return ! in_array( strtolower( $n ), array( 'peptides', 'uncategorized' ), true ); } );
        $eyebrow = ! empty( $terms ) ? reset( $terms ) : '';
    }

    $badge_class = 'ap-prod-badge';
    if ( $product->is_on_sale() && ! $badge ) { $badge = __( 'Sale', 'anchored-peptides' ); }
    if ( strtolower( (string) $badge ) === 'sale' ) $badge_class .= ' sale';
    elseif ( strtolower( (string) $badge ) === 'new' ) $badge_class .= ' new';

    $rating = (float) $product->get_average_rating();
    $rcount = (int) $product->get_review_count();
    ?>
    <a href="<?php echo esc_url( $permalink ); ?>" class="ap-prod-card">
        <div class="ap-prod-thumb">
            <?php if ( $thumb_url ) : ?>
                <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" loading="lazy">
            <?php else : ?>
                <svg viewBox="0 0 100 130" width="64" aria-hidden="true"><rect x="35" y="0" width="30" height="14" rx="2" fill="#3E412E"/><rect x="32" y="14" width="36" height="6" rx="1" fill="#3E412E"/><rect x="20" y="20" width="60" height="105" rx="6" fill="#C9C1AC"/></svg>
            <?php endif; ?>
            <?php if ( $badge ) : ?><span class="<?php echo esc_attr( $badge_class ); ?>"><?php echo esc_html( $badge ); ?></span><?php endif; ?>
        </div>
        <div class="ap-prod-info">
            <?php if ( $eyebrow ) : ?><span class="ap-prod-cat"><?php echo esc_html( $eyebrow ); ?></span><?php endif; ?>
            <span class="ap-prod-name"><?php echo esc_html( $product->get_name() ); ?></span>
            <?php if ( $rcount > 0 ) : ?>
                <span class="ap-prod-rating"><span class="ap-stars"><?php echo esc_html( ap_stars( $rating ) ); ?></span> <?php echo esc_html( $rcount ); ?></span>
            <?php endif; ?>
            <span class="ap-prod-foot"><span class="ap-prod-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span></span>
        </div>
    </a>
    <?php
}
