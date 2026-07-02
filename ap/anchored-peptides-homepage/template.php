<?php
/**
 * Anchored Peptides — Home template (data-driven).
 *
 * Uses the theme's header/footer for a single source of nav + footer.
 * Best-sellers and categories pull live from WooCommerce, so adding products
 * automatically populates the homepage — no hardcoded product names.
 *
 * @package AnchoredPeptides
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Reviewed/edited homepage HTML (set by the generator's brand-config plugin or
 * the provisioner). When present it IS the homepage — a complete, standalone
 * document rendered verbatim, so admin edits to the HTML deploy exactly.
 */
$ap_custom_home = get_option( 'ap_custom_home_html', '' );
if ( is_string( $ap_custom_home ) && '' !== trim( $ap_custom_home ) ) {
	echo $ap_custom_home; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- admin-authored full-page HTML
	exit;
}

get_header();

$shop    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
$learn   = home_url( '/learn/' );
$coa     = home_url( '/coa-library/' );

// Hero image: per-site sideloaded media (option) with the bundled vials as fallback.
$hero_default = function_exists( 'aph_img' ) ? aph_img( 'hero-vials.png' ) : APH_URL . 'images/hero-vials.png';
$hero_id      = (int) get_option( 'ap_hero_image_id' );
$hero         = $hero_id ? ( wp_get_attachment_image_url( $hero_id, 'large' ) ?: wp_get_attachment_image_url( $hero_id, 'full' ) ?: $hero_default ) : $hero_default;

// Brand copy helper (falls back to shipped defaults when a site sets no override).
$c = function_exists( 'ap_copy' ) ? 'ap_copy' : null;
$copy = function ( $key, $default ) use ( $c ) { return $c ? call_user_func( $c, $key, $default ) : $default; };

// Category icons keyed by Natty Vision slug.
$cat_icons = array(
    'weight-loss' => '<path d="M13 2 4 14h7l-1 8 9-12h-7z"/>',
    'energy'      => '<path d="M3 12h4l3-9 4 18 3-9h4"/>',
    'healing'     => '<path d="M12 21s-7-4.5-9-9a5 5 0 0 1 9-3 5 5 0 0 1 9 3c-2 4.5-9 9-9 9z"/>',
    'skin'        => '<path d="M12 3v18M5 8l14 8M19 8 5 16"/>',
    'brain'       => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/>',
    'stacks'      => '<rect x="4" y="13" width="16" height="6" rx="1"/><rect x="6" y="7" width="12" height="5" rx="1"/><rect x="8" y="2" width="8" height="4" rx="1"/>',
);
?>

<!-- ── HERO ── -->
<section class="ap-hero">
    <div class="ap-hero-c">
        <p class="ap-eyebrow"><?php echo esc_html( $copy( 'hero_eyebrow', 'Research-grade quality' ) ); ?></p>
        <h1><?php echo esc_html( $copy( 'hero_h1', 'Peptides That' ) ); ?> <em><?php echo esc_html( $copy( 'hero_h1_em', 'Stay Grounded' ) ); ?></em></h1>
        <p class="ap-hero-sub"><?php echo esc_html( $copy( 'hero_sub', 'Third-party HPLC-tested peptides for serious researchers. Purity you can trust, dispatched same-day from Canada.' ) ); ?></p>
        <div class="ap-hero-btns">
            <a class="ap-btn" href="<?php echo esc_url( $shop ); ?>"><?php echo esc_html( $copy( 'hero_cta_primary', 'Browse Catalog' ) ); ?></a>
            <a class="ap-btn-outline" href="<?php echo esc_url( $learn ); ?>"><?php echo esc_html( $copy( 'hero_cta_secondary', 'Learn More' ) ); ?></a>
        </div>
        <div class="ap-hero-stats">
            <div class="ap-hero-stat"><b><?php echo esc_html( $copy( 'hero_stat1_num', '99.9%' ) ); ?></b><span><?php echo esc_html( $copy( 'hero_stat1_label', 'Purity' ) ); ?></span></div>
            <div class="ap-hero-stat"><b><?php echo esc_html( $copy( 'hero_stat2_num', '20k+' ) ); ?></b><span><?php echo esc_html( $copy( 'hero_stat2_label', 'Researchers' ) ); ?></span></div>
            <div class="ap-hero-stat"><b><?php echo esc_html( $copy( 'hero_stat3_num', '24h' ) ); ?></b><span><?php echo esc_html( $copy( 'hero_stat3_label', 'Dispatch' ) ); ?></span></div>
        </div>
    </div>
    <div class="ap-hero-media">
        <img src="<?php echo esc_url( $hero ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) . ' ' . $copy( 'hero_image_alt', 'research vials' ) ); ?>">
        <span class="ap-hero-badge">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="var(--ap-olive)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.5 8 8 11 4.5-3 8-6 8-11V5Z"/><path d="m9 12 2 2 4-4"/></svg>
            <span><b><?php echo esc_html( $copy( 'hero_badge_title', 'HPLC Verified' ) ); ?></b><br><small style="color:var(--ap-muted)"><?php echo esc_html( $copy( 'hero_badge_sub', 'Batch COA available' ) ); ?></small></span>
        </span>
    </div>
