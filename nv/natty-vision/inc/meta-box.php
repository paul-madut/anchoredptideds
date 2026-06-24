<?php
/**
 * Natty Vision — per-product editable meta box.
 *
 * Adds a panel under the product description with all the per-product
 * editable fields used by the single-product template. No ACF dependency.
 *
 * @package NattyVision
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'add_meta_boxes', function () {
    add_meta_box(
        'nv_product_meta',
        __( 'Natty Vision Product', 'natty-vision' ),
        'nv_product_meta_box',
        'product',
        'normal',
        'high'
    );
} );

function nv_product_meta_box( $post ) {
    wp_nonce_field( 'nv_product_meta', 'nv_product_meta_nonce' );

    $fields = nv_meta_field_definitions();
    echo '<style>
        .nv-meta-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:8px;}
        .nv-meta-field{display:flex;flex-direction:column;gap:6px;}
        .nv-meta-field label{font-weight:600;font-size:13px;color:#1a1e1c;}
        .nv-meta-field code{display:inline-block;background:#f0efea;color:#2d6a4f;padding:1px 6px;border-radius:3px;font-size:11px;font-family:monospace;}
        .nv-meta-field input[type=text],.nv-meta-field textarea,.nv-meta-field select{width:100%;padding:8px 10px;border:1px solid #d4d2cc;border-radius:6px;font-size:13px;}
        .nv-meta-field textarea{font-family:inherit;min-height:80px;resize:vertical;}
        .nv-meta-field .description{font-size:12px;color:#7a7f7c;margin:0;line-height:1.5;}
        .nv-meta-section{grid-column:1/-1;border-top:1px solid #e5e5e5;padding-top:16px;margin-top:8px;}
        .nv-meta-section h3{margin:0 0 4px;font-size:14px;}
        .nv-meta-section p{margin:0;color:#7a7f7c;font-size:12px;}
        .nv-generate-btn{background:#2d6a4f;color:#fff;border:0;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:500;cursor:pointer;margin-top:8px;display:inline-block;}
        .nv-generate-btn:hover{background:#1f5238;}
        .nv-generate-btn:disabled{opacity:0.6;cursor:wait;}
        .nv-generate-status{margin-top:8px;padding:10px 12px;border-radius:6px;font-size:13px;display:none;}
        .nv-generate-status.success{background:#d4edda;color:#155724;display:block;}
        .nv-generate-status.error{background:#f8d7da;color:#721c24;display:block;}
        .nv-generate-help{font-size:12px;color:#7a7f7c;margin-top:6px;line-height:1.5;}
    </style>';
    echo '<div class="nv-meta-grid">';

    foreach ( $fields as $field ) {
        if ( isset( $field['section'] ) ) {
            echo '<div class="nv-meta-section"><h3>' . esc_html( $field['section'] ) . '</h3>';
            if ( ! empty( $field['section_desc'] ) ) {
                echo '<p>' . esc_html( $field['section_desc'] ) . '</p>';
            }
            echo '</div>';
            continue;
        }

        $value = get_post_meta( $post->ID, $field['key'], true );
        echo '<div class="nv-meta-field">';
        echo '<label for="' . esc_attr( $field['key'] ) . '">' . esc_html( $field['label'] ) . ' <code>' . esc_html( $field['key'] ) . '</code></label>';

        if ( $field['type'] === 'textarea' ) {
            echo '<textarea id="' . esc_attr( $field['key'] ) . '" name="' . esc_attr( $field['key'] ) . '" rows="' . ( isset( $field['rows'] ) ? intval( $field['rows'] ) : 3 ) . '">' . esc_textarea( $value ) . '</textarea>';
        } elseif ( $field['type'] === 'select' ) {
            $current = ( $value !== '' ) ? $value : ( $field['default'] ?? '' );
            echo '<select id="' . esc_attr( $field['key'] ) . '" name="' . esc_attr( $field['key'] ) . '">';
            foreach ( (array) $field['options'] as $opt ) {
                echo '<option value="' . esc_attr( $opt ) . '"' . selected( $current, $opt, false ) . '>' . esc_html( $opt ) . '</option>';
            }
            echo '</select>';
        } else {
            echo '<input type="text" id="' . esc_attr( $field['key'] ) . '" name="' . esc_attr( $field['key'] ) . '" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $field['placeholder'] ?? '' ) . '">';
        }

        if ( ! empty( $field['desc'] ) ) {
            echo '<p class="description">' . wp_kses_post( $field['desc'] ) . '</p>';
        }
        echo '</div>';
    }
    echo '</div>';

    // ---- Generate Variations button + AJAX JS ----
    $ajax_nonce = wp_create_nonce( 'nv_generate_variations' );
    ?>
    <div class="nv-meta-section" style="margin-top:24px;">
        <h3><?php esc_html_e( 'Generate WooCommerce variations', 'natty-vision' ); ?></h3>
        <p><?php esc_html_e( 'After filling in the Variants JSON above, click this button to convert the product to a Variable Product and create a real WooCommerce variation for each mg with its own SKU, price, and stock count. This is what Kentro syncs inventory against.', 'natty-vision' ); ?></p>
        <button type="button" class="nv-generate-btn" id="nv-generate-btn">
            <?php esc_html_e( '⚡ Generate variations from JSON', 'natty-vision' ); ?>
        </button>
        <div class="nv-generate-status" id="nv-generate-status"></div>
        <p class="nv-generate-help">
            <?php esc_html_e( 'After clicking, save the product to apply changes. You\'ll then see the variations in the "Product data" panel under the "Variations" tab.', 'natty-vision' ); ?>
        </p>
    </div>
    <script>
    (function () {
        var btn = document.getElementById('nv-generate-btn');
        var status = document.getElementById('nv-generate-status');
        if (!btn) return;
        btn.addEventListener('click', function () {
            var jsonField = document.getElementById('_nv_variants');
            if (!jsonField) return;
            var jsonValue = jsonField.value.trim();
            if (!jsonValue) {
                status.className = 'nv-generate-status error';
                status.textContent = 'Variants JSON is empty. Fill it in first.';
                return;
            }
            try { JSON.parse(jsonValue); } catch (e) {
                status.className = 'nv-generate-status error';
                status.textContent = 'Variants JSON is not valid: ' + e.message;
                return;
            }

            btn.disabled = true;
            status.className = 'nv-generate-status';
            status.textContent = '';
            btn.textContent = 'Generating…';

            var data = new FormData();
            data.append('action', 'nv_generate_variations');
            data.append('nonce', '<?php echo esc_js( $ajax_nonce ); ?>');
            data.append('product_id', '<?php echo (int) $post->ID; ?>');
            data.append('variants_json', jsonValue);

            fetch(ajaxurl, { method: 'POST', body: data, credentials: 'same-origin' })
                .then(function (r) { return r.json(); })
                .then(function (resp) {
                    btn.disabled = false;
                    btn.textContent = '⚡ Generate variations from JSON';
                    if (resp && resp.success) {
                        status.className = 'nv-generate-status success';
                        status.textContent = resp.data.message + ' Refresh the page to see them in the Variations tab.';
                    } else {
                        status.className = 'nv-generate-status error';
                        status.textContent = (resp && resp.data && resp.data.message) ? resp.data.message : 'Something went wrong.';
                    }
                })
                .catch(function (err) {
                    btn.disabled = false;
                    btn.textContent = '⚡ Generate variations from JSON';
                    status.className = 'nv-generate-status error';
                    status.textContent = 'Request failed: ' + err.message;
                });
        });
    })();
    </script>
    <?php
}

/**
 * Single source of truth for the editable field set.
 * The template reads these same keys.
 */
