<?php
/**
 * Product card (loop item) — delegates to the shared card renderer.
 * @package AnchoredPeptides
 */
defined( 'ABSPATH' ) || exit;
global $product;
if ( $product ) {
    ap_render_product_card( $product );
}
