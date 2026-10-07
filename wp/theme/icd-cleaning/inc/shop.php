<?php
/** Bán hàng: giá (ẩn/hiện), khuyến mại, nhãn NEW, bộ lọc, sản phẩm hot/khuyến mại, giỏ báo giá, dữ liệu Google Shopping. */
if (!defined('ABSPATH')) exit;

function icd_show_price() { return icd('show_price') === '1'; }

/** Giá gốc, giá khuyến mại, giá bán hiện tại (0 nếu chưa nhập). */
function icd_price($id) {
    $reg = (float) get_post_meta($id, '_regular_price', true); $sale = (float) get_post_meta($id, '_sale_price', true); $cur = (float) get_post_meta($id, '_price', true);
    if (!$reg && $cur) $reg = $cur;
    if ($sale && $reg && $sale >= $reg) $sale = 0;
    return ['reg' => $reg, 'sale' => $sale, 'cur' => $sale ?: $reg];
}
function icd_money($n) { return number_format($n, 0, ',', '.') . '&nbsp;₫'; }
function icd_on_sale($id) { $p = icd_price($id); return $p['sale'] > 0; }
function icd_is_new($id) {
    if (get_post_meta($id, '_icd_new', true) === '1') return true;
    return strtotime(get_post_field('post_date', $id)) > strtotime('-30 days');
}

/** Dòng giá trên thẻ/ trang sản phẩm. Ẩn giá: chỉ hiện "Liên hệ báo giá". */
function icd_price_html($id) {
    $p = icd_price($id);
    if (icd_show_price() && $p['cur']) {
        if ($p['sale']) return '<span class="pr pr--sale"><b>' . icd_money($p['sale']) . '</b><del>' . icd_money($p['reg']) . '</del><em>-' . round((1 - $p['sale'] / $p['reg']) * 100) . '%</em></span>';
        return '<span class="pr"><b>' . icd_money($p['cur']) . '</b></span>';
    }
    return '<span class="pr pr--ct"><a href="#bao-gia" data-sp="' . esc_attr(get_the_title($id)) . '">Liên hệ báo giá</a></span>';
}
/** Nhãn góc ảnh: NEW nhấp nháy, Khuyến mại. */
function icd_badges($id) {
    $o = '';
    if (icd_on_sale($id) || get_post_meta($id, '_icd_promo', true)) $o .= '<span class="tag tag--sale">Khuyến mại' . (icd_show_price() && icd_on_sale($id) ? ' -' . round((1 - icd_price($id)['sale'] / icd_price($id)['reg']) * 100) . '%' : '') . '</span>';
    if (icd_is_new($id)) $o .= '<span class="tag tag--new">NEW</span>';
    return $o ? '<span class="tags">' . $o . '</span>' : '';
}

