<!doctype html>
<html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width,initial-scale=1">
<?php wp_head(); ?></head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="hd">
  <div class="hd__top"><div class="wrap"><span>Máy và thiết bị làm sạch công nghiệp chính hãng</span><span class="hd__tr"><span>Tư vấn sản phẩm: <a class="hot" href="tel:<?php echo esc_attr(icd('hotline_tel')); ?>"><?php echo esc_html(icd('hotline')); ?></a></span><a href="<?php echo esc_url(home_url('/cho-thue-thanh-ly.html')); ?>">Cho thuê máy</a><a href="<?php echo esc_url(home_url('/thanh-ly')); ?>">Thanh lý</a></span></div></div>
  <div class="hd__main"><div class="wrap hd__in">
    <a class="logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="ICD Cleaning Machines"><img src="<?php echo esc_url(get_theme_file_uri('assets/img/logo.png')); ?>" alt="ICD Green Tech"><span>ICD Cleaning<br>Machines</span></a>
    <form class="search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"><input type="search" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="Bạn cần tìm gì hôm nay?" aria-label="Tìm sản phẩm"><input type="hidden" name="post_type" value="product"><button aria-label="Tìm"><?php echo icd_ico('search'); ?></button></form>
    <a class="hd__tel" href="tel:<?php echo esc_attr(icd('hotline_tel')); ?>"><i><?php echo icd_ico('phone_l'); ?></i><span><small>Hotline mua hàng</small><b><?php echo esc_html(icd('hotline')); ?></b></span></a>
    <a class="hd__qt" href="#bao-gia">Nhận báo giá</a>
  </div></div>
  <nav class="nav" aria-label="Menu chính"><div class="wrap nav__in">
    <div class="nav__catw"><a class="nav__cat" href="<?php echo esc_url(home_url('/danh-muc-san-pham.html')); ?>">Danh mục sản phẩm</a>
      <ul class="nav__mega"><?php foreach (icd_top_cats() as $t) : ?><li><a href="<?php echo esc_url(get_term_link($t)); ?>"><?php echo esc_html($t->name); ?></a></li><?php endforeach; ?></ul></div>
    <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'fallback_cb' => false, 'depth' => 1, 'menu_class' => 'menu']); ?>
    <a class="nav__blog" href="<?php echo esc_url(home_url('/tu-van.html')); ?>">Tư vấn chọn máy</a>
  </div></nav>
</header>
<main id="main">
