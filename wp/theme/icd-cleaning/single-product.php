<?php get_header(); while (have_posts()) : the_post(); $id = get_the_ID(); $img = icd_img($id, 'full') ?: icd_img($id);
$cats = get_the_terms($id, 'product_cat'); $cat = $cats ? $cats[0] : null; ?>
<div class="wrap page"><?php echo icd_breadcrumb(); ?>
<article class="pd">
  <div class="pd__top">
    <div class="pd__l">    <div class="pd__g"><?php if ($img) : ?><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(get_the_title()); ?>"><?php endif; ?></div></div>
    <div class="pd__i">
      <h1><?php the_title(); ?></h1>
      <div class="pd__p"><?php echo icd_price_html($id); echo icd_badges($id); ?></div>
      <?php if (has_excerpt()) : ?><div class="pd__sum"><?php echo wp_kses_post(wpautop(get_the_excerpt() ? get_post_field('post_excerpt', $id) : '')); ?></div><?php endif; ?>
      <div class="pd__cta"><a class="btn" href="#bao-gia" data-sp="<?php echo esc_attr(get_the_title()); ?>">Nhận báo giá</a><a class="btn btn--ghost" href="tel:<?php echo esc_attr(icd('hotline_tel')); ?>">Gọi <?php echo esc_html(icd('hotline')); ?></a><button class="btn btn--q" type="button" data-addq="<?php echo (int) $id; ?>" data-t="<?php echo esc_attr(get_the_title()); ?>"><?php echo icd_ico('cart'); ?> Thêm vào giỏ báo giá</button></div>
      <?php icd_promo_box($id); icd_callback(); ?>
      <ul class="pd__u"><li><i><?php echo icd_ico('ok'); ?></i>Hàng chính hãng, đầy đủ CO CQ</li><li><i><?php echo icd_ico('truck'); ?></i>Giao hàng, lắp đặt, hướng dẫn tại chỗ</li><li><i><?php echo icd_ico('shield'); ?></i>Bảo hành, bảo dưỡng và phụ tùng thay thế</li></ul>
      <?php if ($cat) echo '<p class="pd__m">Danh mục: <a href="' . esc_url(get_term_link($cat)) . '">' . esc_html($cat->name) . '</a></p>'; ?>
    </div>
  </div>
  <nav class="tabs" aria-label="Mục trong trang"><a href="#chi-tiet">Thông tin chi tiết</a><a href="#cung-loai">Sản phẩm cùng loại</a><a href="#tu-van">Tư vấn liên quan</a><a href="#bao-gia">Nhận báo giá</a></nav>
  <div class="prose prose--box prose--pd" id="chi-tiet"><h2>Thông tin chi tiết</h2><div data-toc-after></div><?php the_content(); $yt = get_post_meta($id, '_icd_youtube', true); if ($yt) { $vid = preg_match('~(?:v=|youtu\.be/|embed/)([\w-]{11})~', $yt, $m) ? $m[1] : ''; if ($vid) echo '<h2>Video giới thiệu</h2><iframe loading="lazy" src="https://www.youtube.com/embed/' . esc_attr($vid) . '" title="Video sản phẩm" allowfullscreen></iframe>'; } ?></div>
</article>
<?php if ($cat) { $rel = get_posts(['post_type' => 'product', 'numberposts' => 5, 'post__not_in' => [$id], 'tax_query' => [['taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $cat->term_id]]]);
if ($rel) : ?><section class="block" id="cung-loai"><div class="block__h"><h2>Sản phẩm cùng loại</h2><a class="more" href="<?php echo esc_url(get_term_link($cat)); ?>">Xem tất cả</a></div><div class="block__b block__b--full"><div class="grid grid--5"><?php foreach ($rel as $p) icd_card($p); ?></div></div></section><?php endif; } ?>
<?php icd_cta(); ?>
<div id="tu-van"><?php icd_related_posts('Bài tư vấn liên quan'); ?></div>
</div>
<?php endwhile; get_footer();