/* ---------- Ô nhập liệu trong trang sửa sản phẩm ---------- */
add_action('add_meta_boxes', function () {
    add_meta_box('icd_sell', 'ICD - Khuyến mại, video, Google Shopping', function ($post) {
        $g = fn($k) => esc_attr(get_post_meta($post->ID, $k, true));
        wp_nonce_field('icd_sell', '_icd_sell');
        echo '<p><b>Giá</b>: nhập ở ô "Dữ liệu sản phẩm" bên dưới (Giá thông thường / Giá khuyến mại). Khách chỉ thấy giá khi bật "Hiển thị giá cho khách" ở Cài đặt ICD > Khối sản phẩm; mặc định ẩn, hiện "Liên hệ báo giá". Dữ liệu giá vẫn được lưu để dùng cho Google Shopping.</p>';
        echo '<p><label><b>Nội dung khuyến mại đi kèm</b> (mỗi dòng 1 ưu đãi; muốn có link Xem chi tiết thì viết: nội dung | https://link)</label><textarea name="icd_promo" rows="5" style="width:100%">' . esc_textarea(get_post_meta($post->ID, '_icd_promo', true)) . '</textarea></p>';
        echo '<p><label><b>Banner ưu đãi: dòng chữ lớn</b> (vd GIẢM 10%, để trống nếu không cần banner)</label><input name="icd_promo_head" value="' . $g('_icd_promo_head') . '" style="width:100%"></p><p><label><b>Banner ưu đãi: dòng phụ</b></label><input name="icd_promo_sub" value="' . $g('_icd_promo_sub') . '" style="width:100%"></p>';
        echo '<p><label><b>Khuyến mại áp dụng đến hết ngày</b> (vd 31/10/2026)</label> <input name="icd_promo_end" value="' . $g('_icd_promo_end') . '" style="width:160px"></p>';
        echo '<p><label><b>Link video YouTube</b> (hiện ở phần mô tả sản phẩm)</label><input name="icd_youtube" value="' . $g('_icd_youtube') . '" style="width:100%" placeholder="https://www.youtube.com/watch?v=..."></p>';
        echo '<p><label><input type="checkbox" name="icd_new" value="1"' . checked(get_post_meta($post->ID, '_icd_new', true), '1', false) . '> Gắn nhãn NEW nhấp nháy (tự gắn cho sản phẩm đăng trong 30 ngày gần nhất)</label></p>';
        echo '<hr><p><b>Google Shopping</b></p><table class="form-table"><tr><th>Thương hiệu</th><td><input name="icd_brand" value="' . $g('_icd_brand') . '"></td></tr>'
            . '<tr><th>Mã SKU</th><td><input name="icd_sku" value="' . $g('_sku') . '"> (SKU của Woo; để trống sẽ dùng ID)</td></tr>'
            . '<tr><th>GTIN / mã vạch</th><td><input name="icd_gtin" value="' . $g('_icd_gtin') . '"></td></tr>'
            . '<tr><th>MPN (mã nhà sản xuất)</th><td><input name="icd_mpn" value="' . $g('_icd_mpn') . '"></td></tr>'
            . '<tr><th>Tình trạng</th><td><select name="icd_cond">' . implode('', array_map(fn($v, $l) => '<option value="' . $v . '"' . selected(get_post_meta($post->ID, '_icd_cond', true) ?: 'new', $v, false) . '>' . $l . '</option>', ['new', 'used', 'refurbished'], ['Mới', 'Đã qua sử dụng', 'Tân trang'])) . '</select></td></tr>'
            . '<tr><th>Còn hàng</th><td><select name="icd_stock">' . implode('', array_map(fn($v, $l) => '<option value="' . $v . '"' . selected(get_post_meta($post->ID, '_icd_stock', true) ?: 'in', $v, false) . '>' . $l . '</option>', ['in', 'out', 'pre'], ['Còn hàng', 'Hết hàng', 'Đặt trước'])) . '</select></td></tr></table>';
    }, 'product', 'normal', 'default');
});
add_action('save_post_product', function ($id) {
    if (!isset($_POST['_icd_sell']) || !wp_verify_nonce($_POST['_icd_sell'], 'icd_sell') || !current_user_can('edit_post', $id)) return;
    foreach (['promo' => '_icd_promo', 'promo_end' => '_icd_promo_end', 'promo_head' => '_icd_promo_head', 'promo_sub' => '_icd_promo_sub', 'youtube' => '_icd_youtube', 'brand' => '_icd_brand', 'gtin' => '_icd_gtin', 'mpn' => '_icd_mpn', 'cond' => '_icd_cond', 'stock' => '_icd_stock'] as $f => $k)
        update_post_meta($id, $k, $f === 'promo' ? sanitize_textarea_field($_POST['icd_' . $f] ?? '') : sanitize_text_field($_POST['icd_' . $f] ?? ''));
    update_post_meta($id, '_icd_new', !empty($_POST['icd_new']) ? '1' : '0');
});

