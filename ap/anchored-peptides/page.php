<?php get_header(); while ( have_posts() ) : the_post(); ?>
<main class="ap-section" style="max-width:820px">
    <article>
        <h1 class="ap-serif" style="font-size:clamp(30px,4vw,44px);margin-bottom:18px"><?php the_title(); ?></h1>
        <div class="ap-tab-panel active"><?php the_content(); ?></div>
    </article>
</main>
<?php endwhile; get_footer();
