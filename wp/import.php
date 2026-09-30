<?php
/**
 * Nhập dữ liệu xequetbui.com (từ tham-khao/data/*.json) vào WP Local.
 * Chạy: wp/wpx.sh eval-file wp/import.php
 * Giữ nguyên ID/slug/ngày; bỏ sản phẩm & bài xe điện Magway (đã có bảng 301). Chạy lại được (bỏ qua ID đã có).
 */
if (!defined('WP_CLI')) exit;
set_time_limit(0);
ini_set('memory_limit', '1024M');
$R = '/Volumes/Extreme SSD/Projects/ICD do thi/06-thiet-ke-xequetbui';
$D = "$R/tham-khao/data/";
$L = function ($n) use ($D) { return json_decode(file_get_contents("$D$n.json"), true); };
$crawl = [];
foreach ($L('crawl_html') as $r) $crawl[rtrim($r['url'], '/')] = $r;
$OLD = 'https://xequetbui.com';
$NEW = untrailingslashit(home_url());
$uploads = wp_get_upload_dir();
$media = [];
foreach ($L('media') as $m) $media[$m['id']] = $m;

function xqb_is_ev($p) {
    $t = html_entity_decode($p['title']['rendered']) . ' ' . $p['slug'];
    if (in_array(651, $p['product_cat'] ?? [])) return true;
    return (bool) (preg_match('/xe.dien|xe điện|magway|wuling|acquy cho xe điện/iu', $t) && !preg_match('/nâng|nang/iu', $t));
}
$dslug = function ($x) {
    $path = urldecode(parse_url($x['link'], PHP_URL_PATH) ?: '');
    $path = Normalizer::normalize($path, Normalizer::FORM_C);
    $b = basename(rtrim($path, '/'));
    $b = preg_replace('/\.html$/', '', $b);
    return $b !== '' ? sanitize_title($b) : $x['slug'];
};
$fix = function ($html) use ($OLD, $NEW) {
    $html = str_replace("$OLD/wp-content/uploads/", "$NEW/wp-content/uploads/", $html);
    return str_replace([$OLD . '/', $OLD . '"', "http://xequetbui.com/"], [$NEW . '/', $NEW . '"', $NEW . '/'], $html);
};
$seo = function ($url) use ($crawl) { return $crawl[rtrim($url, '/')] ?? []; };
$log = function ($m) { WP_CLI::log($m); };

/* ---------- A. TERMS ---------- */
$tmap = ['product_cat' => [], 'product_tag' => [], 'category' => [], 'post_tag' => []];
$termsrc = ['product_cat' => 'product_cat', 'product_tag' => 'product_tag', 'category' => 'categories', 'post_tag' => 'tags'];
foreach ($termsrc as $tax => $file) {
    $items = $L($file);
    $pending = $items; $guard = 0;
    while ($pending && $guard++ < 6) {
        $next = [];
        foreach ($pending as $t) {
            if ($tax === 'product_cat' && $t['id'] == 651) continue;      // danh mục xe điện Magway
            if ($tax === 'post_tag' && in_array($t['id'], [547, 548])) continue; // tag xe điện chở rác
            $parent = $t['parent'] ?? 0;
            if ($parent && !isset($tmap[$tax][$parent])) { $next[] = $t; continue; }
            $name = html_entity_decode($t['name']);
            $ts = $dslug($t);
            $ex = get_term_by('slug', $ts, $tax) ?: get_term_by('slug', $t['slug'], $tax);
            if ($ex && $ex->slug !== $ts) wp_update_term($ex->term_id, $tax, ['slug' => $ts]);
            if ($ex) { $tmap[$tax][$t['id']] = $ex->term_id; continue; }
            $r = wp_insert_term($name, $tax, ['slug' => $ts, 'description' => $t['description'] ?? '', 'parent' => $parent ? $tmap[$tax][$parent] : 0]);
            if (is_wp_error($r)) { $log("term lỗi $tax {$t['slug']}: " . $r->get_error_message()); continue; }
            $tmap[$tax][$t['id']] = $r['term_id'];
            $s = $seo($t['link']);
            if (!empty($s['title'])) update_term_meta($r['term_id'], 'rank_math_title', $s['title']);
            if (!empty($s['description'])) update_term_meta($r['term_id'], 'rank_math_description', $s['description']);
            if (!empty($s['robots']) && stripos($s['robots'], 'noindex') !== false) update_term_meta($r['term_id'], 'rank_math_robots', ['noindex']);
        }
        $pending = $next;
    }
    $log("terms $tax: " . count($tmap[$tax]));
}
// Uncategorized (VI) -> danh mục mặc định
$unc = get_term_by('slug', 'uncategorized-vi', 'category');
if ($unc) update_option('default_category', $unc->term_id);

