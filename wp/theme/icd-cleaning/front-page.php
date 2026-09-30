<?php get_header();
$hero = icd('hero_product') ? get_post((int) icd('hero_product')) : (icd_products_by_cats(['may-lau-san-cha-san-nha-xuong-cong-nghiep'], 1)[0] ?? null);
$s1 = icd('side1_product') ? get_post((int) icd('side1_product')) : (icd_products_by_cats(['xe-quet-rac'], 1)[0] ?? $hero);
$s2 = icd('side2_product') ? get_post((int) icd('side2_product')) : (icd_products_by_cats(['may-hut-bui-cong-nghiep-nha-xuong-cong-suat-lon'], 1)[0] ?? $hero);
$cats = icd_top_cats();
?>
<section class="bn"><div class="wrap bn__g">
  <aside class="bn__cats"><ul>
    <?php foreach ($cats as $t) : ?><li><a href="<?php echo esc_url(get_term_link($t)); ?>"><?php echo esc_html($t->name); ?></a></li><?php endforeach; ?>
    <li class="sep"><a href="<?php echo esc_url(home_url('/cho-thue-thanh-ly.html')); ?>">Cho thuê máy</a></li>
    <li><a href="<?php echo esc_url(home_url('/thanh-ly')); ?>">Thanh lý máy cũ</a></li>
    <li><a href="<?php echo esc_url(home_url('/tu-van.html')); ?>">Tư vấn chọn máy</a></li>
  </ul></aside>
  <div class="bn__img">
    <div class="bn__txt">
      <h1><?php echo icd_nb(icd('hero_title')); ?></h1>
      <p class="bn__tag"><?php echo esc_html(icd('hero_text')); ?></p>
      <div class="bn__a"><a class="btn" href="#bao-gia"><?php echo esc_html(icd('hero_btn1')); ?></a><a class="btn btn--wl" href="<?php echo esc_url(home_url(icd('hero_btn2_url'))); ?>"><?php echo esc_html(icd('hero_btn2')); ?></a></div>
    </div>
    <div class="bn__pics">
      <?php if ($s1) : ?><a class="p1" href="<?php echo esc_url(get_permalink($s1)); ?>"><img src="<?php echo esc_url(icd_cutout($s1->ID)); ?>" alt="<?php echo esc_attr(get_the_title($s1)); ?>"></a><?php endif; ?>
      <?php if ($hero) : ?><a class="p0" href="<?php echo esc_url(get_permalink($hero)); ?>"><img src="<?php echo esc_url(icd_cutout($hero->ID)); ?>" alt="<?php echo esc_attr(get_the_title($hero)); ?>"></a><?php endif; ?>
      <?php if ($s2) : ?><a class="p2" href="<?php echo esc_url(get_permalink($s2)); ?>"><img src="<?php echo esc_url(icd_cutout($s2->ID)); ?>" alt="<?php echo esc_attr(get_the_title($s2)); ?>"></a><?php endif; ?>
    </div>
  </div>
</div></section>

<div class="mb"><div class="wrap">
<?php foreach (preg_split('/\R/', trim(icd('rows'))) as $n => $line) {
    if (!trim($line)) continue;
    [$slugs, $title, $kick, $bh, $cnt] = array_pad(explode('|', $line), 5, '');
    $slugs = array_map('trim', explode(',', $slugs)); $ps = icd_products_by_cats($slugs, max(5, (int) $cnt)); if (!$ps) continue;
    $first = get_term_by('slug', $slugs[0], 'product_cat'); $more = $first ? get_term_link($first) : home_url('/danh-muc-san-pham.html'); ?>
<section class="box" id="row-<?php echo (int) $n; ?>">
  <div class="box__h"><h2><?php echo esc_html($title); ?></h2><a class="more" href="<?php echo esc_url($more); ?>">Xem thêm</a></div>
  <div class="box__b">
    <a class="promo" href="#bao-gia" style="--pimg:url('<?php echo esc_url(icd_img($ps[0]->ID)); ?>')"><span class="promo__k">ICD Cleaning</span><strong><?php echo icd_nb($bh); ?></strong><span class="promo__c">Nhận báo giá: <?php echo esc_html(icd('hotline')); ?></span></a>
    <div class="grid grid--5"><?php foreach ($ps as $p) icd_card($p); ?></div>
  </div>
</section>
<?php } ?>

<?php $ns = get_posts(['numberposts' => 4]); if ($ns) : $f = array_shift($ns); ?>
<section class="box">
  <div class="box__h"><h2>Tin tức mới</h2><a class="more" href="<?php echo esc_url(home_url('/tu-van.html')); ?>">Xem thêm</a></div>
  <div class="news">
    <a class="news__f" href="<?php echo esc_url(get_permalink($f)); ?>"><?php $im = icd_img($f->ID, 'medium_large'); if ($im) echo '<img loading="lazy" src="' . esc_url($im) . '" alt="">'; ?><strong><?php echo esc_html(get_the_title($f)); ?></strong><time>Ngày cập nhật: <?php echo esc_html(get_the_date('d/m/Y', $f)); ?></time><p><?php echo esc_html(wp_trim_words(get_the_excerpt($f) ?: wp_strip_all_tags($f->post_content), 36, '…')); ?></p></a>
    <ul class="news__l"><?php foreach ($ns as $p) : ?><li><a href="<?php echo esc_url(get_permalink($p)); ?>"><?php $im = icd_img($p->ID, 'medium'); if ($im) echo '<img loading="lazy" src="' . esc_url($im) . '" alt="">'; ?><span><strong><?php echo esc_html(get_the_title($p)); ?></strong><time>Ngày cập nhật: <?php echo esc_html(get_the_date('d/m/Y', $p)); ?></time></span></a></li><?php endforeach; ?></ul>
  </div>
</section>
<?php endif; ?>

<section class="box box--svc">
  <div class="box__h"><h2><?php echo esc_html(icd('svc_title')); ?></h2></div>
  <div class="svc"><p><?php echo esc_html(icd('svc_text')); ?></p>
    <ul><?php foreach (preg_split('/\R/', trim(icd('svc_list'))) as $l) if (trim($l)) echo '<li>' . esc_html($l) . '</li>'; ?></ul>
    <a class="btn" href="#bao-gia">Đặt lịch kỹ thuật</a></div>
</section>
</div></div>
<?php get_footer();
