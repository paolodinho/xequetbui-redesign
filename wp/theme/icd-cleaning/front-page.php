<?php get_header();
$hero = icd('hero_product') ? get_post((int) icd('hero_product')) : (icd_products_by_cats(['may-lau-san-cha-san-nha-xuong-cong-nghiep'], 1)[0] ?? null);
$s1 = icd('side1_product') ? get_post((int) icd('side1_product')) : (icd_products_by_cats(['xe-quet-rac'], 1)[0] ?? $hero);
$s2 = icd('side2_product') ? get_post((int) icd('side2_product')) : (icd_products_by_cats(['may-hut-bui-cong-nghiep-nha-xuong-cong-suat-lon'], 1)[0] ?? $hero);
$cats = icd_top_cats();
?>
<section class="bn"><div class="wrap bn__g">
  <aside class="bn__cats"><ul>
    <?php echo icd_cats_menu(); ?>
  </ul></aside>
  <div class="bn__img">
    <div class="bn__txt">
      <h1><?php echo icd_nb(icd('hero_title')); ?></h1>
      <p class="bn__tag"><?php echo esc_html(icd('hero_text')); ?></p>
      <div class="bn__a"><a class="btn" href="#bao-gia"><?php echo esc_html(icd('hero_btn1')); ?></a><a class="btn btn--wl" href="<?php echo esc_url(home_url(icd('hero_btn2_url'))); ?>"><?php echo esc_html(icd('hero_btn2')); ?></a></div>
    </div>
    <div class="bn__pics bn__pics--g">
      <?php foreach ([['q1', 14632], ['q2', 17443], ['q3', 17511], ['q4', 17475], ['q5', 17292], ['q6', 17631]] as [$qc, $tid]) {
          $pp = get_posts(['post_type' => 'product', 'post_status' => 'publish', 'numberposts' => 1, 'meta_key' => '_thumbnail_id', 'meta_value' => $tid]);
          if ($pp) echo '<a class="' . $qc . '" href="' . esc_url(get_permalink($pp[0])) . '" title="' . esc_attr(get_the_title($pp[0])) . '"><img src="' . esc_url(icd_cutout($pp[0]->ID)) . '" alt="' . esc_attr(get_the_title($pp[0])) . '"></a>';
      } ?>
    </div>
  </div>
</div></section>

<div class="mb"><div class="wrap">
<?php $hid = icd_hot_ids(10); if (!$hid) $hid = get_posts(['post_type' => 'product', 'numberposts' => 10, 'fields' => 'ids', 'orderby' => 'menu_order date']);
if ($hid) : ?>
<section class="box box--hot" id="ban-chay">
  <div class="box__h"><h2>Sản phẩm bán chạy</h2><a class="more" href="<?php echo esc_url(home_url('/san-pham-ban-chay.html')); ?>">Xem thêm</a></div>
  <div class="grid grid--5"><?php foreach (array_slice($hid, 0, 5) as $pid) icd_card($pid); ?></div>
  <?php if (count($hid) > 5) : ?><div class="grid grid--5 grid--gap"><?php foreach (array_slice($hid, 5, 5) as $pid) icd_card($pid); ?></div><?php endif; ?>
</section>
<?php endif; $sid = icd_sale_ids(); if ($sid) : ?>
<section class="box box--sale" id="khuyen-mai">
  <div class="box__h"><h2>Khuyến mại trong tháng</h2><a class="more" href="<?php echo esc_url(home_url('/san-pham-khuyen-mai.html')); ?>">Xem thêm</a></div>
  <div class="grid grid--5"><?php foreach (array_slice($sid, 0, 5) as $pid) icd_card($pid); ?></div>
