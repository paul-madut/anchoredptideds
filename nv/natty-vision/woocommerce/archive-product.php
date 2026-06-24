<?php
/**
 * Natty Vision — Shop archive with category filtering.
 *
 * Groups products by WooCommerce category with pill-style filter tabs.
 *
 * @package NattyVision
 */
defined( 'ABSPATH' ) || exit;
get_header( 'shop' );

// Define the category display order (matches homepage).
$category_order = array(
    'Weight Loss',
    'Energy',
    'Healing',
    'Skin',
    'Brain',
    'Stacks',
);

// Categories to exclude from pills (parent/catch-all).
$exclude_names = array( 'peptides', 'uncategorized' );

// Get all product categories that have products.
$all_cats = get_terms( array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'exclude'    => array( get_option( 'default_product_cat' ) ),
) );

// Build ordered list: predefined order first.
$ordered_cats = array();
foreach ( $category_order as $name ) {
    foreach ( $all_cats as $cat ) {
        if ( strtolower( $cat->name ) === strtolower( $name ) ) {
            $ordered_cats[] = $cat;
            break;
        }
    }
}

// Then append any other categories that have products and aren't excluded
// (e.g. a new "Supplies" category), so they show as pills and in the All grid.
$listed_ids = wp_list_pluck( $ordered_cats, 'term_id' );
foreach ( $all_cats as $cat ) {
    if ( in_array( strtolower( $cat->name ), $exclude_names, true ) ) continue;
    if ( in_array( $cat->term_id, $listed_ids, true ) ) continue;
    $ordered_cats[] = $cat;
    $listed_ids[]   = $cat->term_id;
}

// Build products-per-category map.
// Stable sort: in-stock products first (preserving order), out-of-stock last.
$nv_instock_first = function ( $products ) {
    $in = array(); $out = array();
    foreach ( $products as $p ) {
        if ( $p->get_stock_status() === 'outofstock' ) { $out[] = $p; } else { $in[] = $p; }
    }
    return array_merge( $in, $out );
};

$cat_products = array();
foreach ( $ordered_cats as $cat ) {
    $args = array(
        'status'   => 'publish',
        'limit'    => -1,
        'category' => array( $cat->slug ),
        'orderby'  => 'menu_order',
        'order'    => 'ASC',
    );
    $cat_products[ $cat->term_id ] = $nv_instock_first( wc_get_products( $args ) );
}

// Build flat "all products" list (no duplicates) for the All view.
$all_products_map = array();
foreach ( $cat_products as $prods ) {
    foreach ( $prods as $p ) {
        $all_products_map[ $p->get_id() ] = $p;
    }
}
$all_products_flat = $nv_instock_first( array_values( $all_products_map ) );
?>