/* ---------- B. ATTACHMENT (ảnh đại diện) ---------- */
$attach = function ($old_id) use ($media, $uploads, $log) {
    if (!$old_id || !isset($media[$old_id])) return 0;
    if (get_post($old_id) && get_post_type($old_id) === 'attachment') return $old_id;
    $m = $media[$old_id];
    $rel = urldecode(explode('/wp-content/uploads/', parse_url($m['source_url'], PHP_URL_PATH), 2)[1] ?? '');
    $file = $uploads['basedir'] . '/' . $rel;
    if (!$rel || !file_exists($file)) { $log("thiếu file media $old_id $rel"); return 0; }
    $id = wp_insert_attachment([
        'import_id' => $old_id, 'post_mime_type' => $m['mime_type'], 'post_title' => html_entity_decode($m['title']['rendered'] ?? basename($rel)),
        'post_name' => $m['slug'], 'post_status' => 'inherit', 'post_date' => $m['date'],
    ], $file);
    if (is_wp_error($id) || !$id) return 0;
    if (!empty($m['alt_text'])) update_post_meta($id, '_wp_attachment_image_alt', $m['alt_text']);
    require_once ABSPATH . 'wp-admin/includes/image.php';
    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $file));
    return $id;
};

/* ---------- C. HÀM NHẬP BÀI / TRANG / SẢN PHẨM ---------- */
$put = function ($x, $type, $status = 'publish') use ($fix, $seo, $attach, $tmap, $log, $dslug) {
    if (get_post($x['id'])) { wp_update_post(['ID' => $x['id'], 'post_name' => $dslug($x)]); return $x['id']; }
    $id = wp_insert_post([
        'import_id' => $x['id'], 'post_type' => $type, 'post_status' => $status, 'post_author' => 1,
        'post_title' => html_entity_decode($x['title']['rendered']), 'post_name' => $dslug($x),
        'post_content' => $fix($x['content']['rendered'] ?? ''), 'post_excerpt' => $fix($x['excerpt']['rendered'] ?? ''),
        'post_date' => $x['date'], 'post_date_gmt' => $x['date_gmt'], 'post_modified' => $x['modified'], 'post_modified_gmt' => $x['modified_gmt'],
        'comment_status' => 'closed',
    ], true);
    if (is_wp_error($id)) { $log("lỗi {$x['id']} {$x['slug']}: " . $id->get_error_message()); return 0; }
    $s = $seo($x['link']);
    if (!empty($s['title'])) update_post_meta($id, 'rank_math_title', $s['title']);
    if (!empty($s['description'])) update_post_meta($id, 'rank_math_description', $s['description']);
    $th = $attach($x['featured_media'] ?? 0);
    if ($th) set_post_thumbnail($id, $th);
    return $id;
};

/* ---------- D. TRANG ---------- */
foreach ($L('pages') as $p) {
    $id = $put($p, 'page');
    if ($p['id'] == 3487) update_option('page_on_front', $id);
    if ($p['id'] == 16548) update_option('woocommerce_shop_page_id', $id);
}
update_option('show_on_front', 'page');
$log('pages xong');

/* ---------- E. BÀI VIẾT ---------- */
$skipped = []; $n = 0;
$prodslugs = array_column($L('product'), 'slug');
foreach ($L('posts') as $p) {
    $t = html_entity_decode($p['title']['rendered']) . ' ' . $p['slug'];
    if (preg_match('/xe.dien.cho.rac|xe điện chở rác|magway/iu', $t)) { $skipped[] = "post {$p['slug']}"; continue; }
    $dup = in_array($dslug($p), array_map($dslug, $L('product')), true);           // trùng URL với 1 sản phẩm -> để nháp
    $id = $put($p, 'post', $dup ? 'draft' : 'publish');
    if (!$id) continue;
    $cats = array_filter(array_map(fn($c) => $tmap['category'][$c] ?? null, $p['categories']));
    if ($cats) wp_set_post_categories($id, $cats);
    $tg = array_filter(array_map(fn($c) => $tmap['post_tag'][$c] ?? null, $p['tags']));
    if ($tg) wp_set_post_terms($id, $tg, 'post_tag');
    $n++;
}
$log("posts: $n; bỏ: " . implode(', ', $skipped));

/* ---------- F. SẢN PHẨM ---------- */
$n = 0; $skipped = [];
foreach ($L('product') as $p) {
    if (xqb_is_ev($p)) { $skipped[] = $p['slug']; continue; }
    $id = $put($p, 'product');
    if (!$id) continue;
    wp_set_object_terms($id, 'simple', 'product_type');
    update_post_meta($id, '_visibility', 'visible'); update_post_meta($id, '_stock_status', 'instock');
    update_post_meta($id, '_regular_price', ''); update_post_meta($id, '_price', '');
    $cats = array_filter(array_map(fn($c) => $tmap['product_cat'][$c] ?? null, $p['product_cat']));
    if ($cats) wp_set_object_terms($id, array_values($cats), 'product_cat');
    $tg = array_filter(array_map(fn($c) => $tmap['product_tag'][$c] ?? null, $p['product_tag']));
    if ($tg) wp_set_object_terms($id, array_values($tg), 'product_tag');
    if (function_exists('wc_get_product')) { $wp = wc_get_product($id); if ($wp) $wp->save(); }
    $n++;
    if ($n % 25 === 0) $log("products $n");
}
$log("products: $n; bỏ (EV): " . count($skipped));
update_option('xqb_import_done', current_time('mysql'));
foreach (['product_cat', 'category'] as $tx) { wp_update_term_count_now(get_terms(['taxonomy' => $tx, 'hide_empty' => false, 'fields' => 'ids']), $tx); }
flush_rewrite_rules(false);
$log('XONG');
