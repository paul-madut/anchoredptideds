<?php
/**
 * Template Name: COA Library
 *
 * Auto-lists every published product with a lab verify URL (_ap_coa_url) set.
 * "View COA" links off-site to the independent lab.
 *
 * @package AnchoredPeptides
 */
defined( 'ABSPATH' ) || exit;
get_header();

// Match products with a COA URL under either the AP key or the migrated NV key.
$q = new WP_Query( array(
    'post_type'      => 'product',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'title',
    'order'          => 'ASC',
    'meta_query'     => array(
        'relation' => 'OR',
        array( 'key' => '_ap_coa_url', 'value' => '', 'compare' => '!=' ),
        array( 'key' => '_nv_coa_url', 'value' => '', 'compare' => '!=' ),
    ),
) );

// Read a COA field with _ap_ → _nv_ fallback.
$ap_coa = function ( $pid, $field ) {
    $v = get_post_meta( $pid, '_ap_' . $field, true );
    return ( $v !== '' ) ? $v : get_post_meta( $pid, '_nv_' . $field, true );
};

$cats = array();
if ( $q->have_posts() ) {
    foreach ( $q->posts as $p ) {
        $terms = get_the_terms( $p->ID, 'product_cat' );
        if ( $terms && ! is_wp_error( $terms ) ) foreach ( $terms as $t ) $cats[ $t->slug ] = $t->name;
    }
    asort( $cats );
}
?>
<main>
    <header class="ap-page-hero">
        <p class="ap-eyebrow"><?php esc_html_e( 'Certificate of Analysis', 'anchored-peptides' ); ?></p>
        <h1>COA <em><?php esc_html_e( 'Library', 'anchored-peptides' ); ?></em></h1>
        <p><?php esc_html_e( 'Third-party HPLC lab results for every batch we ship. Search by compound or lot — every report, in the open.', 'anchored-peptides' ); ?></p>
    </header>

    <div class="ap-container">
        <div class="ap-coa-controls">
            <div class="ap-coa-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <input type="text" id="ap-coa-search" placeholder="<?php esc_attr_e( 'Search by product or lot number…', 'anchored-peptides' ); ?>">
            </div>
            <?php if ( ! empty( $cats ) ) : ?>
            <div class="ap-coa-pills">
                <button class="ap-coa-pill active" data-cat="all"><?php esc_html_e( 'All', 'anchored-peptides' ); ?></button>
                <?php foreach ( $cats as $slug => $name ) : ?>
                    <button class="ap-coa-pill" data-cat="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $name ); ?></button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="ap-coa-grid" id="ap-coa-grid">
            <?php if ( $q->have_posts() ) : while ( $q->have_posts() ) : $q->the_post();
                $pid    = get_the_ID();
                $lot    = $ap_coa( $pid, 'coa_lot' );
                $purity = $ap_coa( $pid, 'coa_purity' );
                $tested = $ap_coa( $pid, 'coa_tested' );
                $url    = $ap_coa( $pid, 'coa_url' );
                $terms  = get_the_terms( $pid, 'product_cat' );
                $slugs  = ( $terms && ! is_wp_error( $terms ) ) ? implode( ' ', wp_list_pluck( $terms, 'slug' ) ) : '';
                ?>
                <div class="ap-coa-card" data-cats="<?php echo esc_attr( $slugs ); ?>" data-name="<?php echo esc_attr( strtolower( get_the_title() . ' ' . $lot ) ); ?>">
                    <h3><?php the_title(); ?></h3>
                    <div class="ap-coa-meta">
                        <?php if ( $lot ) : ?><span><?php esc_html_e( 'Lot', 'anchored-peptides' ); ?>: <?php echo esc_html( $lot ); ?></span><?php endif; ?>
                        <?php if ( $purity ) : ?><span><?php esc_html_e( 'Purity', 'anchored-peptides' ); ?>: <?php echo esc_html( $purity ); ?>%</span><?php endif; ?>
                        <?php if ( $tested ) : ?><span><?php esc_html_e( 'Tested', 'anchored-peptides' ); ?>: <?php echo esc_html( $tested ); ?></span><?php endif; ?>
                    </div>
                    <a class="ap-btn-outline" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener nofollow"><?php esc_html_e( 'View COA →', 'anchored-peptides' ); ?></a>
                </div>
            <?php endwhile; wp_reset_postdata(); else : ?>
                <p><?php esc_html_e( 'No certificates published yet.', 'anchored-peptides' ); ?></p>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
(function () {
    var search = document.getElementById('ap-coa-search');
    var pills  = document.querySelectorAll('.ap-coa-pill');
    var cards  = document.querySelectorAll('.ap-coa-card');
    var cat = 'all', term = '';
    function apply() {
        cards.forEach(function (c) {
            var okCat  = cat === 'all' || (' ' + c.dataset.cats + ' ').indexOf(' ' + cat + ' ') > -1;
            var okTerm = !term || c.dataset.name.indexOf(term) > -1;
            c.style.display = (okCat && okTerm) ? '' : 'none';
        });
    }
    pills.forEach(function (p) { p.addEventListener('click', function () { pills.forEach(function (x) { x.classList.remove('active'); }); p.classList.add('active'); cat = p.dataset.cat; apply(); }); });
    if (search) search.addEventListener('input', function () { term = search.value.toLowerCase().trim(); apply(); });
})();
</script>
<?php get_footer();
