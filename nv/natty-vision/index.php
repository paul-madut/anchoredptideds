<?php get_header(); ?>
<main class="nv-main"><div class="nv-container">
<?php if(have_posts()): while(have_posts()): the_post(); ?>
<article <?php post_class(); ?>><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><?php the_excerpt(); ?></article>
<?php endwhile; the_posts_pagination(); else: ?><p><?php esc_html_e('Nothing here.','natty-vision'); ?></p><?php endif; ?>
</div></main>
<?php get_footer();
