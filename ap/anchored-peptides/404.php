<?php get_header(); ?>
<main class="ap-section" style="text-align:center;max-width:560px">
    <h1 class="ap-serif" style="font-size:clamp(34px,5vw,52px);margin-bottom:12px"><?php esc_html_e( 'Page not found', 'anchored-peptides' ); ?></h1>
    <p style="color:var(--ap-muted);margin-bottom:22px"><?php esc_html_e( 'The page you’re looking for has drifted off. Let’s get you back to dry land.', 'anchored-peptides' ); ?></p>
    <a class="ap-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back home', 'anchored-peptides' ); ?></a>
</main>
<?php get_footer();
