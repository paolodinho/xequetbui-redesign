<?php get_header(); while (have_posts()) : the_post(); ?>
<div class="wrap page"><?php echo icd_breadcrumb(); ?>
<article class="prose prose--box art"><h1><?php the_title(); ?></h1><?php the_content(); ?></article></div>
<?php endwhile; get_footer();
