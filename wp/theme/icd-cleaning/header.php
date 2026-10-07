<!doctype html>
<html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php wp_head(); ?></head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="hd">
  <div class="hd__top"><div class="wrap"><span>Máy và thiết bị làm sạch công nghiệp chính hãng</span><span class="hd__tr"><span>Tư vấn sản phẩm: <a class="hot" href="tel:<?php echo esc_attr(icd('hotline_tel')); ?>"><?php echo esc_html(icd('hotline')); ?></a></span></span></div></div>
  <div class="hd__main"><div class="wrap hd__in">
    <button class="mtog" type="button" aria-label="Mở menu" aria-controls="mnav" aria-expanded="false"><span></span><span></span><span></span></button>
    <a class="logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="ICD Cleaning Machines"><img src="<?php echo esc_url(get_theme_file_uri('assets/img/logo.png')); ?>" alt="ICD Green Tech"><span>ICD Cleaning<br>Machines</span></a>
    <form class="search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"><input type="search" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="Bạn cần tìm gì hôm nay?" aria-label="Tìm sản phẩm"><input type="hidden" name="post_type" value="product"><button aria-label="Tìm"><?php echo icd_ico('search'); ?></button></form>
    <a class="hd__tel" href="tel:<?php echo esc_attr(icd('hotline_tel')); ?>"><i><?php echo icd_ico('phone_l'); ?></i><span><small>Hotline mua hàng</small><b><?php echo esc_html(icd('hotline')); ?></b></span></a>
    <button class="hd__cart" type="button" data-cart-open aria-label="Giỏ báo giá"><?php echo icd_ico('cart'); ?><span>Giỏ báo giá</span><i data-cart-n>0</i></button>
    <a class="hd__qt" href="#bao-gia">Nhận báo giá</a>
  </div></div>
  <nav class="nav" aria-label="Menu chính"><div class="wrap nav__in">
    <div class="nav__catw"><a class="nav__cat" href="<?php echo esc_url(home_url('/danh-muc-san-pham.html')); ?>">Danh mục sản phẩm</a>
      <ul class="nav__mega"><?php echo icd_cats_menu(); ?></ul></div>
    <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'fallback_cb' => false, 'depth' => 1, 'menu_class' => 'menu']); ?>
  </div></nav>
</header>
<div class="mnav" id="mnav" hidden>
  <div class="mnav__bg" data-mclose></div>
  <div class="mnav__p" role="dialog" aria-label="Menu">
    <div class="mnav__h"><b>Menu</b><button type="button" data-mclose aria-label="Đóng menu">&times;</button></div>
    <div class="mnav__s">Danh mục sản phẩm</div>
    <ul class="mnav__cats mnav__hot"><li><a href="<?php echo esc_url(home_url('/san-pham-ban-chay.html')); ?>">Sản phẩm bán chạy</a></li><?php if (icd_sale_ids()) : ?><li><a href="<?php echo esc_url(home_url('/san-pham-khuyen-mai.html')); ?>">Khuyến mại trong tháng</a></li><?php endif; ?></ul>
    <ul class="mnav__cats"><?php foreach (icd_top_cats() as $t) : $k = icd_cat_kids($t->term_id); ?>
      <?php if ($k) : ?><li><details><summary><?php echo esc_html($t->name); ?></summary><ul><li><a href="<?php echo esc_url(get_term_link($t)); ?>">Tất cả</a></li><?php foreach ($k as $c) : ?><li><a href="<?php echo esc_url(get_term_link($c)); ?>"><?php echo esc_html($c->name); ?><em><?php echo (int) $c->count; ?></em></a></li><?php endforeach; ?></ul></details></li>
      <?php else : ?><li><a href="<?php echo esc_url(get_term_link($t)); ?>"><?php echo esc_html($t->name); ?></a></li><?php endif; ?>
    <?php endforeach; ?></ul>
  </div>
</div>
<main id="main">
