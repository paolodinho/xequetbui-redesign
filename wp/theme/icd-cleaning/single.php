<?php get_header(); while (have_posts()) : the_post(); ?>
<div class="wrap page"><?php echo icd_breadcrumb(); ?>
<div class="lay"><div class="lay__m lay__m--full">
  <article class="prose prose--box art"><h1><?php the_title(); ?></h1><p class="art__m meta"><time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('d/m/Y')); ?></time></p><?php echo icd_job_chips(get_the_ID()); if (icd_job_info(get_the_ID())['any'] && !icd_job_info(get_the_ID())['expired']) echo '<p class="job__go"><a class="btn btn--hot" href="#bao-gia" data-sp="Ứng tuyển: ' . esc_attr(get_the_title()) . '">Ứng tuyển ngay</a></p>'; ?><div data-toc-after></div><?php the_content(); ?></article>
<?php icd_cta(); ?></div>
<aside class="lay__s"><h4>Sản phẩm nổi bật</h4><div class="side-prods"><?php foreach (get_posts(['post_type' => 'product', 'numberposts' => 4, 'orderby' => 'rand']) as $p) : ?><a href="<?php echo esc_url(get_permalink($p)); ?>"><img loading="lazy" src="<?php echo esc_url(icd_img($p->ID)); ?>" alt=""><span><?php echo esc_html(get_the_title($p)); ?></span></a><?php endforeach; ?></div></aside></div>
<?php icd_related_posts('Bài viết liên quan', get_the_ID()); ?></div>
<?php endwhile; get_footer();
