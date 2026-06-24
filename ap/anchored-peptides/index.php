<?php get_header(); ?>
<main class="ap-section">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <article <?php post_class(); ?>>
        <h2 class="ap-serif" style="font-size:28px;margin-bottom:8px"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <?php the_excerpt(); ?>
    </article>
<?php endwhile; the_posts_pagination(); else : ?>
    <p><?php esc_html_e( 'Nothing here yet.', 'anchored-peptides' ); ?></p>
<?php endif; ?>
</main>
<?php get_footer();