<main class="nv-shop-main">
    <div class="nv-container">

        <h1 class="nv-page-title">Shop</h1>

        <div class="nv-shop-pills" id="nv-shop-pills">
            <button class="nv-shop-pill active" data-cat="all">All</button>
            <?php foreach ( $ordered_cats as $cat ) : ?>
                <button class="nv-shop-pill" data-cat="<?php echo esc_attr( $cat->slug ); ?>">
                    <?php echo esc_html( $cat->name ); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div id="nv-shop-sections">

            <!-- All products flat grid (no duplicates, no category grouping) -->
            <section class="nv-cat-section nv-all-section" data-cat-slug="all">
                <div class="nv-cat-grid">
                    <?php foreach ( $all_products_flat as $product ) :
                        $pid        = $product->get_id();
                        $permalink  = get_permalink( $pid );
                        $badge      = get_post_meta( $pid, '_nv_badge', true );
                        $thumb_id   = get_post_thumbnail_id( $pid );
                        $thumb_url  = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';

                        $price_html = $product->get_price_html();

                        // Get first category name for eyebrow.
                        $prod_cats   = wp_get_post_terms( $pid, 'product_cat', array( 'fields' => 'names' ) );
                        $prod_cats   = array_filter( $prod_cats, function( $n ) use ( $exclude_names ) {
                            return ! in_array( strtolower( $n ), $exclude_names, true );
                        });
                        $eyebrow_cat = ! empty( $prod_cats ) ? reset( $prod_cats ) : '';

                        $stock_status = $product->get_stock_status();
                        $stock_qty    = $product->get_stock_quantity();
                        if ( $stock_status === 'outofstock' ) {
                            $stock_text  = 'Out of stock';
                            $stock_class = 'nv-stock-out';
                        } elseif ( $stock_qty !== null && $stock_qty > 0 && $stock_qty <= 10 ) {
                            $stock_text  = $stock_qty . ' vials left';
                            $stock_class = 'nv-stock-low';
                        } else {
                            $stock_text  = 'In stock';
                            $stock_class = 'nv-stock-in';
                        }
                        ?>
                        <a href="<?php echo esc_url( $permalink ); ?>" class="nv-cat-card">
                            <div class="nv-cat-card-thumb">
                                <?php if ( $thumb_url ) : ?>
                                    <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" loading="lazy" />
                                <?php else : ?>
                                    <div class="nv-cat-card-placeholder">
                                        <svg viewBox="0 0 100 130"><rect x="35" y="0" width="30" height="14" rx="2" fill="currentColor"/><rect x="32" y="14" width="36" height="6" rx="1" fill="currentColor"/><rect x="20" y="20" width="60" height="105" rx="6" fill="currentColor"/></svg>
                                    </div>
                                <?php endif; ?>
                                <?php if ( $badge ) : ?>
                                    <span class="nv-cat-card-badge"><?php echo esc_html( $badge ); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="nv-cat-card-info">
                                <?php if ( $eyebrow_cat ) : ?>
                                    <span class="nv-cat-card-eyebrow"><?php echo esc_html( $eyebrow_cat ); ?></span>
                                <?php endif; ?>
                                <h3 class="nv-cat-card-title"><?php echo esc_html( $product->get_name() ); ?></h3>
                                <div class="nv-cat-card-bottom">
                                    <span class="nv-cat-card-price"><?php echo $price_html; ?></span>
                                    <span class="nv-cat-card-stock <?php echo esc_attr( $stock_class ); ?>"><?php echo esc_html( $stock_text ); ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Category sections -->
            <?php foreach ( $ordered_cats as $cat ) :
                $products = $cat_products[ $cat->term_id ];
                if ( empty( $products ) ) continue;
                ?>
                <section class="nv-cat-section" data-cat-slug="<?php echo esc_attr( $cat->slug ); ?>" style="display:none;">

                    <div class="nv-cat-header">
                        <span class="nv-cat-label"><?php echo esc_html( $cat->name ); ?></span>
                    </div>

                    <div class="nv-cat-grid">
                        <?php foreach ( $products as $product ) :
                            $pid        = $product->get_id();
                            $permalink  = get_permalink( $pid );
                            $badge      = get_post_meta( $pid, '_nv_badge', true );
                            $thumb_id   = get_post_thumbnail_id( $pid );
                            $thumb_url  = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';

                            $price_html = $product->get_price_html();

                            $stock_status = $product->get_stock_status();
                            $stock_qty    = $product->get_stock_quantity();
                            if ( $stock_status === 'outofstock' ) {
                                $stock_text  = 'Out of stock';
                                $stock_class = 'nv-stock-out';
                            } elseif ( $stock_qty !== null && $stock_qty > 0 && $stock_qty <= 10 ) {
                                $stock_text  = $stock_qty . ' vials left';
                                $stock_class = 'nv-stock-low';
                            } else {
                                $stock_text  = 'In stock';
                                $stock_class = 'nv-stock-in';
                            }
                            ?>
                            <a href="<?php echo esc_url( $permalink ); ?>" class="nv-cat-card">
                                <div class="nv-cat-card-thumb">
                                    <?php if ( $thumb_url ) : ?>
                                        <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" loading="lazy" />
                                    <?php else : ?>
                                        <div class="nv-cat-card-placeholder">
                                            <svg viewBox="0 0 100 130"><rect x="35" y="0" width="30" height="14" rx="2" fill="currentColor"/><rect x="32" y="14" width="36" height="6" rx="1" fill="currentColor"/><rect x="20" y="20" width="60" height="105" rx="6" fill="currentColor"/></svg>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ( $badge ) : ?>
                                        <span class="nv-cat-card-badge"><?php echo esc_html( $badge ); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="nv-cat-card-info">
                                    <span class="nv-cat-card-eyebrow"><?php echo esc_html( $cat->name ); ?></span>
                                    <h3 class="nv-cat-card-title"><?php echo esc_html( $product->get_name() ); ?></h3>
                                    <div class="nv-cat-card-bottom">
                                        <span class="nv-cat-card-price"><?php echo $price_html; ?></span>
                                        <span class="nv-cat-card-stock <?php echo esc_attr( $stock_class ); ?>"><?php echo esc_html( $stock_text ); ?></span>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>

                </section>
            <?php endforeach; ?>
        </div>

    </div>
</main>

<script>
(function () {
    var pills = document.querySelectorAll('.nv-shop-pill');
    var sections = document.querySelectorAll('.nv-cat-section');
    var allSection = document.querySelector('.nv-all-section');

    pills.forEach(function (pill) {
        pill.addEventListener('click', function () {
            pills.forEach(function (p) { p.classList.remove('active'); });
            pill.classList.add('active');
            var cat = pill.getAttribute('data-cat');

            sections.forEach(function (section) {
                var slug = section.getAttribute('data-cat-slug');
                if (cat === 'all') {
                    section.style.display = slug === 'all' ? '' : 'none';
                } else {
                    section.style.display = slug === cat ? '' : 'none';
                }
            });
        });
    });

    var params = new URLSearchParams(window.location.search);
    var catParam = params.get('category');
    if (catParam) {
        var targetPill = document.querySelector('.nv-shop-pill[data-cat="' + catParam + '"]');
        if (targetPill) targetPill.click();
    }
})();
</script>

<?php get_footer( 'shop' );
