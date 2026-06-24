<?php
/**
 * Natty Vision — variations generator.
 *
 * Reads `_nv_variants` JSON and creates real WooCommerce variations
 * (with their own SKU, price, and stock) so Kentro can track inventory
 * on each mg independently.
 *
 * @package NattyVision
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AJAX handler — generate variations from JSON for a given product.
 */
add_action( 'wp_ajax_nv_generate_variations', 'nv_ajax_generate_variations' );
function nv_ajax_generate_variations() {
    check_ajax_referer( 'nv_generate_variations', 'nonce' );

    $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
    if ( ! $product_id || ! current_user_can( 'edit_post', $product_id ) ) {
        wp_send_json_error( array( 'message' => 'Permission denied.' ) );
    }

    $variants_raw = isset( $_POST['variants_json'] ) ? wp_unslash( $_POST['variants_json'] ) : '';
    $variants     = json_decode( $variants_raw, true );

    if ( ! is_array( $variants ) || empty( $variants ) ) {
        wp_send_json_error( array( 'message' => 'Variants JSON is empty or invalid.' ) );
    }

    $result = nv_generate_variations_for_product( $product_id, $variants );

    if ( is_wp_error( $result ) ) {
        wp_send_json_error( array( 'message' => $result->get_error_message() ) );
    }

    wp_send_json_success( array(
        'message' => sprintf( '%d variation(s) created/updated successfully.', $result['count'] ),
        'count'   => $result['count'],
    ) );
}

/**
 * Core function — converts product to Variable, creates Strength attribute,
 * and creates/updates a variation per mg in the JSON.
 *
 * @param int   $product_id
 * @param array $variants — array of arrays with keys: mg, price, sku, stock
 * @return array|WP_Error
 */
function nv_generate_variations_for_product( $product_id, $variants ) {
    $product = wc_get_product( $product_id );
    if ( ! $product ) {
        return new WP_Error( 'no_product', 'Product not found.' );
    }

    // Save the JSON to meta if not already there (so the field reflects current state)
    update_post_meta( $product_id, '_nv_variants', wp_json_encode( $variants ) );

    // Step 1 — convert to Variable product if not already.
    $current_type = $product->get_type();
    if ( $current_type !== 'variable' ) {
        wp_set_object_terms( $product_id, 'variable', 'product_type' );
        // Re-fetch as variable.
        $product = wc_get_product( $product_id );
    }

    // Step 2 — set up the Strength attribute with mg values.
    $mg_values = array();
    foreach ( $variants as $v ) {
        if ( isset( $v['mg'] ) ) {
            $mg_values[] = $v['mg'] . 'mg';
        }
    }
    $mg_values = array_unique( $mg_values );

    if ( empty( $mg_values ) ) {
        return new WP_Error( 'no_mg', 'No mg values found in JSON.' );
    }

    $attribute_name = 'Strength';
    $attributes     = $product->get_attributes();

    $attribute_obj = new WC_Product_Attribute();
    $attribute_obj->set_name( $attribute_name );
    $attribute_obj->set_options( $mg_values );
    $attribute_obj->set_position( 0 );
    $attribute_obj->set_visible( true );
    $attribute_obj->set_variation( true );

    $attributes['strength'] = $attribute_obj;
    $product->set_attributes( $attributes );
    $product->save();

    // Step 3 — for each variant in JSON, find or create the matching variation.
    $existing_variations = $product->get_children();
    $created_count       = 0;

    // Build a lookup: mg value => existing variation_id
    $variation_lookup = array();
    foreach ( $existing_variations as $vid ) {
        $variation = wc_get_product( $vid );
        if ( ! $variation ) continue;
        $attr_value = $variation->get_attribute( 'Strength' );
        if ( $attr_value ) {
            $variation_lookup[ strtolower( $attr_value ) ] = $vid;
        }
    }

    foreach ( $variants as $v ) {
        if ( ! isset( $v['mg'] ) ) continue;

        $mg          = $v['mg'];
        $mg_label    = $mg . 'mg';
        $price       = isset( $v['price'] ) ? floatval( $v['price'] ) : 0;
        $sku         = isset( $v['sku'] ) ? sanitize_text_field( $v['sku'] ) : '';
        $stock_qty   = isset( $v['stock'] ) ? intval( $v['stock'] ) : null;

        // SKU fallback — prefix + mg
        if ( ! $sku ) {
            $prefix = get_post_meta( $product_id, '_nv_sku_prefix', true );
            if ( $prefix ) {
                $sku = $prefix . $mg;
            }
        }

        $existing_id = isset( $variation_lookup[ strtolower( $mg_label ) ] ) ? $variation_lookup[ strtolower( $mg_label ) ] : 0;

        if ( $existing_id ) {
            $variation = new WC_Product_Variation( $existing_id );
        } else {
            $variation = new WC_Product_Variation();
            $variation->set_parent_id( $product_id );
            $variation->set_attributes( array( 'strength' => $mg_label ) );
        }

        if ( $price > 0 ) {
            $variation->set_regular_price( $price );
        }
        if ( $sku ) {
            // Only set SKU if it's unique (or already this variation's SKU).
            $existing_sku_id = wc_get_product_id_by_sku( $sku );
            if ( ! $existing_sku_id || $existing_sku_id == $variation->get_id() ) {
                $variation->set_sku( $sku );
            }
        }
        if ( $stock_qty !== null ) {
            $variation->set_manage_stock( true );
            $variation->set_stock_quantity( $stock_qty );
            $variation->set_stock_status( $stock_qty > 0 ? 'instock' : 'outofstock' );
        } else {
            $variation->set_stock_status( 'instock' );
        }

        $variation->save();
        $created_count++;
    }

    // Sync the variable product (recalculates min/max prices, etc.)
    WC_Product_Variable::sync( $product_id );

    return array( 'count' => $created_count );
}
