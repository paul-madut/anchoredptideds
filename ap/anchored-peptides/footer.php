<?php
/**
 * Anchored Peptides footer.
 * @package AnchoredPeptides
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$ap_shop = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>
<footer class="ap-footer">
    <div class="ap-footer-i">
        <div class="ap-footer-brand">
            <?php echo ap_render_logo( 30 ); ?>
            <p><?php echo esc_html( get_theme_mod( 'ap_footer_blurb', __( 'Canada’s source for third-party HPLC-tested research peptides. Shipped same-day from our Canadian fulfillment centre. For laboratory & research use only. Not for human consumption.', 'anchored-peptides' ) ) ); ?></p>
        </div>

        <div class="ap-footer-col">
            <h4><?php esc_html_e( 'Shop', 'anchored-peptides' ); ?></h4>
            <ul>
                <li><a href="<?php echo esc_url( $ap_shop ); ?>"><?php esc_html_e( 'All Peptides', 'anchored-peptides' ); ?></a></li>
                <li><a href="<?php echo esc_url( add_query_arg( 'orderby', 'popularity', $ap_shop ) ); ?>"><?php esc_html_e( 'Best Sellers', 'anchored-peptides' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/shop/?category=stacks' ) ); ?>"><?php esc_html_e( 'Stacks', 'anchored-peptides' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/shop/?category=weight-loss' ) ); ?>"><?php esc_html_e( 'Weight Loss', 'anchored-peptides' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/shop/?category=supplies' ) ); ?>"><?php esc_html_e( 'Supplies', 'anchored-peptides' ); ?></a></li>
            </ul>
        </div>

        <div class="ap-footer-col">
            <h4><?php esc_html_e( 'Learn', 'anchored-peptides' ); ?></h4>
            <ul>
                <li><a href="<?php echo esc_url( home_url( '/learn/' ) ); ?>"><?php esc_html_e( 'Peptide Learning Centre', 'anchored-peptides' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/coa-library/' ) ); ?>"><?php esc_html_e( 'COA Lookup', 'anchored-peptides' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/learn/#dosing' ) ); ?>"><?php esc_html_e( 'Dosing Calculator', 'anchored-peptides' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/learn/#storage' ) ); ?>"><?php esc_html_e( 'Storage & Handling', 'anchored-peptides' ); ?></a></li>
            </ul>
        </div>

        <div class="ap-footer-col">
            <h4><?php esc_html_e( 'Support', 'anchored-peptides' ); ?></h4>
            <ul>
                <li><a href="<?php echo esc_url( home_url( '/shipping/' ) ); ?>"><?php esc_html_e( 'Shipping & Delivery', 'anchored-peptides' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/payment/' ) ); ?>"><?php esc_html_e( 'Payment Instructions', 'anchored-peptides' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/returns/' ) ); ?>"><?php esc_html_e( 'Returns & Reships', 'anchored-peptides' ); ?></a></li>
                <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact Us', 'anchored-peptides' ); ?></a></li>
            </ul>
        </div>
    </div>
    <div class="ap-footer-bottom">
        <div class="ap-footer-bottom-i">
            <span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'anchored-peptides' ); ?></span>
            <span><?php esc_html_e( 'Interac e-Transfer · Same-day dispatch · Canada-wide', 'anchored-peptides' ); ?></span>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
