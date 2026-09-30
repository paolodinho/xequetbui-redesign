<?php get_header(); ?>
<div class="wrap page"><header class="ph"><h1>Không tìm thấy trang này</h1><p class="ph__c">Liên kết có thể đã đổi. Hãy tìm sản phẩm bên dưới hoặc gọi <?php echo esc_html(icd('hotline')); ?>.</p></header>
<div class="tiles__g"><?php foreach (icd_top_cats() as $t) : ?><a class="tile" href="<?php echo esc_url(get_term_link($t)); ?>"><?php $tp = icd_products_by_cats([$t->slug], 1)[0] ?? null; if ($tp) : ?><span class="tile__i"><img loading="lazy" src="<?php echo esc_url(icd_img($tp->ID)); ?>" alt=""></span><?php endif; ?><b><?php echo esc_html($t->name); ?></b><small><?php echo (int) $t->count; ?> sản phẩm</small><em>Xem danh mục</em></a><?php endforeach; ?></div></div>
<?php get_footer();
