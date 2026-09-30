<?php get_header(); $q = get_queried_object(); ?>
<div class="wrap page"><?php echo icd_breadcrumb(); ?>
<header class="ph"><h1><?php echo is_search() ? 'Kết quả tìm kiếm: ' . esc_html(get_search_query()) : (is_category() || is_tag() ? esc_html(single_term_title('', false)) : 'Tin tức'); ?></h1></header>
<?php if (have_posts()) : ?><div class="posts"><?php while (have_posts()) : the_post(); ?>
<?php if (get_post_type() === 'product') : ?><div class="posts__p"><?php icd_card(); ?></div><?php else : ?>
<a class="pc" href="<?php the_permalink(); ?>"><?php $im = icd_img(get_the_ID(), 'medium_large'); if ($im) echo '<img loading="lazy" src="' . esc_url($im) . '" alt="">'; ?><div><span class="news__k"><?php echo esc_html(get_the_category()[0]->name ?? 'Tin tức'); ?></span><h2><?php the_title(); ?></h2><p><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: wp_strip_all_tags(get_the_content()), 32, '…')); ?></p><time><?php echo esc_html(get_the_date('d/m/Y')); ?></time></div></a><?php endif; ?>
<?php endwhile; ?></div><?php the_posts_pagination(['prev_text' => '‹', 'next_text' => '›']); else : ?><p>Không tìm thấy nội dung phù hợp. Hãy gọi hotline <?php echo esc_html(icd('hotline')); ?> để được tư vấn.</p><?php endif; ?></div>
<?php get_footer();
