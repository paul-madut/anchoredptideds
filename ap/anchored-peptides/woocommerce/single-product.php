<?php
/**
 * Anchored Peptides single product template.
 *
 * Reads real WooCommerce variations (preferred) or the `_ap_variants` JSON
 * fallback. Renders strength as SIZE pills; price/stock update via main.js.
 *
 * @package AnchoredPeptides
 */
defined( 'ABSPATH' ) || exit;
get_header( 'shop' );

while ( have_posts() ) :
    the_post();
    global $product;
    if ( ! $product ) $product = wc_get_product( get_the_ID() );
    if ( ! $product ) continue;
    $pid = $product->get_id();

    // Editable values + fallbacks.
    $badge   = ap_meta( $pid, '_ap_badge' );
    $eyebrow = ap_meta( $pid, '_ap_eyebrow' );
    if ( ! $eyebrow ) {
        $cats    = wc_get_product_category_list( $pid, ' · ' );
        $eyebrow = $cats ? wp_strip_all_tags( $cats ) : '';
    }
    $title_em = ap_meta( $pid, '_ap_title_em' );
    $tagline  = ap_meta( $pid, '_ap_tagline' );
    if ( ! $tagline ) $tagline = $product->get_short_description();

    $spec_pills = array_filter( array(
        ap_meta( $pid, '_ap_spec_1', __( 'HPLC tested for purity', 'anchored-peptides' ) ),
        ap_meta( $pid, '_ap_spec_2', __( '24/7 support', 'anchored-peptides' ) ),
        ap_meta( $pid, '_ap_spec_3', get_theme_mod( 'ap_spec_pill_3', __( 'Ships from Canada', 'anchored-peptides' ) ) ),
    ) );

    $unit         = ap_meta( $pid, '_ap_unit', 'mg' );
    $price_suffix = ap_meta( $pid, '_ap_price_suffix', __( 'CAD · per vial', 'anchored-peptides' ) );
    $disclaimer   = ap_meta( $pid, '_ap_disclaimer', get_theme_mod( 'ap_disclaimer', __( 'For research use only. Not for human or veterinary use. Not a drug, food, or cosmetic.', 'anchored-peptides' ) ) );
    $coa_url      = ap_meta( $pid, '_ap_coa_url' );

    // ---- Build variant pills (prefer real WC variations, fall back to JSON) ----
    $pills = array();
    if ( $product->is_type( 'variable' ) ) {
        foreach ( $product->get_available_variations() as $av ) {
            $label = '';
            foreach ( $av['attributes'] as $k => $v ) { if ( stripos( $k, 'strength' ) !== false ) { $label = $v; break; } }
            if ( ! $label ) continue;
            preg_match( '/(\d+(?:\.\d+)?)/', $label, $m );
            $num       = $m[1] ?? $label;
            $variation = wc_get_product( $av['variation_id'] );
            $ss        = $variation ? $variation->get_stock_status() : 'instock';
            $pills[]   = array(
                'mg' => $num, 'price' => $av['display_price'], 'sku' => $variation ? $variation->get_sku() : '',
                'stock' => $ss === 'instock' ? 'in' : ( $ss === 'onbackorder' ? 'low' : 'out' ),
                'variation_id' => $av['variation_id'], 'attributes' => $av['attributes'],
                'label' => __( 'Single vial', 'anchored-peptides' ), 'note' => '',
            );
        }
        usort( $pills, function ( $a, $b ) { return floatval( $a['mg'] ) <=> floatval( $b['mg'] ); } );
    }
    if ( empty( $pills ) ) {
        $decoded = json_decode( ap_meta( $pid, '_ap_variants' ), true );
        if ( is_array( $decoded ) ) {
            foreach ( $decoded as $v ) {
                if ( ! isset( $v['mg'] ) ) continue;
                $pills[] = array(
                    'mg' => $v['mg'], 'price' => $v['price'] ?? 0, 'sku' => $v['sku'] ?? '',
                    'stock' => $v['stock'] ?? ( $product->get_stock_status() === 'instock' ? 'in' : 'out' ),
                    'variation_id' => 0, 'attributes' => array(),
                    'label' => $v['label'] ?? __( 'Single vial', 'anchored-peptides' ), 'note' => $v['note'] ?? '',
                );
            }
        }
    }

    $default = null;
    foreach ( $pills as $p ) { if ( $p['stock'] === 'in' ) { $default = $p; break; } }
    if ( ! $default ) { foreach ( $pills as $p ) { if ( $p['stock'] === 'low' ) { $default = $p; break; } } }
    if ( ! $default && ! empty( $pills ) ) $default = $pills[0];

    // The add-to-cart form should show if ANY variant is purchasable (in stock or
    // backorder) — not just the default pill. WooCommerce gates each variation itself.
    $any_buyable = false;
    foreach ( $pills as $p ) { if ( $p['stock'] !== 'out' ) { $any_buyable = true; break; } }
    if ( empty( $pills ) ) {
        $any_buyable = $product->is_purchasable() && $product->get_stock_status() !== 'outofstock';
    }
    $d_price = $default['price'] ?? $product->get_price();
    $d_stock = $default['stock'] ?? ( $product->get_stock_status() === 'instock' ? 'in' : 'out' );

    // Title with optional italic accent word.
    $title    = get_the_title();
    $em       = trim( (string) $title_em );
    if ( $em !== '' ) {
        $len = strlen( $em );
        if ( $len <= strlen( $title ) && strtolower( substr( $title, -$len ) ) === strtolower( $em ) ) {
            $base = rtrim( substr( $title, 0, strlen( $title ) - $len ) );
            $title_html = esc_html( $base ) . ( $base !== '' ? ' ' : '' ) . '<em>' . esc_html( substr( $title, strlen( $title ) - $len ) ) . '</em>';
        } else {
            $title_html = esc_html( $title ) . ' <em>' . esc_html( $em ) . '</em>';
        }
    } else {
        $title_html = esc_html( $title );
    }

    $rating  = (float) $product->get_average_rating();
    $rcount  = (int) $product->get_review_count();
    $thumb   = get_post_thumbnail_id( $pid );
    ?>
    <main class="ap-product">
        <p class="ap-breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'anchored-peptides' ); ?></a> / <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Shop', 'anchored-peptides' ); ?></a> / <?php echo esc_html( $title ); ?></p>

        <article id="product-<?php the_ID(); ?>" <?php wc_product_class( 'ap-product-article', $product ); ?> data-product-id="<?php echo esc_attr( $pid ); ?>">
            <div class="ap-product-layout">

                <div>
                    <div class="ap-gallery-card">
                        <?php if ( $thumb ) : ?>
                            <?php the_post_thumbnail( 'large', array( 'class' => 'ap-gallery-img' ) ); ?>
                        <?php else : ?>
                            <svg viewBox="0 0 100 130" width="150" aria-hidden="true"><rect x="35" y="0" width="30" height="14" rx="2" fill="#3E412E"/><rect x="32" y="14" width="36" height="6" rx="1" fill="#3E412E"/><rect x="20" y="20" width="60" height="105" rx="6" fill="#C9C1AC"/></svg>
                        <?php endif; ?>
                        <?php if ( $badge ) : ?><span class="ap-gallery-badge"><?php echo esc_html( $badge ); ?></span><?php endif; ?>
                    </div>
                    <div class="ap-thumbs">
                        <span class="ap-thumb active"><?php esc_html_e( 'Vial', 'anchored-peptides' ); ?></span>
                        <?php if ( $coa_url ) : ?><span class="ap-thumb"><?php esc_html_e( 'COA Report', 'anchored-peptides' ); ?></span><?php endif; ?>
                        <span class="ap-thumb"><?php esc_html_e( 'HPLC Trace', 'anchored-peptides' ); ?></span>
                    </div>
                </div>

                <div class="ap-summary">
                    <?php if ( $eyebrow ) : ?><span class="ap-eyebrow"><?php echo esc_html( $eyebrow ); ?></span><?php endif; ?>
                    <h1><?php echo wp_kses_post( $title_html ); ?></h1>

                    <?php if ( $rcount > 0 ) : ?>
                        <div class="ap-rating-row"><span class="ap-stars"><?php echo esc_html( ap_stars( $rating ) ); ?></span> <b><?php echo esc_html( number_format( $rating, 1 ) ); ?></b> (<?php printf( esc_html__( '%d reviews', 'anchored-peptides' ), $rcount ); ?>)</div>
                    <?php endif; ?>

                    <div class="ap-price-row">
                        <span class="ap-price" data-ap-price><?php echo $d_price ? '$' . esc_html( number_format( (float) $d_price, 2 ) ) : wp_kses_post( $product->get_price_html() ); ?></span>
                        <?php if ( $price_suffix ) : ?><span class="ap-price-suffix"><?php echo esc_html( $price_suffix ); ?></span><?php endif; ?>
                    </div>

                    <?php if ( $tagline ) : ?><p class="ap-summary-desc"><?php echo wp_kses_post( $tagline ); ?></p><?php endif; ?>

                    <?php if ( ! empty( $spec_pills ) ) : ?>
                    <div class="ap-spec-pills">
                        <?php foreach ( $spec_pills as $sp ) : ?>
                            <span class="ap-spec-pill"><svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10l4 4 8-8"/></svg><?php echo esc_html( $sp ); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $pills ) ) : ?>
                    <div class="ap-variant-block" data-ap-unit="<?php echo esc_attr( $unit ); ?>">
                        <div class="ap-variant-label"><span><?php esc_html_e( 'Size', 'anchored-peptides' ); ?></span> · <b data-ap-active><?php echo esc_html( ( $default['mg'] ?? '' ) . $unit ); ?></b></div>
                        <div class="ap-variant-pills">
                            <?php foreach ( $pills as $v ) :
                                $active   = ( $v === $default ) ? ' active' : '';
                                $disabled = ( $v['stock'] === 'out' ) ? ' disabled' : ''; ?>
                                <button type="button" class="ap-variant-pill<?php echo $active; ?>"
                                    data-mg="<?php echo esc_attr( $v['mg'] ); ?>"
                                    data-price="<?php echo esc_attr( $v['price'] ); ?>"
                                    data-sku="<?php echo esc_attr( $v['sku'] ); ?>"
                                    data-stock="<?php echo esc_attr( $v['stock'] ); ?>"
                                    data-variation-id="<?php echo esc_attr( $v['variation_id'] ); ?>"
                                    data-attributes='<?php echo esc_attr( wp_json_encode( $v['attributes'] ) ); ?>'<?php echo $disabled; ?>>
                                    <?php if ( ! empty( $v['note'] ) ) : ?><span class="ap-variant-save"><?php echo esc_html( $v['note'] ); ?></span><?php endif; ?>
                                    <b><?php echo esc_html( $v['mg'] . $unit ); ?></b>
                                    <span>$<?php echo esc_html( number_format( (float) $v['price'], 2 ) ); ?></span>
                                    <?php if ( ! empty( $v['label'] ) ) : ?><small><?php echo esc_html( $v['label'] ); ?></small><?php endif; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php
                    $sc = 'ap-stock'; $st = __( 'In stock', 'anchored-peptides' );
                    if ( $d_stock === 'out' ) { $sc .= ' out'; $st = __( 'Out of stock', 'anchored-peptides' ); }
                    elseif ( $d_stock === 'low' ) { $sc .= ' low'; $st = __( 'Only a few left — selling fast', 'anchored-peptides' ); }
                    ?>
                    <p class="<?php echo esc_attr( $sc ); ?>" data-ap-stock><span class="dot"></span> <span data-ap-stock-text><?php echo esc_html( $st ); ?></span></p>
                    <p class="ap-dispatch"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg> <?php esc_html_e( 'Order before 2 PM ET for same-day dispatch', 'anchored-peptides' ); ?></p>

                    <?php if ( $any_buyable ) : ?>
                    <div class="ap-cart-row"><?php woocommerce_template_single_add_to_cart(); ?></div>
                    <?php else : ?>
                    <p class="ap-summary-desc"><?php esc_html_e( 'This compound is currently out of stock. Check back soon.', 'anchored-peptides' ); ?></p>
                    <?php endif; ?>

                    <div class="ap-fulfil">
                        <span class="flag">🇨🇦</span>
                        <span><b><?php esc_html_e( 'Canadian domestic fulfillment.', 'anchored-peptides' ); ?></b> <?php esc_html_e( 'Lyophilized vials filled to ~104% of label weight. Reconstitute with bacteriostatic water.', 'anchored-peptides' ); ?></span>
                    </div>

                    <?php if ( $coa_url ) : ?>
                    <div class="ap-verify">
                        <div class="ap-verify-inner">
                            <div>
                                <h4><?php esc_html_e( 'Independently lab verified', 'anchored-peptides' ); ?></h4>
                                <p><?php esc_html_e( 'This batch was third-party HPLC tested — purity, net content and identity. Check the source yourself.', 'anchored-peptides' ); ?></p>
                            </div>
                            <a href="<?php echo esc_url( $coa_url ); ?>" target="_blank" rel="noopener nofollow"><?php esc_html_e( 'View COA', 'anchored-peptides' ); ?>
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17 17 7M9 7h8v8"/></svg></a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ( $disclaimer ) : ?><p class="ap-disclaimer"><?php echo wp_kses_post( $disclaimer ); ?></p><?php endif; ?>
                </div>
            </div>

            <?php
            // ---- Tabs ----
            $tabs = array( 'details' => array( 'title' => __( 'Details', 'anchored-peptides' ), 'content' => apply_filters( 'the_content', get_the_content() ) ) );
            $specs = ap_meta( $pid, '_ap_specs_html' );
            if ( $specs ) $tabs['specs'] = array( 'title' => __( 'Specifications', 'anchored-peptides' ), 'content' => wpautop( $specs ) );
            $storage = ap_meta( $pid, '_ap_storage_html' );
            if ( $storage ) $tabs['storage'] = array( 'title' => __( 'Storage', 'anchored-peptides' ), 'content' => wpautop( $storage ) );
            ?>
            <div class="ap-tabs">
                <div class="ap-tabs-nav">
                    <?php $f = true; foreach ( $tabs as $k => $t ) : ?>
                        <button class="<?php echo $f ? 'active' : ''; ?>" data-tab="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $t['title'] ); ?></button>
                    <?php $f = false; endforeach; ?>
                </div>
                <?php $f = true; foreach ( $tabs as $k => $t ) : ?>
                    <div class="ap-tab-panel <?php echo $f ? 'active' : ''; ?>" id="ap-tab-<?php echo esc_attr( $k ); ?>"><?php
                        // 'details' is post content (already run through the_content); other
                        // tabs come from meta — kses them at output as defense-in-depth.
                        echo ( $k === 'details' ) ? $t['content'] : wp_kses_post( $t['content'] );
                    ?></div>
                <?php $f = false; endforeach; ?>
            </div>

            <!-- FAQ accordion -->
            <div class="ap-acc">
                <?php
                $faqs = array(
                    array( __( 'What are research peptides?', 'anchored-peptides' ), __( 'Peptides are short chains of amino acids that act as signaling molecules. The compounds sold here are supplied strictly as lyophilized powder for laboratory and research use only — not for human consumption.', 'anchored-peptides' ) ),
                    array( __( 'Shipping & ordering', 'anchored-peptides' ), __( 'Orders placed before 2 PM ET ship the same business day from our Canadian fulfillment centre.', 'anchored-peptides' ) ),
                    array( __( 'COAs & lot reports', 'anchored-peptides' ), __( 'Every lot is third-party HPLC tested. Look up the certificate of analysis using the lot number printed on your vial.', 'anchored-peptides' ) ),
                    array( __( 'Storage & handling', 'anchored-peptides' ), __( 'Store lyophilized vials away from light. Once reconstituted with bacteriostatic water, refrigerate.', 'anchored-peptides' ) ),
                );
                foreach ( $faqs as $faq ) : ?>
                    <div class="ap-acc-item">
                        <button class="ap-acc-head"><?php echo esc_html( $faq[0] ); ?> <span class="ico">+</span></button>
                        <div class="ap-acc-body"><?php echo esc_html( $faq[1] ); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php
            // ---- Related ----
            $related_ids = wc_get_related_products( $pid, 4 );
            if ( $related_ids ) : ?>
            <div class="ap-related">
                <h2><?php esc_html_e( 'You may also like', 'anchored-peptides' ); ?></h2>
                <div class="ap-prod-grid">
                    <?php foreach ( $related_ids as $rid ) ap_render_product_card( $rid ); ?>
                </div>
            </div>
            <?php endif; ?>

        </article>
    </main>
<?php endwhile;
get_footer( 'shop' );