</section>
<?php endif; ?>
<?php foreach (preg_split('/\R/u', trim(icd('rows'))) as $n => $line) {
    if (!trim($line)) continue;
    [$slugs, $title, $kick, $bh, $cnt, $bimg] = array_pad(explode('|', $line), 6, '');
    $short = (int) $cnt <= 5 && file_exists(get_template_directory() . '/assets/img/banner-' . ($n + 1) . '-s.jpg');
    if (!trim($bimg) && file_exists(get_template_directory() . '/assets/img/banner-' . ($n + 1) . '.jpg')) { $bf = '/assets/img/banner-' . ($n + 1) . ($short ? '-s' : '') . '.jpg'; $bimg = get_template_directory_uri() . $bf . '?v=' . filemtime(get_template_directory() . $bf); }
    $slugs = array_map('trim', explode(',', $slugs)); $ps = icd_products_by_cats($slugs, max(5, (int) $cnt)); if (!$ps) continue;
    $first = get_term_by('slug', $slugs[0], 'product_cat'); $more = $first ? get_term_link($first) : home_url('/danh-muc-san-pham.html'); ?>
<section class="box" id="row-<?php echo (int) $n; ?>">
  <div class="box__h"><h2><?php echo esc_html($title); ?></h2><a class="more" href="<?php echo esc_url($more); ?>">Xem thêm</a></div>
  <div class="box__b">
    <a class="promo promo--c" href="#bao-gia"><span class="promo__t"><?php echo esc_html($title); ?></span><strong><?php echo icd_chunks($bh); ?></strong><span class="promo__pic"><img loading="lazy" src="<?php echo esc_url(icd_cutout($ps[0]->ID)); ?>" alt="<?php echo esc_attr(get_the_title($ps[0])); ?>"></span><span class="promo__c">Nhận báo giá: <?php echo esc_html(icd('hotline')); ?></span></a>
    <div class="grid grid--5"><?php foreach ($ps as $p) icd_card($p); ?></div>
  </div>
</section>
<?php } ?>

<?php $ns = get_posts(icd_knowledge_args(4)) ?: get_posts(['numberposts' => 4]); if ($ns) : $f = array_shift($ns); ?>
<section class="box">
  <div class="box__h"><h2>Kiến thức và tư vấn chọn máy</h2><a class="more" href="<?php echo esc_url(home_url('/tu-van.html')); ?>">Xem thêm</a></div>
  <div class="news">
    <a class="news__f" href="<?php echo esc_url(get_permalink($f)); ?>"><?php $im = icd_img($f->ID, 'medium_large'); if ($im) echo '<img loading="lazy" src="' . esc_url($im) . '" alt="">'; ?><strong><?php echo esc_html(get_the_title($f)); ?></strong><time>Ngày cập nhật: <?php echo esc_html(get_the_date('d/m/Y', $f)); ?></time><p><?php echo esc_html(wp_trim_words(get_the_excerpt($f) ?: wp_strip_all_tags($f->post_content), 36, '…')); ?></p></a>
    <ul class="news__l"><?php foreach ($ns as $p) : ?><li><a href="<?php echo esc_url(get_permalink($p)); ?>"><?php $im = icd_img($p->ID, 'medium'); if ($im) echo '<img loading="lazy" src="' . esc_url($im) . '" alt="">'; ?><span><strong><?php echo esc_html(get_the_title($p)); ?></strong><time>Ngày cập nhật: <?php echo esc_html(get_the_date('d/m/Y', $p)); ?></time></span></a></li><?php endforeach; ?></ul>
  </div>
</section>
<?php endif; ?>

<section class="box box--svc">
  <div class="box__h"><h2><?php echo esc_html(icd('svc_title')); ?></h2></div>
  <div class="svc"><p><?php echo esc_html(icd('svc_text')); ?></p>
    <ul><?php foreach (preg_split('/\R/u', trim(icd('svc_list'))) as $l) if (trim($l)) echo '<li>' . esc_html($l) . '</li>'; ?></ul>
    <a class="btn" href="#bao-gia">Đặt lịch kỹ thuật</a></div>
</section>
</div></div>
<?php get_footer();
