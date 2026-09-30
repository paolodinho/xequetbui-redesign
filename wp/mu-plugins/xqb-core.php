<?php
/**
 * Plugin Name: XQB Core (xequetbui.com)
 * Description: Giữ permalink cũ (.html, không tiền tố), 301 xe điện, catalog mode (không giỏ hàng/thanh toán), form báo giá lưu vào admin.
 */
if (!defined('ABSPATH')) exit;

/* ================= 1. PERMALINK GIỮ NGUYÊN SITE CŨ ================= */
// Bài, trang, sản phẩm, danh mục SP, danh mục bài: /slug.html ; tag: /tag/slug.html ; tag sản phẩm: /tu-khoa-san-pham/slug
add_action('init', function () {
    add_rewrite_rule('^tag/([^/]+)\.html(?:/page/([0-9]+))?/?$', 'index.php?tag=$matches[1]&paged=$matches[2]', 'top');
    // URL cũ không có .html
    add_rewrite_rule('^(thanh-ly|danh-muc-san-pham)/?$', 'index.php?xqb_html=$matches[1]', 'top');
    add_rewrite_rule('^([^/]+)\.html(?:/page/([0-9]+))?/?$', 'index.php?xqb_html=$matches[1]&paged=$matches[2]', 'top');
});
add_filter('query_vars', function ($v) { $v[] = 'xqb_html'; return $v; });

// Phân giải /slug.html theo thứ tự: sản phẩm > bài > trang > danh mục SP > danh mục bài
add_filter('request', function ($qv) {
    if (empty($qv['xqb_html'])) return $qv;
    global $wpdb;
    $slug = sanitize_title(rawurldecode($qv['xqb_html']));
    $paged = $qv['paged'] ?? '';
    $out = [];
    foreach (['product' => 'product', 'post' => 'post', 'page' => 'page'] as $type => $pt) {
        $id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_name=%s AND post_type=%s AND post_status='publish' LIMIT 1", $slug, $pt));
        if ($id) {
            $out = $type === 'page' ? ['pagename' => $slug] : ($type === 'product' ? ['post_type' => 'product', 'product' => $slug, 'name' => $slug] : ['name' => $slug]);
            return $out;
        }
    }
    if (term_exists($slug, 'product_cat')) $out = ['product_cat' => $slug];
    elseif (term_exists($slug, 'category')) $out = ['category_name' => $slug];
    else return ['error' => 404];
    if ($paged) $out['paged'] = $paged;
    return $out;
});

// Sinh link đúng dạng .html
add_filter('post_type_link', function ($url, $post) {
    if ($post->post_type === 'product') return home_url('/' . $post->post_name . '.html');
    return $url;
}, 10, 2);
add_filter('page_link', function ($url, $id) {
    if ((int) get_option('page_on_front') === (int) $id) return home_url('/');
    $p = get_post($id);
    return $p ? home_url('/' . $p->post_name . '.html') : $url;
}, 10, 2);
add_filter('term_link', function ($url, $term, $tax) {
    if ($tax === 'category' && $term->slug === 'thanh-ly') return home_url('/thanh-ly');
    if (in_array($tax, ['product_cat', 'category'], true)) return home_url('/' . $term->slug . '.html');
    if ($tax === 'post_tag') return home_url('/tag/' . $term->slug . '.html');
    return $url;
}, 10, 3);
// Không để WP tự đổi /slug.html sang dạng khác
add_filter('redirect_canonical', function ($redirect, $requested) {
    if (preg_match('#\.html(/page/\d+)?/?$#', parse_url($requested, PHP_URL_PATH) ?: '')) return false;
    return $redirect;
}, 10, 2);

/* ================= 2. 301 XE ĐIỆN (Magway-EV) SANG xedienmoitruong.vn ================= */
add_action('parse_request', function ($wp) {
    // chạy sớm hơn cả 404: cùng bảng map
    $map = json_decode(@file_get_contents(__DIR__ . '/xqb-ev-redirects.json'), true) ?: [];
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
    $key = mb_strtolower(\Normalizer::normalize(rawurldecode($path), \Normalizer::FORM_C));
    if (isset($map[$key])) { wp_redirect($map[$key], 301); exit; }
}, 0);

/* ================= 3. CATALOG MODE: KHÔNG GIỎ HÀNG, KHÔNG THANH TOÁN ================= */
add_filter('woocommerce_is_purchasable', '__return_false');
add_filter('woocommerce_get_price_html', function () { return '<span class="xqb-price">Liên hệ báo giá</span>'; });
add_filter('woocommerce_loop_add_to_cart_link', '__return_empty_string');
add_action('init', function () {
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
});
add_action('template_redirect', function () {
    if (function_exists('is_cart') && (is_cart() || is_checkout() || is_account_page())) { wp_safe_redirect(home_url('/'), 302); exit; }
});
add_filter('woocommerce_enqueue_styles', '__return_empty_array'); // giao diện do theme tự style
add_filter('woocommerce_show_page_title', '__return_false');
add_filter('woocommerce_product_tabs', function ($t) { unset($t['reviews']); return $t; }, 98);
add_filter('woocommerce_product_get_rating_html', '__return_empty_string');

/* ================= 4. FORM BÁO GIÁ -> LƯU VÀO ADMIN + GỬI MAIL ================= */
add_action('init', function () {
    register_post_type('bao_gia', [
        'labels' => ['name' => 'Yêu cầu báo giá', 'singular_name' => 'Yêu cầu báo giá', 'menu_name' => 'Báo giá', 'all_items' => 'Tất cả yêu cầu', 'search_items' => 'Tìm yêu cầu'],
        'public' => false, 'show_ui' => true, 'menu_icon' => 'dashicons-email-alt', 'menu_position' => 26,
        'supports' => ['title', 'editor'], 'capability_type' => 'post', 'capabilities' => ['create_posts' => 'do_not_allow'], 'map_meta_cap' => true,
    ]);
});
add_action('admin_post_nopriv_xqb_quote', 'xqb_handle_quote');
add_action('admin_post_xqb_quote', 'xqb_handle_quote');
function xqb_handle_quote() {
    $back = wp_get_referer() ?: home_url('/');
    if (!isset($_POST['_xqb']) || !wp_verify_nonce($_POST['_xqb'], 'xqb_quote') || !empty($_POST['website'])) { wp_safe_redirect(add_query_arg('bg', 'err', $back)); exit; }
    $name = sanitize_text_field($_POST['name'] ?? ''); $phone = preg_replace('/[^0-9+ .]/', '', $_POST['phone'] ?? '');
    $sp = sanitize_text_field($_POST['sp'] ?? ''); $note = sanitize_textarea_field($_POST['note'] ?? '');
    if (strlen(preg_replace('/\D/', '', $phone)) < 9 || $name === '') { wp_safe_redirect(add_query_arg('bg', 'err', $back) . '#bao-gia'); exit; }
    $body = "Họ tên: $name\nĐiện thoại: $phone\nSản phẩm: $sp\nNhu cầu: $note\nTrang gửi: $back";
    wp_insert_post(['post_type' => 'bao_gia', 'post_status' => 'publish', 'post_title' => "$name - $phone" . ($sp ? " - $sp" : ''), 'post_content' => $body]);
    $to = get_option('xqb_email') ?: get_option('admin_email');
    wp_mail($to, "[Báo giá] $name - $phone", $body);
    wp_safe_redirect(add_query_arg('bg', 'ok', $back) . '#bao-gia'); exit;
}