</section>

<!-- ── TRUST BAR ── -->
<section class="ap-trust">
    <div class="ap-trust-i">
        <?php
        $trust = array(
            array( __( 'Third-Party HPLC Tested', 'anchored-peptides' ), __( '99%+ every batch', 'anchored-peptides' ) ),
            array( __( 'Ships From Canada', 'anchored-peptides' ), __( 'No customs delays', 'anchored-peptides' ) ),
            array( __( 'Same-Day Dispatch', 'anchored-peptides' ), __( 'Order before 2 PM ET', 'anchored-peptides' ) ),
            array( __( 'Reship Guarantee', 'anchored-peptides' ), __( 'Full package protection', 'anchored-peptides' ) ),
        );
        foreach ( $trust as $t ) : ?>
            <div class="ap-trust-item">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.5 8 8 11 4.5-3 8-6 8-11V5Z"/><path d="m9 12 2 2 4-4"/></svg>
                <span><b><?php echo esc_html( $t[0] ); ?></b><small><?php echo esc_html( $t[1] ); ?></small></span>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ── BROWSE BY GOAL ── -->
<?php $cats = function_exists( 'ap_homepage_categories' ) ? ap_homepage_categories() : array(); ?>
<?php if ( $cats ) : ?>
<section class="ap-section">
    <div class="ap-section-head">
        <div><p class="ap-eyebrow"><?php esc_html_e( 'Browse by goal', 'anchored-peptides' ); ?></p>
        <h2><?php esc_html_e( 'Find your research', 'anchored-peptides' ); ?> <em><?php esc_html_e( 'category', 'anchored-peptides' ); ?></em></h2></div>
        <a class="ap-link" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'View all →', 'anchored-peptides' ); ?></a>
    </div>
    <div class="ap-cat-grid">
        <?php foreach ( $cats as $cat ) :
            $icon = $cat_icons[ $cat->slug ] ?? '<circle cx="12" cy="12" r="9"/>'; ?>
            <a class="ap-cat-card" href="<?php echo esc_url( ap_shop_cat_url( $cat->slug ) ); ?>">
                <svg class="ap-cat-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><?php echo $icon; // phpcs:ignore ?></svg>
                <div>
                    <h3><?php echo esc_html( $cat->name ); ?></h3>
                    <span><?php printf( esc_html__( '%d products', 'anchored-peptides' ), (int) $cat->count ); ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- ── BEST SELLERS (data-driven) ── -->
