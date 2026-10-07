<?php get_header();
if (get_query_var('icd_list')) : $sale = get_query_var('icd_list') === 'sale'; ?>
<div class="wrap page"><?php echo icd_breadcrumb(); ?>
<section class="box"><div class="box__h"><h2><?php echo $sale ? 'Khuyến mại trong tháng' : 'Sản phẩm bán chạy'; ?></h2></div>
<?php icd_sortbar(); if (have_posts()) : ?><div class="grid grid--5 grid--box"><?php while (have_posts()) { the_post(); icd_card(); } ?></div><?php the_posts_pagination(['prev_text' => '‹', 'next_text' => '›']); else : ?><p class="ph__c"><?php echo $sale ? 'Hiện chưa có chương trình khuyến mại. Gọi hotline ' . esc_html(icd('hotline')) . ' để nhận ưu đãi mới nhất.' : 'Đang cập nhật.'; ?></p><?php endif; ?></section>
<?php icd_cta(); ?></div>
<?php get_footer(); return; endif; ?>
<div class="wrap page"><?php echo icd_breadcrumb(); ?>
<section class="box"><div class="box__h"><h2>Tất cả sản phẩm</h2></div><p class="ph__c">Chọn danh mục hoặc nhận báo giá trực tiếp từ kỹ thuật viên ICD.</p>
<div class="tiles__g"><?php foreach (icd_top_cats() as $t) : ?><a class="tile" href="<?php echo esc_url(get_term_link($t)); ?>"><?php $tp = icd_products_by_cats([$t->slug], 1)[0] ?? null; if ($tp) : ?><span class="tile__i"><img loading="lazy" src="<?php echo esc_url(icd_img($tp->ID)); ?>" alt=""></span><?php endif; ?><b><?php echo esc_html($t->name); ?></b><small><?php echo (int) $t->count; ?> sản phẩm</small><em>Xem danh mục</em></a><?php endforeach; ?></div></section>
<?php icd_cta(); ?></div>
<?php get_footer();
