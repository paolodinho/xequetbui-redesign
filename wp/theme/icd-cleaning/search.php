<?php get_header();
$prods = []; $arts = [];
while (have_posts()) { the_post(); if (get_post_type() === 'product') $prods[] = get_post(); else $arts[] = get_post(); }
$title = is_search() ? 'Kết quả tìm kiếm: ' . esc_html(get_search_query()) : (is_category() || is_tag() ? esc_html(single_term_title('', false)) : 'Tin tức'); ?>
<div class="wrap page"><?php echo icd_breadcrumb(); ?>
<?php if ($prods) : ?><section class="box"><div class="box__h"><h2><?php echo $title; ?></h2></div><div class="grid grid--5"><?php foreach ($prods as $p) icd_card($p); ?></div></section><?php endif; ?>
<?php if ($arts) : ?><section class="box"><?php if (!$prods) : ?><div class="box__h"><h2><?php echo $title; ?></h2></div><?php else : ?><div class="box__h"><h2>Bài viết liên quan</h2></div><?php endif; ?>
<div class="posts"><?php foreach ($arts as $a) : ?>
<a class="pc" href="<?php echo esc_url(get_permalink($a)); ?>"><?php $im = icd_img($a->ID, 'medium_large'); if ($im) echo '<img loading="lazy" src="' . esc_url($im) . '" alt="">'; ?><div><h2><?php echo esc_html(get_the_title($a)); ?></h2><p><?php echo esc_html(wp_trim_words(get_the_excerpt($a) ?: wp_strip_all_tags($a->post_content), 32, '…')); ?></p><time><?php echo esc_html(get_the_date('d/m/Y', $a)); ?></time></div></a>
<?php endforeach; ?></div></section><?php endif; ?>
<?php if (!$prods && !$arts) : ?><section class="box"><div class="box__h"><h2><?php echo $title; ?></h2></div><p>Không tìm thấy nội dung phù hợp. Hãy gọi hotline <?php echo esc_html(icd('hotline')); ?> để được tư vấn.</p></section><?php endif; ?>
<?php the_posts_pagination(['prev_text' => '‹', 'next_text' => '›']); ?>
</div>
<?php get_footer();
