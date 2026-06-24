<?php
/**
 * Template Name: Learn / Knowledge Hub
 *
 * Anchored Peptides learning centre landing. Static intro + resource cards +
 * the page's own editable content + an FAQ accordion.
 *
 * @package AnchoredPeptides
 */
defined( 'ABSPATH' ) || exit;
get_header();
$shop = class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>
<main>
    <section class="ap-band-dark">
        <div class="ap-band-inner">
            <p class="ap-eyebrow" style="color:var(--ap-cream3)"><?php esc_html_e( 'Knowledge Hub', 'anchored-peptides' ); ?></p>
            <h2 style="color:var(--ap-cream)"><?php esc_html_e( 'Learn the research before you run it', 'anchored-peptides' ); ?></h2>
            <p><?php esc_html_e( 'Plain-language guides to peptide handling, reconstitution, storage and lab safety — written for researchers, not marketers.', 'anchored-peptides' ); ?></p>
        </div>
    </section>

    <section class="ap-section">
        <div class="ap-learn-cards">
            <div class="ap-learn-card">
                <h3><?php esc_html_e( 'Reconstitution & dosing', 'anchored-peptides' ); ?></h3>
                <p><?php esc_html_e( 'How to reconstitute lyophilized peptides with bacteriostatic water and calculate concentrations.', 'anchored-peptides' ); ?></p>
            </div>
            <div class="ap-learn-card">
                <h3><?php esc_html_e( 'Storage & handling', 'anchored-peptides' ); ?></h3>
                <p><?php esc_html_e( 'Cold-chain best practices, light exposure, and shelf life for lyophilized and reconstituted vials.', 'anchored-peptides' ); ?></p>
            </div>
            <div class="ap-learn-card">
                <h3><?php esc_html_e( 'Reading a COA', 'anchored-peptides' ); ?></h3>
                <p><?php esc_html_e( 'Understand HPLC purity, net content and identity confirmation on every certificate of analysis.', 'anchored-peptides' ); ?></p>
            </div>
        </div>
    </section>

    <?php while ( have_posts() ) : the_post(); if ( get_the_content() ) : ?>
    <section class="ap-section" style="padding-top:0">
        <div class="ap-tab-panel active" style="max-width:760px;margin:0 auto"><?php the_content(); ?></div>
    </section>
    <?php endif; endwhile; ?>

    <section class="ap-band-taupe">
        <div class="ap-band-inner">
            <h2 style="color:var(--ap-cream)"><?php esc_html_e( 'Ready to start your research?', 'anchored-peptides' ); ?></h2>
            <a href="<?php echo esc_url( $shop ); ?>" class="ap-btn" style="margin-top:8px"><?php esc_html_e( 'Browse the catalog →', 'anchored-peptides' ); ?></a>
        </div>
    </section>
</main>
<?php get_footer();
