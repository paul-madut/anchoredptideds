<?php
/**
 * Anchored Peptides — Shop archive with sidebar category filtering.
 * @package AnchoredPeptides
 */
defined( 'ABSPATH' ) || exit;
get_header( 'shop' );

// Preferred category display order (Natty Vision scheme). Others appended after.
$ap_category_order = array( 'Weight Loss', 'Energy', 'Healing', 'Skin', 'Brain', 'Stacks', 'Supplies' );
$ap_exclude        = array( 'peptides', 'uncategorized' );

$all_cats = get_terms( array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'exclude'    => array( get_option( 'default_product_cat' ) ),
) );

$ordered = array();
foreach ( $ap_category_order as $name ) {
    foreach ( $all_cats as $cat ) {
        if ( strtolower( $cat->name ) === strtolower( $name ) ) { $ordered[] = $cat; break; }
    }
}
$listed = wp_list_pluck( $ordered, 'term_id' );
foreach ( $all_cats as $cat ) {
    if ( in_array( strtolower( $cat->name ), $ap_exclude, true ) ) continue;
    if ( in_array( $cat->term_id, $listed, true ) ) continue;
    $ordered[] = $cat; $listed[] = $cat->term_id;
}

// In-stock-first ordering.
$instock_first = function ( $products ) {
    $in = array(); $out = array();
    foreach ( $products as $p ) { ( $p->get_stock_status() === 'outofstock' ) ? $out[] = $p : $in[] = $p; }
    return array_merge( $in, $out );
};

$cat_products = array();
foreach ( $ordered as $cat ) {
    $cat_products[ $cat->term_id ] = $instock_first( wc_get_products( array(
        'status' => 'publish', 'limit' => -1, 'category' => array( $cat->slug ), 'orderby' => 'menu_order', 'order' => 'ASC',
    ) ) );
}
$all_map = array();
foreach ( $cat_products as $prods ) { foreach ( $prods as $p ) $all_map[ $p->get_id() ] = $p; }
$all_flat  = $instock_first( array_values( $all_map ) );
$total     = count( $all_flat );
$coa_page  = get_page_by_path( 'coa-library' );
$coa_url   = $coa_page ? get_permalink( $coa_page ) : home_url( '/coa-library/' );
?>
<main class="ap-shop">

    <p class="ap-breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'anchored-peptides' ); ?></a> / <?php esc_html_e( 'Shop', 'anchored-peptides' ); ?></p>

    <div class="ap-shop-head">
        <div>
            <h1><?php esc_html_e( 'All Peptides', 'anchored-peptides' ); ?></h1>
            <p><?php printf( esc_html__( '%d research compounds · lab-tested, ships from Canada', 'anchored-peptides' ), (int) $total ); ?></p>
        </div>
    </div>

    <div class="ap-shop-layout">

        <aside class="ap-shop-side">
            <div class="ap-filter-card">
                <h4><?php esc_html_e( 'Category', 'anchored-peptides' ); ?></h4>
                <div class="ap-filter-list" id="ap-shop-filters">
                    <button class="active" data-cat="all"><?php esc_html_e( 'All Products', 'anchored-peptides' ); ?> <span><?php echo (int) $total; ?></span></button>
                    <?php foreach ( $ordered as $cat ) : if ( empty( $cat_products[ $cat->term_id ] ) ) continue; ?>
                        <button data-cat="<?php echo esc_attr( $cat->slug ); ?>"><?php echo esc_html( $cat->name ); ?> <span><?php echo (int) count( $cat_products[ $cat->term_id ] ); ?></span></button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="ap-coa-promo">
                <b><?php esc_html_e( 'Every batch, verified', 'anchored-peptides' ); ?></b>
                <?php esc_html_e( 'Pull the exact certificate of analysis printed on your vial.', 'anchored-peptides' ); ?>
                <a href="<?php echo esc_url( $coa_url ); ?>"><?php esc_html_e( 'Look up a COA →', 'anchored-peptides' ); ?></a>
            </div>
        </aside>

        <div id="ap-shop-sections">
            <!-- All products -->
            <section class="ap-cat-section" data-cat-slug="all">
                <div class="ap-prod-grid">
                    <?php foreach ( $all_flat as $product ) ap_render_product_card( $product ); ?>
                </div>
            </section>
            <!-- Per category -->
            <?php foreach ( $ordered as $cat ) :
                if ( empty( $cat_products[ $cat->term_id ] ) ) continue; ?>
                <section class="ap-cat-section" data-cat-slug="<?php echo esc_attr( $cat->slug ); ?>" style="display:none;">
                    <div class="ap-prod-grid">
                        <?php foreach ( $cat_products[ $cat->term_id ] as $product ) ap_render_product_card( $product, $cat->name ); ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>

    </div>
</main>

<script>
(function () {
    var btns = document.querySelectorAll('#ap-shop-filters button');
    var sections = document.querySelectorAll('.ap-cat-section');
    function show(cat) {
        btns.forEach(function (b) { b.classList.toggle('active', b.dataset.cat === cat); });
        sections.forEach(function (s) { s.style.display = (s.getAttribute('data-cat-slug') === cat) ? '' : 'none'; });
    }
    btns.forEach(function (b) { b.addEventListener('click', function () { show(b.dataset.cat); }); });
    var p = new URLSearchParams(location.search).get('category');
    if (p && document.querySelector('#ap-shop-filters button[data-cat="' + p + '"]')) show(p);
})();
</script>

<?php get_footer( 'shop' );
