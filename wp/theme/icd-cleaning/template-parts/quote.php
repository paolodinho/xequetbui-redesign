<?php $bg = $_GET['bg'] ?? ''; ?>
<section class="quote" id="bao-gia"><div class="wrap"><div class="quote__in">
  <div><h2><?php echo esc_html(icd('quote_title')); ?></h2><p><?php echo esc_html(icd('quote_text')); ?></p>
    <?php if ($bg === 'ok') : ?><p class="qmsg qmsg--ok">Đã nhận yêu cầu. Kỹ thuật viên ICD sẽ gọi lại bạn trong thời gian sớm nhất.</p><?php elseif ($bg === 'err') : ?><p class="qmsg qmsg--err">Vui lòng nhập đủ họ tên và số điện thoại hợp lệ.</p><?php endif; ?></div>
  <form class="qf" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="xqb_quote"><?php wp_nonce_field('xqb_quote', '_xqb'); ?>
    <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
    <input name="name" placeholder="Họ và tên" aria-label="Họ và tên" required><input name="phone" placeholder="Số điện thoại" aria-label="Số điện thoại" inputmode="tel" required>
    <input class="full" id="sp" name="sp" placeholder="Sản phẩm quan tâm" aria-label="Sản phẩm quan tâm" value="<?php echo is_singular('product') ? esc_attr(get_the_title()) : ''; ?>">
    <textarea class="full" name="note" placeholder="Diện tích, nhu cầu sử dụng (nếu có)" aria-label="Nhu cầu"></textarea>
    <button class="btn full" type="submit">Gửi yêu cầu báo giá</button></form>
</div></div></section>