<?php $bestsellers = function_exists( 'ap_homepage_products' ) ? ap_homepage_products( 10 ) : array(); ?>
<?php if ( $bestsellers ) : ?>
<section class="ap-section" style="padding-top:0">
    <div class="ap-section-head">
        <div><p class="ap-eyebrow"><?php esc_html_e( 'Weekly top 10', 'anchored-peptides' ); ?></p>
        <h2><?php esc_html_e( 'Best-selling', 'anchored-peptides' ); ?> <em><?php esc_html_e( 'peptides', 'anchored-peptides' ); ?></em></h2></div>
        <a class="ap-link" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Shop all →', 'anchored-peptides' ); ?></a>
    </div>
    <div class="ap-prod-grid">
        <?php foreach ( $bestsellers as $product ) {
            if ( function_exists( 'ap_render_product_card' ) ) ap_render_product_card( $product );
        } ?>
    </div>
    <div style="text-align:center;margin-top:32px">
        <a class="ap-btn" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'See All Products →', 'anchored-peptides' ); ?></a>
    </div>
</section>
<?php endif; ?>

<!-- ── FOUNDER STORY ── -->
<section class="ap-band-dark">
    <div class="ap-band-inner">
        <p class="ap-eyebrow" style="color:var(--ap-cream3)"><?php echo esc_html( $copy( 'founder_eyebrow', 'Why we’re here' ) ); ?></p>
        <h2 style="color:var(--ap-cream)"><?php echo esc_html( $copy( 'founder_h2', 'How We Found Peptides — and Never Looked Back' ) ); ?></h2>
        <p><?php echo esc_html( $copy( 'founder_p1', 'Founded by a husband and wife after having two kids and entering our mid-30s, we found ourselves fighting for energy, stamina, and ways to keep up with the busyness of parenthood.' ) ); ?></p>
        <p><?php echo esc_html( $copy( 'founder_p2', 'We came across peptides through a family friend, did our own research, and never looked back. Our mission is to share the research that helped us, so doing your own research can help you find the same changes — for the better.' ) ); ?></p>
        <p class="ap-band-sign"><?php echo esc_html( $copy( 'tagline', 'Stay true, stay anchored.' ) ); ?> ⚓</p>
    </div>
</section>

<!-- ── THE ANCHORED STANDARD ── -->
<section class="ap-section">
    <div class="ap-section-head">
        <div><p class="ap-eyebrow"><?php esc_html_e( 'The Anchored standard', 'anchored-peptides' ); ?></p>
        <h2><?php esc_html_e( 'Purity you can verify,', 'anchored-peptides' ); ?> <em><?php esc_html_e( 'not just trust.', 'anchored-peptides' ); ?></em></h2></div>
        <a class="ap-link" href="<?php echo esc_url( $coa ); ?>"><?php esc_html_e( 'See our testing process →', 'anchored-peptides' ); ?></a>
    </div>
    <div class="ap-stats">
        <div class="ap-stat"><b>99.9%</b><span><?php esc_html_e( 'Verified purity · HPLC, every lot', 'anchored-peptides' ); ?></span></div>
        <div class="ap-stat"><b>104%</b><span><?php esc_html_e( 'Honest fill · vials filled above label', 'anchored-peptides' ); ?></span></div>
        <div class="ap-stat"><b>24h</b><span><?php esc_html_e( 'Fast dispatch · before 2 PM ET', 'anchored-peptides' ); ?></span></div>
        <div class="ap-stat"><b>100%</b><span><?php esc_html_e( 'Canadian · domestic fulfillment', 'anchored-peptides' ); ?></span></div>
    </div>
</section>

