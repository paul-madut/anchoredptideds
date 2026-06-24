<?php get_header(); while(have_posts()): the_post(); ?>
<main class="nv-main"><div class="nv-container"><article <?php post_class(); ?>><h1><?php the_title(); ?></h1><div><?php the_content(); ?></div></article></div></main>
<?php endwhile; get_footer();