/** Dữ liệu có cấu trúc Product đầy đủ cho Google Shopping (giá chỉ xuất khi đang hiển thị giá cho khách). */
function icd_schema_extra($pr, $p) {
    $m = fn($k) => get_post_meta($p->ID, $k, true);
    if ($m('_icd_brand')) $pr['brand'] = ['@type' => 'Brand', 'name' => $m('_icd_brand')];
    if ($m('_sku')) $pr['sku'] = $m('_sku');
    if ($m('_icd_gtin')) $pr['gtin'] = $m('_icd_gtin');
    if ($m('_icd_mpn')) $pr['mpn'] = $m('_icd_mpn');
    $pp = icd_price($p->ID);
    if (icd_show_price() && $pp['cur']) {
        $stock = ['in' => 'InStock', 'out' => 'OutOfStock', 'pre' => 'PreOrder'][$m('_icd_stock') ?: 'in'];
        $cond = ['new' => 'NewCondition', 'used' => 'UsedCondition', 'refurbished' => 'RefurbishedCondition'][$m('_icd_cond') ?: 'new'];
        $pr['offers'] = ['@type' => 'Offer', 'url' => get_permalink($p), 'priceCurrency' => 'VND', 'price' => (string) $pp['cur'], 'availability' => 'https://schema.org/' . $stock, 'itemCondition' => 'https://schema.org/' . $cond, 'seller' => ['@type' => 'Organization', 'name' => 'ICD Green Tech']];
    }
    return $pr;
}

/** Khối "Khuyến mại đi kèm" trên trang sản phẩm. */
function icd_promo_box($id) {
    $t = trim((string) get_post_meta($id, '_icd_promo', true)); if (!$t) return;
    $end = get_post_meta($id, '_icd_promo_end', true);
    $h = trim((string) get_post_meta($id, '_icd_promo_head', true)) ?: 'Khuyến mại đi kèm'; $sub = trim((string) get_post_meta($id, '_icd_promo_sub', true));
    $gift = '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="4"/><path d="M12 8v13M5 12v9h14v-9M7.5 8a2.5 2.5 0 010-5C10 3 12 8 12 8s2-5 4.5-5a2.5 2.5 0 010 5"/></svg>';
    echo '<div class="promo-c"><div class="promo-c__h">' . $gift . '<b>' . esc_html($h) . '</b>' . ($sub ? '<span>' . esc_html($sub) . '</span>' : '') . ($end ? '<em>Đến hết ' . esc_html($end) . '</em>' : '') . '</div><ol>';
    foreach (preg_split('/\R/u', $t) as $l) { $l = trim($l); if (!$l) continue; $p = array_map('trim', explode('|', $l, 2));
        echo '<li>' . esc_html($p[0]) . (!empty($p[1]) ? ' <a href="' . esc_url($p[1]) . '">Xem chi tiết</a>' : '') . '</li>'; }
    echo '</ol></div>';
}
/** Ô để lại số điện thoại: nút nổi màu, gửi yêu cầu gọi lại. */
function icd_callback($cls = '') {
    echo '<form class="cb ' . esc_attr($cls) . '" method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="xqb_quote"><input type="hidden" name="cb" value="1">'
        . wp_nonce_field('xqb_quote', '_xqb', true, false) . '<input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">'
        . '<input type="hidden" name="sp" value="' . (is_singular('product') ? esc_attr(get_the_title()) : '') . '">'
        . '<span class="cb__t">' . icd_ico('phone') . ' Để lại số điện thoại, kỹ thuật viên gọi lại ngay</span>'
        . '<input name="phone" type="tel" inputmode="tel" placeholder="Nhập số điện thoại của bạn" aria-label="Số điện thoại" required pattern="[0-9 .+]{9,15}">'
        . '<button class="btn btn--hot" type="submit">Gọi lại cho tôi</button></form>';
}