function nv_meta_field_definitions() {
    return array(
        array( 'section' => 'Header & badges', 'section_desc' => 'These show above and around the product title.' ),
        array( 'key' => '_nv_badge', 'label' => 'Featured pill', 'type' => 'text', 'placeholder' => 'Featured peptide', 'desc' => 'e.g. <em>Featured peptide</em>, <em>Top seller</em>, <em>New</em>. Leave blank to hide.' ),
        array( 'key' => '_nv_eyebrow', 'label' => 'Eyebrow text', 'type' => 'text', 'placeholder' => 'Mitochondrial · Metabolic', 'desc' => 'Small uppercase tagline above title. Falls back to product categories.' ),
        array( 'key' => '_nv_title_em', 'label' => 'Italic accent word', 'type' => 'text', 'placeholder' => 'Peptide', 'desc' => 'A word shown in italic green in the title. If the title already ends with this word it is italicized in place (e.g. "Bacteriostatic <em>Water</em>"); otherwise it is appended (e.g. "MOTS-c <em>Peptide</em>").' ),
        array( 'key' => '_nv_tagline', 'label' => 'Tagline (under title)', 'type' => 'textarea', 'placeholder' => 'A mitochondrial-derived peptide for advanced metabolic research.', 'desc' => 'Falls back to short description if blank.', 'rows' => 2 ),

        array( 'section' => 'Spec list', 'section_desc' => 'Up to 4 checkmark rows under the tagline. HTML allowed. Use {mg} to insert the active strength variant (it renders with the Unit set below, e.g. 30ml).' ),
        array( 'key' => '_nv_spec_1', 'label' => 'Spec row 1', 'type' => 'text', 'placeholder' => 'Mitochondrial-derived 16-amino-acid peptide' ),
        array( 'key' => '_nv_spec_2', 'label' => 'Spec row 2', 'type' => 'text', 'placeholder' => '{mg} per vial — lyophilized powder' ),
        array( 'key' => '_nv_spec_3', 'label' => 'Spec row 3', 'type' => 'text', 'placeholder' => 'HPLC verified at <strong>99%+ purity</strong>' ),
        array( 'key' => '_nv_spec_4', 'label' => 'Spec row 4 (feature line)', 'type' => 'text', 'placeholder' => 'Third-party tested · cold-chain shipping', 'desc' => 'Leave blank to inherit the consistent site-wide feature line set in <strong>Customizer → Natty Vision — General</strong>. Fill in only to override it for this product.' ),

        array( 'section' => 'Variants & pricing', 'section_desc' => 'Define mg + price for each variant pill. JSON array.' ),
        array( 'key' => '_nv_unit', 'label' => 'Strength unit', 'type' => 'select', 'options' => array( 'mg', 'ml', 'iu', 'mcg', 'g' ), 'default' => 'mg', 'desc' => 'Unit shown on the strength pills and label. Switch to <em>ml</em> for bacteriostatic water, <em>iu</em> for HCG/HMG, etc. The number stays from the variant; only the unit changes.' ),
        array( 'key' => '_nv_variants', 'label' => 'Variants JSON', 'type' => 'textarea', 'placeholder' => '[{"mg":5,"price":69.99},{"mg":10,"price":129.99},{"mg":20,"price":229.99}]', 'desc' => 'Each variant: <code>{"mg":number,"price":number,"sku":"optional","stock":"optional"}</code>. The <code>mg</code> key just means the numeric strength. Stock can be <em>in</em>, <em>out</em>, or <em>low</em>. Leave blank to skip variant pills and use the regular product price.', 'rows' => 4 ),
        array( 'key' => '_nv_sku_prefix', 'label' => 'SKU prefix', 'type' => 'text', 'placeholder' => 'MTSC', 'desc' => 'Generates SKU like MTSC10 from prefix + mg.' ),
        array( 'key' => '_nv_purity_label', 'label' => 'Purity label (image card)', 'type' => 'text', 'placeholder' => '99%+ purity' ),
        array( 'key' => '_nv_price_suffix', 'label' => 'Price suffix', 'type' => 'text', 'placeholder' => 'Per vial', 'desc' => 'e.g. <em>Per kit</em>, <em>Per 10ml</em>.' ),

        array( 'section' => 'Certificate of Analysis', 'section_desc' => 'Powers the Verify button on this product page and its card in the COA Library. Paste the Kovera verify link to make this product appear in the library.' ),
        array( 'key' => '_nv_coa_url', 'label' => 'Kovera verify URL', 'type' => 'text', 'placeholder' => 'https://koveralabs.com/#/verify?t=...', 'desc' => 'The independent lab link customers click to verify this batch. <strong>Required</strong> for the product to show in the COA Library.' ),
        array( 'key' => '_nv_coa_lot', 'label' => 'Batch / lot number', 'type' => 'text', 'placeholder' => 'NV-RT10-260521', 'desc' => 'Optional. Shown on the COA Library card.' ),
        array( 'key' => '_nv_coa_purity', 'label' => 'Verified purity %', 'type' => 'text', 'placeholder' => '99.38', 'desc' => 'Optional. Number only — the % is added automatically.' ),
        array( 'key' => '_nv_coa_tested', 'label' => 'Date tested', 'type' => 'text', 'placeholder' => 'Jun 3, 2026', 'desc' => 'Optional. Shown as the latest batch date on the library card.' ),

        array( 'section' => 'Tab content', 'section_desc' => 'Optional override HTML for each tab. Leave blank to hide that tab (Description always shows).' ),
        array( 'key' => '_nv_specs_html', 'label' => 'Specifications tab', 'type' => 'textarea', 'rows' => 4 ),
        array( 'key' => '_nv_shipping_html', 'label' => 'Shipping tab', 'type' => 'textarea', 'rows' => 4 ),
        array( 'key' => '_nv_storage_html', 'label' => 'Storage tab', 'type' => 'textarea', 'rows' => 4 ),

        array( 'section' => 'Disclaimer', 'section_desc' => 'Per-product override of the default research-use-only disclaimer.' ),
        array( 'key' => '_nv_disclaimer', 'label' => 'Disclaimer override', 'type' => 'textarea', 'desc' => 'Leave blank to use the global theme disclaimer.', 'rows' => 3 ),
    );
}

add_action( 'save_post_product', function ( $post_id ) {
    if ( ! isset( $_POST['nv_product_meta_nonce'] ) || ! wp_verify_nonce( $_POST['nv_product_meta_nonce'], 'nv_product_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    foreach ( nv_meta_field_definitions() as $field ) {
        if ( empty( $field['key'] ) ) continue;
        $key = $field['key'];
        if ( isset( $_POST[ $key ] ) ) {
            $value = wp_unslash( $_POST[ $key ] );
            // Allow safe HTML in spec rows, tab HTML, and tagline.
            if ( in_array( $key, array( '_nv_spec_1', '_nv_spec_2', '_nv_spec_3', '_nv_spec_4', '_nv_specs_html', '_nv_shipping_html', '_nv_storage_html', '_nv_tagline', '_nv_disclaimer' ), true ) ) {
                update_post_meta( $post_id, $key, wp_kses_post( $value ) );
            } elseif ( $key === '_nv_variants' ) {
                // Store as-is JSON, validated on read.
                update_post_meta( $post_id, $key, sanitize_textarea_field( $value ) );
            } else {
                update_post_meta( $post_id, $key, sanitize_text_field( $value ) );
            }
        }
    }
} );
