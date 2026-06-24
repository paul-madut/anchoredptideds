<?php
/**
 * Anchored Peptides — per-product editable meta box.
 *
 * Adds a panel under the product description with the per-product fields used
 * by the single-product template. No ACF dependency.
 *
 * @package AnchoredPeptides
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'add_meta_boxes', function () {
    add_meta_box( 'ap_product_meta', __( 'Anchored Peptides Product', 'anchored-peptides' ), 'ap_product_meta_box', 'product', 'normal', 'high' );
} );

function ap_product_meta_box( $post ) {
    wp_nonce_field( 'ap_product_meta', 'ap_product_meta_nonce' );
    $fields = ap_meta_field_definitions();
    echo '<style>
        .ap-meta-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:8px;}
        .ap-meta-field{display:flex;flex-direction:column;gap:6px;}
        .ap-meta-field label{font-weight:600;font-size:13px;color:#2C2E22;}
        .ap-meta-field code{display:inline-block;background:#EDE7D9;color:#3E412E;padding:1px 6px;border-radius:3px;font-size:11px;}
        .ap-meta-field input[type=text],.ap-meta-field textarea,.ap-meta-field select{width:100%;padding:8px 10px;border:1px solid #DCD5C4;border-radius:6px;font-size:13px;}
        .ap-meta-field textarea{font-family:inherit;min-height:80px;resize:vertical;}
        .ap-meta-field .description{font-size:12px;color:#8A8676;margin:0;line-height:1.5;}
        .ap-meta-section{grid-column:1/-1;border-top:1px solid #e5e5e5;padding-top:16px;margin-top:8px;}
        .ap-meta-section h3{margin:0 0 4px;font-size:14px;}
        .ap-meta-section p{margin:0;color:#8A8676;font-size:12px;}
        .ap-gen-btn{background:#3E412E;color:#fff;border:0;padding:10px 18px;border-radius:6px;font-size:13px;font-weight:500;cursor:pointer;margin-top:8px;}
        .ap-gen-btn:hover{background:#33352A;} .ap-gen-btn:disabled{opacity:.6;cursor:wait;}
        .ap-gen-status{margin-top:8px;padding:10px 12px;border-radius:6px;font-size:13px;display:none;}
        .ap-gen-status.success{background:#d4edda;color:#155724;display:block;}
        .ap-gen-status.error{background:#f8d7da;color:#721c24;display:block;}
    </style>';
    echo '<div class="ap-meta-grid">';
    foreach ( $fields as $field ) {
        if ( isset( $field['section'] ) ) {
            echo '<div class="ap-meta-section"><h3>' . esc_html( $field['section'] ) . '</h3>';
            if ( ! empty( $field['section_desc'] ) ) echo '<p>' . esc_html( $field['section_desc'] ) . '</p>';
            echo '</div>';
            continue;
        }
        // ap_meta() falls back to the matching _nv_ key, so a migrated NV product
        // shows its existing values in the editor (a plain save then persists them
        // into the _ap_ keys, completing the migration).
        $value = ap_meta( $post->ID, $field['key'] );
        echo '<div class="ap-meta-field">';
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
        if ( ! empty( $field['desc'] ) ) echo '<p class="description">' . wp_kses_post( $field['desc'] ) . '</p>';
        echo '</div>';
    }
    echo '</div>';

    $ajax_nonce = wp_create_nonce( 'ap_generate_variations' );
    ?>
    <div class="ap-meta-section" style="margin-top:24px;">
        <h3><?php esc_html_e( 'Generate WooCommerce variations', 'anchored-peptides' ); ?></h3>
        <p><?php esc_html_e( 'After filling in the Variants JSON above, click to convert this product to a Variable Product and create a real WooCommerce variation per strength with its own SKU, price and stock.', 'anchored-peptides' ); ?></p>
        <button type="button" class="ap-gen-btn" id="ap-gen-btn"><?php esc_html_e( '⚓ Generate variations from JSON', 'anchored-peptides' ); ?></button>
        <div class="ap-gen-status" id="ap-gen-status"></div>
    </div>
    <script>
    (function () {
        var btn = document.getElementById('ap-gen-btn'), status = document.getElementById('ap-gen-status');
        if (!btn) return;
        btn.addEventListener('click', function () {
            var f = document.getElementById('_ap_variants'); if (!f) return;
            var v = f.value.trim();
            if (!v) { status.className='ap-gen-status error'; status.textContent='Variants JSON is empty.'; return; }
            try { JSON.parse(v); } catch(e){ status.className='ap-gen-status error'; status.textContent='Invalid JSON: '+e.message; return; }
            btn.disabled=true; status.className='ap-gen-status'; status.textContent=''; btn.textContent='Generating…';
            var data=new FormData();
            data.append('action','ap_generate_variations');
            data.append('nonce','<?php echo esc_js( $ajax_nonce ); ?>');
            data.append('product_id','<?php echo (int) $post->ID; ?>');
            data.append('variants_json', v);
            fetch(ajaxurl,{method:'POST',body:data,credentials:'same-origin'}).then(function(r){return r.json();}).then(function(resp){
                btn.disabled=false; btn.textContent='⚓ Generate variations from JSON';
                if(resp&&resp.success){status.className='ap-gen-status success';status.textContent=resp.data.message+' Refresh to see them in the Variations tab.';}
                else{status.className='ap-gen-status error';status.textContent=(resp&&resp.data&&resp.data.message)?resp.data.message:'Something went wrong.';}
            }).catch(function(err){btn.disabled=false;btn.textContent='⚓ Generate variations from JSON';status.className='ap-gen-status error';status.textContent='Request failed: '+err.message;});
        });
    })();
    </script>
    <?php
}

/**
 * Single source of truth for the editable field set — the template reads the same keys.
 */
