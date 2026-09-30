</main>
<?php get_template_part('template-parts/quote'); ?>
<footer class="ft"><div class="wrap">
  <div class="ft__s">
    <div class="ft__hot"><div><b>Hotline hỗ trợ (<?php echo esc_html(icd('hours')); ?>)</b><p>Hotline: <a href="tel:<?php echo esc_attr(icd('hotline_tel')); ?>"><?php echo esc_html(icd('hotline')); ?></a> - <a href="tel:<?php echo esc_attr(icd('hotline2_tel')); ?>"><?php echo esc_html(icd('hotline2')); ?></a></p><p>Email: <a href="mailto:<?php echo esc_attr(icd('email')); ?>"><?php echo esc_html(icd('email')); ?></a></p></div></div>
    <div class="ft__hot"><div><b>Bạn cần báo giá thiết bị?</b><p>Để lại thông tin, kỹ thuật viên gọi lại tư vấn trong ngày.</p></div><a class="btn" href="#bao-gia">Nhận báo giá</a></div>
  </div>
  <div class="ft__g">
    <div><h4>Về chúng tôi</h4><ul><li><a href="<?php echo esc_url(home_url('/ve-chung-toi.html')); ?>">Giới thiệu</a></li><li><a href="<?php echo esc_url(home_url('/lien-he.html')); ?>">Liên hệ</a></li><li><a href="<?php echo esc_url(home_url('/tin-tuyen-dung.html')); ?>">Tuyển dụng</a></li><li><a href="https://icd.com.vn">icd.com.vn</a></li></ul></div>
    <div><h4>Chuyên mục chính</h4><ul><?php foreach (array_slice(icd_top_cats(), 0, 6) as $t) : ?><li><a href="<?php echo esc_url(get_term_link($t)); ?>"><?php echo esc_html($t->name); ?></a></li><?php endforeach; ?></ul></div>
    <div><h4>Hỗ trợ khách hàng</h4><?php if (has_nav_menu('footer')) : wp_nav_menu(['theme_location' => 'footer', 'container' => false, 'depth' => 1]); else : ?><ul><li><a href="<?php echo esc_url(home_url('/cho-thue-thanh-ly.html')); ?>">Cho thuê máy</a></li><li><a href="<?php echo esc_url(home_url('/thanh-ly')); ?>">Thanh lý máy cũ</a></li><li><a href="<?php echo esc_url(home_url('/dich-vu.html')); ?>">Dịch vụ bảo dưỡng</a></li><li><a href="<?php echo esc_url(home_url('/tu-van.html')); ?>">Tư vấn chọn máy</a></li></ul><?php endif; ?></div>
    <div><h4>Văn phòng</h4><p>Hà Nội: <?php echo esc_html(icd('addr_hn')); ?></p><p>HCM: <?php echo esc_html(icd('addr_hcm')); ?></p></div>
    <div><h4>Kết nối với chúng tôi</h4><ul><li><a href="<?php echo esc_url(icd('facebook')); ?>" rel="noopener">Facebook</a></li><li><a href="<?php echo esc_url(icd('youtube')); ?>" rel="noopener">YouTube</a></li><li><a href="<?php echo esc_url(icd('zalo')); ?>" rel="noopener">Zalo: <?php echo esc_html(icd('hotline2')); ?></a></li></ul></div>
  </div>
  <div class="ft__b"><span>Copyright © <?php echo esc_html(date('Y')); ?> ICD Green Tech - All Rights Reserved.</span><span><?php echo esc_html(icd('footer_about')); ?></span></div>
</div></footer>
<a class="callfab" href="tel:<?php echo esc_attr(icd('hotline_tel')); ?>" aria-label="Gọi <?php echo esc_attr(icd('hotline')); ?>"><span class="callfab__i"><?php echo icd_ico('phone_l'); ?></span><span class="callfab__t"><small>Gọi tư vấn miễn phí</small><b><?php echo esc_html(icd('hotline')); ?></b></span></a>
<div class="fab"><a class="zl" href="<?php echo esc_url(icd('zalo')); ?>" rel="noopener" aria-label="Chat Zalo"><span>Zalo</span></a><a class="ms" href="<?php echo esc_url(icd('facebook')); ?>" rel="noopener" aria-label="Messenger"><?php echo icd_ico('msg'); ?></a></div>
<?php wp_footer(); ?></body></html>
