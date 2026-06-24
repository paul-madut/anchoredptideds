<?php
/**
 * Anchored Peptides — variations generator.
 *
 * Reads `_ap_variants` JSON and creates real WooCommerce variations (with their
 * own SKU, price and stock) so inventory can be tracked per strength.
 *
 * @package AnchoredPeptides
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_ajax_ap_generate_variations', 'ap_ajax_generate_variations' );
function ap_ajax_generate_variations() {
    check_ajax_referer( 'ap_generate_variations', 'nonce' );
    $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
    if ( ! $product_id || ! current_user_can( 'edit_post', $product_id ) ) {
        wp_send_json_error( array( 'message' => 'Permission denied.' ) );
    }
    $variants = json_decode( isset( $_POST['variants_json'] ) ? wp_unslash( $_POST['variants_json'] ) : '', true );
    if ( ! is_array( $variants ) || empty( $variants ) ) {
        wp_send_json_error( array( 'message' => 'Variants JSON is empty or invalid.' ) );
    }
    $result = ap_generate_variations_for_product( $product_id, $variants );
    if ( is_wp_error( $result ) ) wp_send_json_error( array( 'message' => $result->get_error_message() ) );
    wp_send_json_success( array( 'message' => sprintf( '%d variation(s) created/updated.', $result['count'] ), 'count' => $result['count'] ) );
}

/**
 * Converts product to Variable, creates a Strength attribute, and creates/updates
 * a variation per strength in the JSON.
 *
 * @param int   $product_id
 * @param array $variants  array of arrays with keys: mg, price, sku, stock
 * @return array|WP_Error
 */
function ap_generate_variations_for_product( $product_id, $variants ) {
    $product = wc_get_product( $product_id );
    if ( ! $product ) return new WP_Error( 'no_product', 'Product not found.' );

    update_post_meta( $product_id, '_ap_variants', wp_json_encode( $variants ) );

    if ( $product->get_type() !== 'variable' ) {
        wp_set_object_terms( $product_id, 'variable', 'product_type' );
        $product = wc_get_product( $product_id );
    }

    $unit      = ap_meta( $product_id, '_ap_unit', 'mg' );
    $mg_values = array();
    foreach ( $variants as $v ) {
        if ( isset( $v['mg'] ) ) $mg_values[] = $v['mg'] . $unit;
    }
    $mg_values = array_unique( $mg_values );
    if ( empty( $mg_values ) ) return new WP_Error( 'no_mg', 'No strength values found in JSON.' );

    $attribute = new WC_Product_Attribute();
    $attribute->set_name( 'Strength' );
    $attribute->set_options( $mg_values );
    $attribute->set_position( 0 );
    $attribute->set_visible( true );
    $attribute->set_variation( true );

    $attributes             = $product->get_attributes();
    $attributes['strength'] = $attribute;
    $product->set_attributes( $attributes );
    $product->save();

    $lookup = array();
    foreach ( $product->get_children() as $vid ) {
        $variation  = wc_get_product( $vid );
        if ( ! $variation ) continue;
        $attr_value = $variation->get_attribute( 'Strength' );
        if ( $attr_value ) $lookup[ strtolower( $attr_value ) ] = $vid;
    }

    $count = 0;
    foreach ( $variants as $v ) {
        if ( ! isset( $v['mg'] ) ) continue;
        $label = $v['mg'] . $unit;
        $price = isset( $v['price'] ) ? floatval( $v['price'] ) : 0;
        $sku   = isset( $v['sku'] ) ? sanitize_text_field( $v['sku'] ) : '';
        $stock = isset( $v['stock'] ) ? ( is_numeric( $v['stock'] ) ? intval( $v['stock'] ) : $v['stock'] ) : null;

        if ( ! $sku ) {
            $prefix = ap_meta( $product_id, '_ap_sku_prefix' ); // ap_meta() honors a migrated _nv_ prefix
            if ( $prefix ) $sku = $prefix . $v['mg'];
        }

        $existing = isset( $lookup[ strtolower( $label ) ] ) ? $lookup[ strtolower( $label ) ] : 0;
        $variation = $existing ? new WC_Product_Variation( $existing ) : new WC_Product_Variation();
        if ( ! $existing ) {
            $variation->set_parent_id( $product_id );
            $variation->set_attributes( array( 'strength' => $label ) );
        }
        if ( $price > 0 ) $variation->set_regular_price( $price );
        if ( $sku ) {
            $sku_owner = wc_get_product_id_by_sku( $sku );
            if ( ! $sku_owner || $sku_owner == $variation->get_id() ) $variation->set_sku( $sku );
        }
        if ( is_int( $stock ) ) {
            $variation->set_manage_stock( true );
            $variation->set_stock_quantity( $stock );
            $variation->set_stock_status( $stock > 0 ? 'instock' : 'outofstock' );
        } elseif ( $stock === 'out' ) {
            $variation->set_stock_status( 'outofstock' );
        } elseif ( $stock === 'low' || $stock === 'onbackorder' ) {
            $variation->set_stock_status( 'onbackorder' );
        } else {
            $variation->set_stock_status( 'instock' );
        }
        $variation->save();
        $count++;
    }

    WC_Product_Variable::sync( $product_id );
    return array( 'count' => $count );
}