/* ---------- Sản phẩm hot, khuyến mại, bộ lọc sắp xếp ---------- */
add_action('init', function () {
    add_rewrite_rule('^san-pham-ban-chay\.html/?$', 'index.php?post_type=product&icd_list=hot', 'top');
    add_rewrite_rule('^san-pham-khuyen-mai\.html/?$', 'index.php?post_type=product&icd_list=sale', 'top');
    if (get_option('icd_rw_v') !== '4') { flush_rewrite_rules(false); update_option('icd_rw_v', '4'); }
}, 5);
add_filter('query_vars', fn($v) => array_merge($v, ['icd_list', 'sx']));
function icd_hot_ids($n = 10) {
    $f = get_posts(['post_type' => 'product', 'numberposts' => $n, 'fields' => 'ids', 'orderby' => 'meta_value_num', 'meta_key' => 'total_sales', 'order' => 'DESC',
        'tax_query' => [['taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => 'featured']]]);
    return $f;
}
function icd_sale_ids() {
    global $wpdb; $ids = $wpdb->get_col("SELECT p.ID FROM $wpdb->posts p JOIN $wpdb->postmeta m ON m.post_id=p.ID AND m.meta_key='_sale_price' AND m.meta_value+0>0 WHERE p.post_type='product' AND p.post_status='publish'");
    $ids2 = $wpdb->get_col("SELECT p.ID FROM $wpdb->posts p JOIN $wpdb->postmeta m ON m.post_id=p.ID AND m.meta_key='_icd_promo' AND m.meta_value<>'' WHERE p.post_type='product' AND p.post_status='publish'");
    return array_values(array_unique(array_merge($ids, $ids2)));
}
add_action('pre_get_posts', function ($q) {
    if (is_admin() || !$q->is_main_query()) return;
    $list = $q->get('icd_list');
    if ($list === 'hot') { $q->set('tax_query', [['taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => 'featured']]); }
    elseif ($list === 'sale') { $q->set('post__in', icd_sale_ids() ?: [0]); }
    if ($list) { $q->set('post_type', 'product'); $q->set('posts_per_page', 24); $q->is_post_type_archive = true; $q->is_home = false; }
    if (!($list || $q->is_tax('product_cat') || $q->is_tax('product_tag') || $q->is_post_type_archive('product'))) return;
    $sx = $q->get('sx') ?: ($_GET['sx'] ?? '');
    if ($sx === 'gia-tang' || $sx === 'gia-giam') {
        $q->set('meta_query', ['relation' => 'OR', 'pr' => ['key' => '_price', 'compare' => 'EXISTS', 'type' => 'NUMERIC'], ['key' => '_price', 'compare' => 'NOT EXISTS']]);
        $q->set('orderby', ['pr' => $sx === 'gia-tang' ? 'ASC' : 'DESC', 'date' => 'DESC']);
    } elseif ($sx === 'ban-chay') {
        $q->set('meta_query', ['relation' => 'OR', 'ts' => ['key' => 'total_sales', 'compare' => 'EXISTS', 'type' => 'NUMERIC'], ['key' => 'total_sales', 'compare' => 'NOT EXISTS']]);
        $q->set('orderby', ['ts' => 'DESC', 'date' => 'DESC']);
    }
});
/** Thanh sắp xếp: Mới nhất / Bán chạy / Giá tăng / Giá giảm. */
function icd_sortbar() {
    $cur = $_GET['sx'] ?? ''; $base = remove_query_arg(['sx', 'paged']);
    $base = preg_replace('#/page/\d+/?#', '/', $base);
    echo '<div class="sortbar"><span>Sắp xếp:</span>';
    foreach (['' => 'Mới nhất', 'ban-chay' => 'Bán chạy nhất', 'gia-tang' => 'Giá từ thấp đến cao', 'gia-giam' => 'Giá từ cao đến thấp'] as $k => $l)
        echo '<a class="' . ($cur === $k ? 'on' : '') . '" href="' . esc_url($k ? add_query_arg('sx', $k, $base) : $base) . '">' . esc_html($l) . '</a>';
    echo '</div>';
}
// danh sách rỗng (chưa có SP hot/khuyến mại) vẫn là trang 200, không phải 404
add_action('wp', function () {
    global $wp_query;
    if (get_query_var('icd_list') && $wp_query->is_404()) { $wp_query->is_404 = false; $wp_query->is_archive = true; $wp_query->is_post_type_archive = true; status_header(200); }
});

/** Bài kiến thức/tư vấn: loại bài cho thuê, thanh lý, tuyển dụng, khuyến mãi. */
function icd_knowledge_args($n, $extra = []) {
    $ex = [];
    foreach (['cho-thue-thanh-ly', 'thanh-ly', 'tin-tuyen-dung', 'khuyen-mai'] as $s) { $t = get_category_by_slug($s); if ($t) $ex[] = $t->term_id; }
    global $wpdb; $bad = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type='post' AND (post_title LIKE '%cho thuê%' OR post_title LIKE '%thanh lý%' OR post_title LIKE '%tuyển dụng%' OR post_title LIKE '%tuyển %')");
    $args = array_merge(['numberposts' => $n, 'category_name' => 'tu-van', 'category__not_in' => $ex], $extra);
    $args['post__not_in'] = array_merge($bad, $args['post__not_in'] ?? []);
    return $args;
}

/* ---------- Tin tuyển dụng: lương, hạn nộp, số lượng, địa điểm ---------- */
add_action('add_meta_boxes', function () {
    add_meta_box('icd_job', 'ICD - Thông tin tuyển dụng (chỉ điền cho tin tuyển dụng)', function ($post) {
        wp_nonce_field('icd_job', '_icd_job');
        $g = fn($k) => esc_attr(get_post_meta($post->ID, $k, true));
        echo '<table class="form-table"><tr><th>Mức lương</th><td><input name="icd_job_salary" value="' . $g('_icd_job_salary') . '" style="width:100%" placeholder="vd 8 - 10 triệu + % doanh số"></td></tr>'
            . '<tr><th>Hạn nộp hồ sơ</th><td><input type="date" name="icd_job_deadline" value="' . $g('_icd_job_deadline') . '"> (quá ngày này tin tự hiện "Đã hết hạn")</td></tr>'
            . '<tr><th>Số lượng cần tuyển</th><td><input name="icd_job_qty" value="' . $g('_icd_job_qty') . '" placeholder="vd 03 người"></td></tr>'
            . '<tr><th>Địa điểm làm việc</th><td><input name="icd_job_loc" value="' . $g('_icd_job_loc') . '" style="width:100%" placeholder="vd Hà Nội, TP. Hồ Chí Minh"></td></tr></table>';
    }, 'post', 'side', 'default');
});
add_action('save_post_post', function ($id) {
    if (!isset($_POST['_icd_job']) || !wp_verify_nonce($_POST['_icd_job'], 'icd_job') || !current_user_can('edit_post', $id)) return;
    foreach (['salary', 'deadline', 'qty', 'loc'] as $f) update_post_meta($id, '_icd_job_' . $f, sanitize_text_field($_POST['icd_job_' . $f] ?? ''));
});
function icd_job_info($id) {
    $m = fn($k) => trim((string) get_post_meta($id, '_icd_job_' . $k, true));
    $r = ['salary' => $m('salary'), 'deadline' => $m('deadline'), 'qty' => $m('qty'), 'loc' => $m('loc')];
    $r['any'] = (bool) array_filter($r);
    $r['expired'] = $r['deadline'] && strtotime($r['deadline'] . ' 23:59:59') < time();
    return $r;
}
/** Dải thông tin tuyển dụng (chip). */
function icd_job_chips($id) {
    $j = icd_job_info($id); if (!$j['any']) return '';
    $o = '<ul class="job">';
    if ($j['salary']) $o .= '<li class="job__s"><small>Mức lương</small><b>' . esc_html($j['salary']) . '</b></li>';
    if ($j['deadline']) $o .= '<li class="job__d' . ($j['expired'] ? ' is-off' : '') . '"><small>Hạn nộp hồ sơ</small><b>' . ($j['expired'] ? 'Đã hết hạn (' . esc_html(date('d/m/Y', strtotime($j['deadline']))) . ')' : esc_html(date('d/m/Y', strtotime($j['deadline'])))) . '</b></li>';
    if ($j['qty']) $o .= '<li><small>Số lượng</small><b>' . esc_html($j['qty']) . '</b></li>';
    if ($j['loc']) $o .= '<li><small>Địa điểm</small><b>' . esc_html($j['loc']) . '</b></li>';
    return $o . '</ul>';
}
