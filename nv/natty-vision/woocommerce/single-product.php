<?php
/**
 * Natty Vision single product template.
 *
 * Reads real WooCommerce variations (preferred) or falls back to JSON.
 * Renders mg variants as pills; hides the default dropdown via CSS.
 *
 * @package NattyVision
 */
defined( 'ABSPATH' ) || exit;
get_header( 'shop' );

while ( have_posts() ) :
    the_post();
    global $product;
    if ( ! $product ) $product = wc_get_product( get_the_ID() );
    if ( ! $product ) continue;

    $pid = $product->get_id();

    // Editable values with fallbacks.
    $badge   = nv_meta( $pid, '_nv_badge' );
    $eyebrow = nv_meta( $pid, '_nv_eyebrow' );
    if ( ! $eyebrow ) {
        $cats    = wc_get_product_category_list( $pid, ' · ' );
        $eyebrow = $cats ? wp_strip_all_tags( $cats ) : '';
    }
    $title_em = nv_meta( $pid, '_nv_title_em' );
    $tagline  = nv_meta( $pid, '_nv_tagline' );
    if ( ! $tagline ) $tagline = $product->get_short_description();

    $spec_rows = array_filter( array(
        nv_meta( $pid, '_nv_spec_1' ),
        nv_meta( $pid, '_nv_spec_2' ),
        nv_meta( $pid, '_nv_spec_3' ),
        nv_meta( $pid, '_nv_spec_4', get_theme_mod( 'nv_spec_4', __( 'Third-party tested · cold-chain shipping', 'natty-vision' ) ) ),
    ) );

    $sku_prefix   = nv_meta( $pid, '_nv_sku_prefix' );
    $unit         = nv_meta( $pid, '_nv_unit', 'mg' );
    $purity_label = nv_meta( $pid, '_nv_purity_label', __( '99%+ purity', 'natty-vision' ) );
    $price_suffix = nv_meta( $pid, '_nv_price_suffix', __( 'Per vial', 'natty-vision' ) );
    $disclaimer   = nv_meta( $pid, '_nv_disclaimer', get_theme_mod( 'nv_disclaimer', __( 'For research use only. Not for human or veterinary use. Not a drug, food, or cosmetic. Not for diagnostic or therapeutic use.', 'natty-vision' ) ) );
    $coa_url      = nv_meta( $pid, '_nv_coa_url' );

    // ==========================================================
    // Build variant data — prefer real WC variations (Kentro-tracked),
    // fall back to JSON if no variations exist.
    // ==========================================================
    $pill_variants = array();
    $is_variable   = $product->is_type( 'variable' );

    if ( $is_variable ) {
        $available_variations = $product->get_available_variations();
        foreach ( $available_variations as $av ) {
            // Find the strength attribute value (case-insensitive).
            $mg_label = '';
            foreach ( $av['attributes'] as $key => $val ) {
                if ( stripos( $key, 'strength' ) !== false ) {
                    $mg_label = $val;
                    break;
                }
            }
            if ( ! $mg_label ) continue;
            // Extract numeric mg value (e.g., "10mg" -> "10")
            preg_match( '/(\d+(?:\.\d+)?)/', $mg_label, $matches );
            $mg_num = $matches[1] ?? $mg_label;

            $variation = wc_get_product( $av['variation_id'] );
            $stock_status = $variation ? $variation->get_stock_status() : 'instock';

            $pill_variants[] = array(
                'mg'           => $mg_num,
                'mg_label'     => $mg_num . $unit,
                'price'        => $av['display_price'],
                'sku'          => $variation ? $variation->get_sku() : '',
                'stock'        => $stock_status === 'instock' ? 'in' : ( $stock_status === 'onbackorder' ? 'low' : 'out' ),
                'variation_id' => $av['variation_id'],
                'attributes'   => $av['attributes'],
            );
        }
        // Sort by mg ascending
        usort( $pill_variants, function ( $a, $b ) {
            return floatval( $a['mg'] ) <=> floatval( $b['mg'] );
        } );
    }

    // Fallback to JSON if no variations or product isn't variable.
    if ( empty( $pill_variants ) ) {
        $variants_raw = nv_meta( $pid, '_nv_variants' );
        if ( $variants_raw ) {
            $decoded = json_decode( $variants_raw, true );
            if ( is_array( $decoded ) ) {
                foreach ( $decoded as $v ) {
                    $mg = $v['mg'] ?? '';
                    if ( $mg === '' ) continue;
                    $pill_variants[] = array(
                        'mg'           => $mg,
                        'mg_label'     => $mg . $unit,
                        'price'        => $v['price'] ?? 0,
                        'sku'          => $v['sku'] ?? ( $sku_prefix ? $sku_prefix . $mg : '' ),
                        'stock'        => $v['stock'] ?? ( $product->get_stock_status() === 'instock' ? 'in' : 'out' ),
                        'variation_id' => 0,
                        'attributes'   => array(),
                    );
                }
            }
        }
    }

    // Default-active variant (first one with stock, else first).
    $default_variant = null;
    foreach ( $pill_variants as $v ) {
        if ( $v['stock'] === 'in' ) { $default_variant = $v; break; }
    }
    if ( ! $default_variant && ! empty( $pill_variants ) ) {
        $default_variant = $pill_variants[0];
    }

    // Determine display values for default state.
    $default_mg    = $default_variant['mg'] ?? '';
    $default_price = $default_variant['price'] ?? $product->get_price();
    $default_sku   = $default_variant['sku'] ?? $product->get_sku();
    $default_stock = $default_variant['stock'] ?? ( $product->get_stock_status() === 'instock' ? 'in' : 'out' );

    // Build the title with the optional italic accent word.
    // If the title already ends with the accent text, italicize that trailing
    // word in place instead of appending a duplicate (e.g. "Bacteriostatic Water"
    // + accent "Water" => "Bacteriostatic <em>Water</em>", not "...Water Water").
    $nv_title      = get_the_title();
    $nv_title_em   = trim( (string) $title_em );
    if ( $nv_title_em !== '' ) {
        $em_len = strlen( $nv_title_em );
        if ( $em_len <= strlen( $nv_title ) && strtolower( substr( $nv_title, -$em_len ) ) === strtolower( $nv_title_em ) ) {
            $base          = rtrim( substr( $nv_title, 0, strlen( $nv_title ) - $em_len ) );
            $tail          = substr( $nv_title, strlen( $nv_title ) - $em_len );
            $nv_title_html = esc_html( $base ) . ( $base !== '' ? ' ' : '' ) . '<em>' . esc_html( $tail ) . '</em>';
        } else {
            $nv_title_html = esc_html( $nv_title ) . ' <em>' . esc_html( $nv_title_em ) . '</em>';
        }
    } else {
        $nv_title_html = esc_html( $nv_title );
    }
    ?>

    <main class="nv-product-main">
        <div class="nv-container">

            <article id="product-<?php the_ID(); ?>" <?php wc_product_class( 'nv-product', $product ); ?> data-product-id="<?php echo esc_attr( $pid ); ?>">

                <div class="nv-product-layout">

                    <div class="nv-gallery-card">
                        <?php
                        if ( has_post_thumbnail() ) {
                            the_post_thumbnail( 'large', array( 'class' => 'nv-gallery-img' ) );
                        } else {
                            ?>
                            <div class="nv-gallery-placeholder" aria-hidden="true">
                                <svg viewBox="0 0 100 130"><rect x="35" y="0" width="30" height="14" rx="2" fill="#1a1e1c"/><rect x="32" y="14" width="36" height="6" rx="1" fill="#1a1e1c"/><rect x="20" y="20" width="60" height="105" rx="6" fill="#1a1e1c"/></svg>
                            </div>
                            <?php
                        }
                        ?>
                    </div>

                    <div class="nv-summary">

                        <?php if ( $badge ) : ?>
                            <span class="nv-eyebrow-pill"><?php echo esc_html( $badge ); ?></span>
                        <?php endif; ?>

                        <?php if ( $eyebrow ) : ?>
                            <p class="nv-product-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
                        <?php endif; ?>

                        <h1 class="nv-product-title">
                            <?php echo wp_kses_post( $nv_title_html ); ?>
                        </h1>

                        <?php if ( $tagline ) : ?>
                            <p class="nv-product-tagline"><?php echo wp_kses_post( $tagline ); ?></p>
                        <?php endif; ?>

                        <?php if ( ! empty( $spec_rows ) ) : ?>
                            <ul class="nv-spec-list">
                                <?php foreach ( $spec_rows as $row ) :
                                    $rendered = str_replace( '{mg}', '<span data-nv-mg>' . esc_html( $default_mg . $unit ) . '</span>', $row );
                                    ?>
                                    <li>
                                        <svg class="nv-spec-check" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 10l4 4 8-8"/></svg>
                                        <span><?php echo wp_kses_post( $rendered ); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if ( ! empty( $pill_variants ) ) : ?>
                            <div class="nv-variant-block" data-nv-unit="<?php echo esc_attr( $unit ); ?>" data-nv-variants='<?php echo esc_attr( wp_json_encode( $pill_variants ) ); ?>'>
                                <div class="nv-variant-label">
                                    <span><?php esc_html_e( 'Strength', 'natty-vision' ); ?></span>
                                    <span class="nv-variant-label-active" data-nv-active-label><?php echo esc_html( $default_mg . $unit ); ?> per vial</span>
                                </div>
                                <div class="nv-variant-pills">
                                    <?php foreach ( $pill_variants as $i => $v ) :
                                        $active   = ( $v === $default_variant ) ? ' active' : '';
                                        $disabled = ( $v['stock'] === 'out' ) ? ' disabled' : '';
                                        ?>
                                        <button type="button"
                                                class="nv-variant-pill<?php echo $active; ?>"
                                                data-mg="<?php echo esc_attr( $v['mg'] ); ?>"
                                                data-mg-label="<?php echo esc_attr( $v['mg_label'] ); ?>"
                                                data-price="<?php echo esc_attr( $v['price'] ); ?>"
                                                data-sku="<?php echo esc_attr( $v['sku'] ); ?>"
                                                data-stock="<?php echo esc_attr( $v['stock'] ); ?>"
                                                data-variation-id="<?php echo esc_attr( $v['variation_id'] ); ?>"
                                                data-attributes='<?php echo esc_attr( wp_json_encode( $v['attributes'] ) ); ?>'
                                                <?php echo $disabled; ?>>
                                            <?php echo esc_html( $v['mg'] . $unit ); ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="nv-price-row">
                            <span class="nv-product-price" data-nv-price>
                                <?php
                                if ( $default_price ) {
                                    echo '$' . esc_html( number_format( (float) $default_price, 2 ) );
                                } else {
                                    echo $product->get_price_html();
                                }
                                ?>
                            </span>
                            <?php if ( $price_suffix ) : ?>
                                <span class="nv-product-price-suffix"><?php echo esc_html( $price_suffix ); ?></span>
                            <?php endif; ?>
                        </div>

                        <?php
                        $stock_class = 'nv-stock-in';
                        $stock_text  = __( 'In stock', 'natty-vision' );
                        if ( $default_stock === 'out' ) {
                            $stock_class = 'nv-stock-out';
                            $stock_text  = __( 'Out of stock', 'natty-vision' );
                        } elseif ( $default_stock === 'low' ) {
                            $stock_class = 'nv-stock-low';
                            $stock_text  = __( 'Backorder', 'natty-vision' );
                        }
                        ?>
                        <p class="nv-stock-indicator <?php echo esc_attr( $stock_class ); ?>" data-nv-stock>
                            <span class="nv-stock-dot"></span>
                            <span data-nv-stock-text><?php echo esc_html( $stock_text ); ?></span>
                        </p>

                        <?php if ( $default_stock === 'out' ) : ?>
                        <div class="nv-bis" data-nv-bis
                             data-product="<?php echo esc_attr( get_the_title( $pid ) ); ?>"
                             data-product-id="<?php echo esc_attr( $pid ); ?>"
                             data-sku="<?php echo esc_attr( $product->get_sku() ); ?>">
                            <p class="nv-bis-label">Notify me when this is back in stock</p>
                            <div class="nv-bis-row">
                                <input type="email" class="nv-bis-email" placeholder="you@email.com" autocomplete="email">
                                <button type="button" class="nv-bis-btn">Notify me</button>
                            </div>
                            <p class="nv-bis-msg" data-nv-bis-msg hidden></p>
                        </div>
                        <style>
                        .nv-bis{margin-top:8px}
                        .nv-bis-label{font-size:14px;margin:0 0 10px;opacity:.85}
                        .nv-bis-row{display:flex;gap:8px;flex-wrap:wrap}
                        .nv-bis-email{flex:1;min-width:190px;padding:13px 15px;border:1px solid rgba(0,0,0,.18);border-radius:10px;font-size:15px;background:#fff}
                        .nv-bis-btn{padding:13px 20px;border:0;border-radius:10px;background:#1a1a1a;color:#fff;font-size:15px;font-weight:500;cursor:pointer}
                        .nv-bis-btn:disabled{opacity:.6;cursor:default}
                        .nv-bis-msg{font-size:13px;margin:10px 0 0}
                        .nv-bis-msg.ok{color:#2e7d32}
                        .nv-bis-msg.err{color:#c62828}
                        </style>
                        <script>
                        (function(){
                            var box=document.querySelector('[data-nv-bis]');
                            if(!box) return;
                            var btn=box.querySelector('.nv-bis-btn'),
                                email=box.querySelector('.nv-bis-email'),
                                msg=box.querySelector('[data-nv-bis-msg]');
                            function show(t,ok){msg.hidden=false;msg.textContent=t;msg.className='nv-bis-msg '+(ok?'ok':'err');}
                            btn.addEventListener('click',function(){
                                var e=(email.value||'').trim();
                                if(!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(e)){show('Please enter a valid email.',false);return;}
                                try{
                                    window.omnisend=window.omnisend||[];
                                    omnisend.push(["identifyContact",{email:e}]);
                                    omnisend.push(["track","backInStockRequested",{
                                        email:e,
                                        product:box.dataset.product,
                                        productID:box.dataset.productId,
                                        sku:box.dataset.sku,
                                        url:window.location.href
                                    }]);
                                    show("You're on the list. We'll email you when it's back.",true);
                                    btn.disabled=true;email.value='';
                                }catch(err){show('Could not sign you up right now, please try again.',false);}
                            });
                        })();
                        </script>
                        <?php else : ?>
                        <div class="nv-cart-row">
                            <?php woocommerce_template_single_add_to_cart(); ?>
                        </div>
                        <?php endif; ?>

                        <?php if ( $disclaimer ) : ?>
                            <p class="nv-disclaimer"><?php echo wp_kses_post( $disclaimer ); ?></p>
                        <?php endif; ?>

                        <?php if ( $coa_url ) : ?>
                        <div class="nv-verify">
                            <div class="nv-verify-inner">
                                <div class="nv-verify-txt">
                                    <span class="nv-verify-seal" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.5 8 8 11 4.5-3 8-6 8-11V5Z"/><path d="m9 12 2 2 4-4"/></svg>
                                    </span>
                                    <div>
                                        <h4><?php esc_html_e( 'Independently lab verified', 'natty-vision' ); ?></h4>
                                        <p><?php esc_html_e( 'This batch was tested by Kovera Labs — purity, net content, LC-MS identity and endotoxins. Check the source yourself.', 'natty-vision' ); ?></p>
                                    </div>
                                </div>
                                <a class="nv-verify-btn" href="<?php echo esc_url( $coa_url ); ?>" target="_blank" rel="noopener nofollow">
                                    <?php esc_html_e( 'Verify on Kovera Labs', 'natty-vision' ); ?>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"/></svg>
                                </a>
                            </div>
                        </div>
                        <style>
                        .nv-verify{margin-top:22px;border-radius:16px;padding:22px 24px;color:#eef2ea;position:relative;overflow:hidden;background:radial-gradient(130% 150% at 12% 0%,#27302b 0%,#2a302d 44%,#11150f 100%);}
                        .nv-verify-inner{position:relative;display:flex;align-items:center;gap:16px;flex-wrap:wrap;justify-content:space-between;}
                        .nv-verify-txt{display:flex;align-items:center;gap:14px;}
                        .nv-verify-seal{width:44px;height:44px;border-radius:50%;background:rgba(93,158,116,.16);border:1px solid rgba(93,158,116,.42);display:flex;align-items:center;justify-content:center;flex:none;}
                        .nv-verify-seal svg{width:21px;height:21px;color:#bfe0c4;}
                        .nv-verify h4{font-family:'Instrument Serif',Georgia,serif;font-weight:400;font-size:22px;color:#fff;margin:0;}
                        .nv-verify p{font-size:12.5px;color:#bcc6ba;margin:2px 0 0;max-width:340px;line-height:1.5;}
                        .nv-verify-btn{font-family:'DM Mono',monospace;display:inline-flex;align-items:center;gap:9px;height:48px;padding:0 20px;background:#a8bfa2;color:#13201a;border:0;border-radius:11px;font-size:11px;letter-spacing:.1em;text-transform:uppercase;cursor:pointer;text-decoration:none;white-space:nowrap;transition:background .18s;}
                        .nv-verify-btn:hover{background:#bcd1b6;}
                        .nv-verify-btn svg{width:14px;height:14px;}
                        @media(max-width:520px){.nv-verify-btn{width:100%;justify-content:center;}}
                        </style>
                        <?php endif; ?>

                    </div>

                </div>

                <div class="nv-product-tabs">
                    <?php
                    $tabs = array();
                    $tabs['description'] = array(
                        'title'   => __( 'Description', 'natty-vision' ),
                        'content' => apply_filters( 'the_content', get_the_content() ),
                    );

                    $specs_html = nv_meta( $pid, '_nv_specs_html' );
                    if ( $specs_html ) $tabs['specs'] = array( 'title' => __( 'Specifications', 'natty-vision' ), 'content' => wpautop( $specs_html ) );

                    $shipping_html = nv_meta( $pid, '_nv_shipping_html' );
                    if ( $shipping_html ) $tabs['shipping'] = array( 'title' => __( 'Shipping', 'natty-vision' ), 'content' => wpautop( $shipping_html ) );

                    $storage_html = nv_meta( $pid, '_nv_storage_html' );
                    if ( $storage_html ) $tabs['storage'] = array( 'title' => __( 'Storage', 'natty-vision' ), 'content' => wpautop( $storage_html ) );

                    $review_count    = $product->get_review_count();
                    $tabs['reviews'] = array(
                        'title'   => sprintf( __( 'Reviews (%d)', 'natty-vision' ), $review_count ),
                        'content' => '',
                    );
                    ?>
                    <ul class="nv-tabs-list">
                        <?php $first = true; foreach ( $tabs as $key => $tab ) : ?>
                            <li class="<?php echo $first ? 'active' : ''; ?>" data-tab="<?php echo esc_attr( $key ); ?>"><button type="button"><?php echo esc_html( $tab['title'] ); ?></button></li>
                        <?php $first = false; endforeach; ?>
                    </ul>
                    <?php $first = true; foreach ( $tabs as $key => $tab ) : ?>
                        <div class="nv-tab-panel <?php echo $first ? 'active' : ''; ?>" id="nv-tab-<?php echo esc_attr( $key ); ?>">
                            <?php
                            if ( $key === 'reviews' ) {
                                comments_template();
                            } else {
                                echo $tab['content'];
                            }
                            ?>
                        </div>
                    <?php $first = false; endforeach; ?>
                </div>

            </article>
        </div>
    </main>

<?php endwhile;
get_footer( 'shop' );
