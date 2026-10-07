<?php get_header();
$prods = []; $arts = [];
while (have_posts()) { the_post(); if (get_post_type() === 'product') $prods[] = get_post(); else $arts[] = get_post(); }
$title = is_search() ? 'Kết quả tìm kiếm: ' . esc_html(get_search_query()) : (is_category() || is_tag() ? esc_html(single_term_title('', false)) : 'Tin tức'); ?>
<div class="wrap page"><?php echo icd_breadcrumb(); ?>
<?php if ($prods) : ?><section class="box"><div class="box__h"><h2><?php echo $title; ?></h2></div><div class="grid grid--5"><?php foreach ($prods as $p) icd_card($p); ?></div></section><?php endif; ?>
<?php if ($arts && !$prods) : ?><div class="lay"><div class="lay__m lay__m--full"><div class="box__h"><h2><?php echo $title; ?></h2></div>
<div class="posts posts--1"><?php foreach ($arts as $a) : ?>
<a class="pc" href="<?php echo esc_url(get_permalink($a)); ?>"><?php $im = icd_img($a->ID, 'medium_large'); echo $im ? '<img loading="lazy" src="' . esc_url($im) . '" alt="">' : '<span class="pc__ph"></span>'; ?><div><h2><?php echo esc_html(get_the_title($a)); ?></h2><time>Ngày cập nhật: <?php echo esc_html(get_the_modified_date('d/m/Y', $a)); ?></time><?php echo icd_job_chips($a->ID); ?><p><?php echo esc_html(wp_trim_words(get_the_excerpt($a) ?: wp_strip_all_tags($a->post_content), 40, '…')); ?></p><span class="pc__more">Đọc tiếp ›</span></div></a>
<?php endforeach; ?></div>
<?php the_posts_pagination(['prev_text' => '‹', 'next_text' => '›']); ?></div>
<aside class="lay__s"><h4>Bài viết nổi bật</h4><div class="side-posts"><?php foreach (get_posts(icd_knowledge_args(6, ['orderby' => 'rand'])) as $sp) : $si = icd_img($sp->ID, 'thumbnail'); ?><a href="<?php echo esc_url(get_permalink($sp)); ?>"><?php if ($si) echo '<img loading="lazy" src="' . esc_url($si) . '" alt="">'; ?><span><?php echo esc_html(get_the_title($sp)); ?></span></a><?php endforeach; ?></div>
<h4 class="lay__h2">Sản phẩm nổi bật</h4><div class="side-prods"><?php foreach (get_posts(['post_type' => 'product', 'numberposts' => 4, 'orderby' => 'rand']) as $p) : ?><a href="<?php echo esc_url(get_permalink($p)); ?>"><img loading="lazy" src="<?php echo esc_url(icd_img($p->ID)); ?>" alt=""><span><?php echo esc_html(get_the_title($p)); ?></span></a><?php endforeach; ?></div></aside></div>
<?php $paged_done = true; endif; ?>
<?php if ($arts && empty($paged_done)) : ?><section class="box"><?php if (!$prods) : ?><div class="box__h"><h2><?php echo $title; ?></h2></div><?php else : ?><div class="box__h"><h2>Bài viết liên quan</h2></div><?php endif; ?>
<div class="posts"><?php foreach ($arts as $a) : ?>
<a class="pc" href="<?php echo esc_url(get_permalink($a)); ?>"><?php $im = icd_img($a->ID, 'medium_large'); if ($im) echo '<img loading="lazy" src="' . esc_url($im) . '" alt="">'; ?><div><h2><?php echo esc_html(get_the_title($a)); ?></h2><p><?php echo esc_html(wp_trim_words(get_the_excerpt($a) ?: wp_strip_all_tags($a->post_content), 32, '…')); ?></p><time><?php echo esc_html(get_the_date('d/m/Y', $a)); ?></time></div></a>
<?php endforeach; ?></div></section><?php endif; ?>
<?php if (!$prods && !$arts && empty($paged_done)) : ?><section class="box"><div class="box__h"><h2><?php echo $title; ?></h2></div><p>Không tìm thấy nội dung phù hợp. Hãy gọi hotline <?php echo esc_html(icd('hotline')); ?> để được tư vấn.</p></section><?php endif; ?>
<?php if (empty($paged_done)) the_posts_pagination(['prev_text' => '‹', 'next_text' => '›']); ?>
</div>
<?php get_footer();
