<?php
/**
 * Plugin Name: XQB SEO (xequetbui.com)
 * Description: Giữ title / meta description / robots / canonical / OG / JSON-LD của site cũ. Nguồn dữ liệu: meta rank_math_title, rank_math_description, rank_math_robots (đã nhập từ site cũ) - không phụ thuộc plugin Rank Math.
 */
if (!defined('ABSPATH')) exit;

function xqb_seo_meta($key) {
    if (is_singular()) return get_post_meta(get_queried_object_id(), $key, true);
    if (is_category() || is_tag() || is_tax()) return get_term_meta(get_queried_object_id(), $key, true);
    return '';
}
function xqb_seo_desc() {
    $d = xqb_seo_meta('rank_math_description');
    if (!$d && is_singular()) { $p = get_queried_object(); $d = wp_trim_words(wp_strip_all_tags($p->post_excerpt ?: $p->post_content), 30, ''); }
    if (!$d && (is_tax() || is_category())) $d = wp_trim_words(wp_strip_all_tags(term_description()), 30, '');
    return trim(preg_replace('/\s+/', ' ', (string) $d));
}

add_filter('pre_get_document_title', function ($t) {
    if (is_admin()) return $t;
    $v = xqb_seo_meta('rank_math_title');
    return $v ? wp_strip_all_tags($v) : $t;
}, 20);
// không để WP đổi " - " thành en dash trong title
add_filter('document_title_separator', fn() => '-');

add_action('wp_head', function () {
    if (is_admin() || is_feed()) return;
    $desc = xqb_seo_desc();
    if ($desc) echo '<meta name="description" content="' . esc_attr($desc) . "\">\n";
    $robots = xqb_seo_meta('rank_math_robots');
    $noindex = (is_array($robots) && in_array('noindex', $robots, true)) || is_search() || is_404();
    if ($noindex) echo "<meta name=\"robots\" content=\"noindex, follow\">\n";
    // canonical cho trang danh mục / tag (WP chỉ tự in cho trang đơn)
    if (is_tax() || is_category() || is_tag()) {
        $l = get_term_link(get_queried_object()); $pg = get_query_var('paged');
        if (!is_wp_error($l)) echo '<link rel="canonical" href="' . esc_url($pg > 1 ? trailingslashit($l) . 'page/' . $pg . '/' : $l) . "\">\n";
    }
    // Open Graph / Twitter
    $title = wp_get_document_title(); $url = is_singular() ? get_permalink() : (is_front_page() ? home_url('/') : '');
    $img = is_singular() ? get_the_post_thumbnail_url(get_queried_object_id(), 'large') : '';
    printf("<meta property=\"og:locale\" content=\"vi_VN\">\n<meta property=\"og:type\" content=\"%s\">\n<meta property=\"og:title\" content=\"%s\">\n<meta property=\"og:site_name\" content=\"ICD Green Tech\">\n", is_singular('post') ? 'article' : (is_singular('product') ? 'product' : 'website'), esc_attr($title));
    if ($desc) printf("<meta property=\"og:description\" content=\"%s\">\n", esc_attr($desc));
    if ($url) printf("<meta property=\"og:url\" content=\"%s\">\n", esc_url($url));
    if ($img) printf("<meta property=\"og:image\" content=\"%s\">\n<meta name=\"twitter:card\" content=\"summary_large_image\">\n", esc_url($img));
    // JSON-LD
    $g = [];
    $org = ['@type' => 'Organization', '@id' => home_url('/#org'), 'name' => 'ICD Green Tech', 'url' => home_url('/'), 'logo' => get_theme_file_uri('assets/img/logo.png'), 'telephone' => function_exists('icd') ? icd('hotline_tel') : ''];
    if (is_front_page()) { $g[] = $org; $g[] = ['@type' => 'WebSite', '@id' => home_url('/#website'), 'url' => home_url('/'), 'name' => 'ICD Cleaning Machines', 'publisher' => ['@id' => home_url('/#org')], 'potentialAction' => ['@type' => 'SearchAction', 'target' => home_url('/?s={search_term_string}&post_type=product'), 'query-input' => 'required name=search_term_string']]; }
    if (is_singular('product')) {
        $p = get_queried_object(); $pr = ['@type' => 'Product', 'name' => get_the_title($p), 'url' => get_permalink($p), 'description' => $desc, 'brand' => ['@type' => 'Brand', 'name' => 'ICD Cleaning Machines'], 'sku' => (string) $p->ID];
        if ($img) $pr['image'] = $img;
        if (function_exists('icd_schema_extra')) $pr = icd_schema_extra($pr, $p);
        $g[] = $pr;
    } elseif (is_singular('post')) {
        $p = get_queried_object(); $ar = ['@type' => 'Article', 'headline' => get_the_title($p), 'datePublished' => get_the_date('c', $p), 'dateModified' => get_the_modified_date('c', $p), 'mainEntityOfPage' => get_permalink($p), 'author' => ['@type' => 'Organization', 'name' => 'ICD Green Tech'], 'publisher' => $org];
        if ($img) $ar['image'] = $img;
        $g[] = $ar;
    }
    if (!is_front_page() && function_exists('icd_breadcrumb')) {
        preg_match_all('#<a href="([^"]+)">([^<]+)</a>#', icd_breadcrumb(), $m, PREG_SET_ORDER); $items = []; $i = 1;
        foreach ($m as $x) $items[] = ['@type' => 'ListItem', 'position' => $i++, 'name' => html_entity_decode($x[2]), 'item' => $x[1]];
        $items[] = ['@type' => 'ListItem', 'position' => $i, 'name' => wp_strip_all_tags(is_singular() ? get_the_title() : single_term_title('', false))];
        $g[] = ['@type' => 'BreadcrumbList', 'itemListElement' => $items];
    }
    if ($g) echo '<script type="application/ld+json">' . wp_json_encode(['@context' => 'https://schema.org', '@graph' => $g], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</script>\n";
}, 2);
// bỏ canonical/robots mặc định của WP đã bị thay thế ở trên (giữ rel_canonical cho trang đơn)
remove_action('wp_head', 'wp_robots', 1);