<!-- ── KNOWLEDGE HUB ── -->
<section class="ap-section" style="padding-top:0">
    <div class="ap-section-head"><div><p class="ap-eyebrow"><?php esc_html_e( 'Knowledge hub', 'anchored-peptides' ); ?></p>
        <h2><?php esc_html_e( 'Explore our research', 'anchored-peptides' ); ?> <em><?php esc_html_e( 'resources', 'anchored-peptides' ); ?></em></h2></div></div>
    <div class="ap-res-grid">
        <div class="ap-res-card"><h3><?php esc_html_e( 'Batch-Tested COA Reports', 'anchored-peptides' ); ?></h3><p><?php esc_html_e( 'Look up the exact certificate of analysis for the lot printed on your vial.', 'anchored-peptides' ); ?></p><a class="ap-link" href="<?php echo esc_url( $coa ); ?>"><?php esc_html_e( 'View COAs →', 'anchored-peptides' ); ?></a></div>
        <div class="ap-res-card"><h3><?php esc_html_e( 'Peptide Learning Centre', 'anchored-peptides' ); ?></h3><p><?php esc_html_e( 'Plain-language guides on each compound — sourcing, handling and storage.', 'anchored-peptides' ); ?></p><a class="ap-link" href="<?php echo esc_url( $learn ); ?>"><?php esc_html_e( 'Start learning →', 'anchored-peptides' ); ?></a></div>
        <div class="ap-res-card"><h3><?php esc_html_e( 'Research Resource Library', 'anchored-peptides' ); ?></h3><p><?php esc_html_e( 'Storage, reconstitution and dosing-calculator tools built for the bench.', 'anchored-peptides' ); ?></p><a class="ap-link" href="<?php echo esc_url( $learn ); ?>"><?php esc_html_e( 'Explore tools →', 'anchored-peptides' ); ?></a></div>
    </div>
</section>

<!-- ── REVIEWS ── -->
<section class="ap-band-dark">
    <div class="ap-section" style="padding-top:clamp(48px,7vw,90px);padding-bottom:clamp(48px,7vw,90px)">
        <div style="text-align:center;margin-bottom:28px">
            <p class="ap-stars" style="font-size:18px"><?php echo esc_html( str_repeat( '★', 5 ) ); ?></p>
            <p style="color:var(--ap-cream2);font-size:14px"><?php esc_html_e( '4.9 / 5 from 6,200+ verified researchers', 'anchored-peptides' ); ?></p>
        </div>
        <div class="ap-reviews">
            <div class="ap-review"><p class="ap-stars">★★★★★</p><p><?php esc_html_e( 'COAs match every batch and dispatch is genuinely same-day. The only Canadian source I reorder from.', 'anchored-peptides' ); ?></p><b>Dr. M. Reyes</b><small><?php esc_html_e( 'Verified buyer', 'anchored-peptides' ); ?></small></div>
            <div class="ap-review"><p class="ap-stars">★★★★★</p><p><?php esc_html_e( 'Packaging is clean, labelling is consistent, and the lot lookup actually works. Exactly what research ordering should feel like.', 'anchored-peptides' ); ?></p><b>J. Whitfield</b><small><?php esc_html_e( 'Verified buyer', 'anchored-peptides' ); ?></small></div>
            <div class="ap-review"><p class="ap-stars">★★★★★</p><p><?php esc_html_e( 'Switched over from an overseas vendor. No customs headaches, arrived in three days, exactly as listed.', 'anchored-peptides' ); ?></p><b>A. Kovac</b><small><?php esc_html_e( 'Verified buyer', 'anchored-peptides' ); ?></small></div>
        </div>
    </div>
</section>

<!-- ── NEWSLETTER ── -->
<section class="ap-news">
    <h2><?php esc_html_e( 'Join the research list', 'anchored-peptides' ); ?></h2>
    <p><?php esc_html_e( 'New batch COAs, restocks, and member-only pricing. No noise.', 'anchored-peptides' ); ?></p>
    <form class="ap-news-form" onsubmit="return false;">
        <input type="email" placeholder="you@lab.com" autocomplete="email" aria-label="<?php esc_attr_e( 'Email address', 'anchored-peptides' ); ?>">
        <button type="submit" class="ap-btn"><?php esc_html_e( 'Subscribe', 'anchored-peptides' ); ?></button>
    </form>
</section>

<?php get_footer();