function ap_meta_field_definitions() {
    return array(
        array( 'section' => 'Header & badges', 'section_desc' => 'These show above and around the product title.' ),
        array( 'key' => '_ap_badge', 'label' => 'Card badge', 'type' => 'text', 'placeholder' => 'Best seller', 'desc' => 'e.g. <em>Best seller</em>, <em>New</em>, <em>Sale</em>. Leave blank to hide.' ),
        array( 'key' => '_ap_eyebrow', 'label' => 'Eyebrow text', 'type' => 'text', 'placeholder' => 'Healing & Recovery', 'desc' => 'Small uppercase tagline above title. Falls back to product category.' ),
        array( 'key' => '_ap_title_em', 'label' => 'Italic accent word', 'type' => 'text', 'placeholder' => '', 'desc' => 'A word shown in italic serif in the title. If the title already ends with this word it is italicized in place; otherwise appended.' ),
        array( 'key' => '_ap_tagline', 'label' => 'Tagline (under title)', 'type' => 'textarea', 'placeholder' => 'A synthetic peptide fragment studied for tissue repair and recovery research.', 'desc' => 'Falls back to short description if blank.', 'rows' => 2 ),

        array( 'section' => 'Spec pills', 'section_desc' => 'Up to 3 small pills under the description. Plain text.' ),
        array( 'key' => '_ap_spec_1', 'label' => 'Spec pill 1', 'type' => 'text', 'placeholder' => 'HPLC tested for purity' ),
        array( 'key' => '_ap_spec_2', 'label' => 'Spec pill 2', 'type' => 'text', 'placeholder' => '24/7 support' ),
        array( 'key' => '_ap_spec_3', 'label' => 'Spec pill 3', 'type' => 'text', 'placeholder' => 'Ships from Canada', 'desc' => 'Leave blank to inherit the site-wide pill set from <strong>Customizer → Anchored Peptides — General</strong>.' ),

        array( 'section' => 'Variants & pricing', 'section_desc' => 'Define strength + price for each pill. JSON array.' ),
        array( 'key' => '_ap_unit', 'label' => 'Strength unit', 'type' => 'select', 'options' => array( 'mg', 'ml', 'iu', 'mcg', 'g' ), 'default' => 'mg', 'desc' => 'Unit shown on the size pills.' ),
        array( 'key' => '_ap_variants', 'label' => 'Variants JSON', 'type' => 'textarea', 'placeholder' => '[{"mg":10,"price":92.99,"label":"Single vial"},{"mg":20,"price":144.13,"label":"Single vial"},{"mg":100,"price":799.71,"label":"1 box · best value","note":"Save 7%"}]', 'desc' => 'Each: <code>{"mg":number,"price":number,"label":"text","note":"badge","sku":"","stock":number}</code>. Stock can be <em>in</em>, <em>out</em>, <em>low</em> or a number. Leave blank to use the regular product price.', 'rows' => 4 ),
        array( 'key' => '_ap_sku_prefix', 'label' => 'SKU prefix', 'type' => 'text', 'placeholder' => 'BPC', 'desc' => 'Generates SKU like BPC10 from prefix + strength.' ),
        array( 'key' => '_ap_price_suffix', 'label' => 'Price suffix', 'type' => 'text', 'placeholder' => 'CAD · per vial' ),

        array( 'section' => 'Certificate of Analysis', 'section_desc' => 'Powers the Verify button + the COA Library card. Paste the lab verify link to make this product appear in the library.' ),
        array( 'key' => '_ap_coa_url', 'label' => 'Lab verify URL', 'type' => 'text', 'placeholder' => 'https://lab.example/verify?t=...', 'desc' => '<strong>Required</strong> for the product to show in the COA Library.' ),
        array( 'key' => '_ap_coa_lot', 'label' => 'Batch / lot number', 'type' => 'text', 'placeholder' => 'AP-2043' ),
        array( 'key' => '_ap_coa_purity', 'label' => 'Verified purity %', 'type' => 'text', 'placeholder' => '99.4', 'desc' => 'Number only — the % is added automatically.' ),
        array( 'key' => '_ap_coa_tested', 'label' => 'Date tested', 'type' => 'text', 'placeholder' => 'Jun 3, 2026' ),

        array( 'section' => 'Tab content', 'section_desc' => 'Optional override HTML for each tab. Leave blank to hide (Details always shows).' ),
        array( 'key' => '_ap_specs_html', 'label' => 'Specifications tab', 'type' => 'textarea', 'rows' => 4 ),
        array( 'key' => '_ap_storage_html', 'label' => 'Storage tab', 'type' => 'textarea', 'rows' => 4 ),

        array( 'section' => 'Disclaimer', 'section_desc' => 'Per-product override of the default research-use-only disclaimer.' ),
        array( 'key' => '_ap_disclaimer', 'label' => 'Disclaimer override', 'type' => 'textarea', 'desc' => 'Leave blank to use the global theme disclaimer.', 'rows' => 3 ),
    );
}

add_action( 'save_post_product', function ( $post_id ) {
    if ( ! isset( $_POST['ap_product_meta_nonce'] ) || ! wp_verify_nonce( $_POST['ap_product_meta_nonce'], 'ap_product_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    foreach ( ap_meta_field_definitions() as $field ) {
        if ( empty( $field['key'] ) ) continue;
        $key = $field['key'];
        if ( ! isset( $_POST[ $key ] ) ) continue;
        $value = wp_unslash( $_POST[ $key ] );
        if ( in_array( $key, array( '_ap_spec_1', '_ap_spec_2', '_ap_spec_3', '_ap_specs_html', '_ap_storage_html', '_ap_tagline', '_ap_disclaimer' ), true ) ) {
            update_post_meta( $post_id, $key, wp_kses_post( $value ) );
        } elseif ( $key === '_ap_variants' ) {
            update_post_meta( $post_id, $key, sanitize_textarea_field( $value ) );
        } else {
            update_post_meta( $post_id, $key, sanitize_text_field( $value ) );
        }
    }
} );
