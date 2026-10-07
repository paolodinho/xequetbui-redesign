<?php
if (!defined('ABSPATH')) exit;

/** Ảnh sản phẩm/bài: ưu tiên cỡ vừa, sau đó full. */
function icd_img($id, $size = 'woocommerce_single') {
    $t = get_post_thumbnail_id($id);
    if (!$t) return '';
    $u = wp_get_attachment_image_url($t, $size) ?: wp_get_attachment_image_url($t, 'medium_large') ?: wp_get_attachment_image_url($t, 'full');
    return $u ?: '';
}
function icd_ico($n) {
    $i = [
        'search' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>',
        'phone' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/></svg>',
        'pin' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>',
        'menu' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>',
        'doc' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h5"/></svg>',
        'bell' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg>',
        'phone_l' => '<svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor"><path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25c1.1.37 2.3.57 3.6.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.3.2 2.5.57 3.6a1 1 0 0 1-.25 1z"/></svg>',
        'msg' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.5 2 2 6.1 2 11.3c0 2.9 1.4 5.5 3.7 7.2V22l3.4-1.9c.9.2 1.9.4 2.9.4 5.5 0 10-4.1 10-9.2S17.5 2 12 2zm1 12.4-2.5-2.7-4.9 2.7 5.4-5.7 2.6 2.7 4.8-2.7z"/></svg>',
        'cart' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.6"/><circle cx="18" cy="20" r="1.6"/><path d="M2 3h3l2.6 12.5a1 1 0 0 0 1 .8h8.9a1 1 0 0 0 1-.8L20 8H6"/></svg>',
        'fb' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 22v-8.2h2.8l.5-3.3h-3.3V8.4c0-.9.4-1.7 1.8-1.7h1.6V3.8S15.6 3.5 14.2 3.5c-3 0-4.6 1.8-4.6 4.8v2.2H6.8v3.3h2.8V22z"/></svg>',
        'yt' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2C2 8.8 2 12 2 12s0 3.2.4 4.8a2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8c.4-1.6.4-4.8.4-4.8s0-3.2-.4-4.8zM10 15V9l5.2 3z"/></svg>',
        'up' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 14 7-7 7 7"/><path d="M5 20h14"/></svg>',
        'ok' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>',
        'truck' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 3h15v13H1zM16 8h4l3 3v5h-7z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
        'tool' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.4 2.4-2.4-.6-.6-2.4z"/></svg>',
        'shield' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.5 9.5 8 11 4.5-1.5 8-6 8-11V5z"/><path d="m9 12 2 2 4-4"/></svg>',
    ];
    return $i[$n] ?? '';
}
/** Nối 2 từ cuối bằng NBSP + giữ nguyên mã model không bị ngắt ở dấu gạch. */
function icd_nb($t) {
    $t = esc_html($t);
    $t = preg_replace('/\s+(\S+)$/u', '&nbsp;$1', $t);
    return preg_replace('/(\S*\d\S*-\S*|\S*-\S*\d\S*)/u', '<span class="nw0">$1</span>', $t);
}
function icd_card($post = null) {
    $post = get_post($post); $id = $post->ID; $t = get_the_title($id); $u = get_permalink($id); $img = icd_img($id);
    ?>
    <article class="card">
      <a class="card__img" href="<?php echo esc_url($u); ?>" aria-label="<?php echo esc_attr($t); ?>"><?php if ($img) : ?><img loading="lazy" src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($t); ?>"><?php endif; echo icd_badges($id); ?></a>
      <h3 class="card__t"><a href="<?php echo esc_url($u); ?>"><?php echo icd_nb($t); ?></a></h3>
      <div class="card__p"><?php echo icd_price_html($id); ?></div>
      <button class="card__q" type="button" data-addq="<?php echo (int) $id; ?>" data-t="<?php echo esc_attr($t); ?>"><?php echo icd_ico('cart'); ?> Thêm vào giỏ</button>
    </article>
    <?php
}
/** Danh sách sản phẩm theo slug danh mục (có thể nhiều slug). */
function icd_products_by_cats($slugs, $n) {
    return get_posts(['post_type' => 'product', 'post_status' => 'publish', 'numberposts' => $n, 'orderby' => 'menu_order date', 'order' => 'DESC',
        'tax_query' => [['taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $slugs, 'include_children' => true]]]);
}
function icd_top_cats() {
    $ts = get_terms(['taxonomy' => 'product_cat', 'parent' => 0, 'hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC']);
    return array_values(array_filter($ts, fn($t) => $t->slug !== 'chua-phan-loai'));
}
function icd_breadcrumb() {
    $b = ['<a href="' . esc_url(home_url('/')) . '">Trang chủ</a>'];
    if (is_singular('product')) {
        $t = get_the_terms(get_the_ID(), 'product_cat');
        if ($t) { usort($t, fn($a, $c) => $c->parent <=> $a->parent); $b[] = '<a href="' . esc_url(get_term_link($t[0])) . '">' . esc_html($t[0]->name) . '</a>'; }
    } elseif (is_tax('product_cat')) {
        $q = get_queried_object(); if ($q->parent) { $p = get_term($q->parent); $b[] = '<a href="' . esc_url(get_term_link($p)) . '">' . esc_html($p->name) . '</a>'; }
    } elseif (is_singular('post')) {
        $c = get_the_category(); if ($c) $b[] = '<a href="' . esc_url(get_category_link($c[0])) . '">' . esc_html($c[0]->name) . '</a>';
    }
    $b[] = '<span>' . esc_html(is_tax() || is_category() || is_tag() ? single_term_title('', false) : wp_strip_all_tags(get_the_title())) . '</span>';
    return '<nav class="crumb" aria-label="Breadcrumb">' . implode('<i>›</i>', $b) . '</nav>';
}
// tìm kiếm chỉ trong sản phẩm + bài
add_action('pre_get_posts', function ($q) {
    if (!is_admin() && $q->is_main_query() && $q->is_search() && !$q->get('post_type')) $q->set('post_type', ['product', 'post']);
    if (!is_admin() && $q->is_main_query() && ($q->is_tax('product_cat') || $q->is_tax('product_tag') || $q->is_post_type_archive('product'))) $q->set('posts_per_page', 24);
});
// excerpt: không tự thêm chấm lửng lạ
add_filter('excerpt_more', fn() => '…');

/** Ảnh sản phẩm đã tách nền trắng (PNG trong suốt), tạo 1 lần rồi cache trong uploads/icd-cutout/. */
function icd_cutout($post_id) {
    $tid = get_post_thumbnail_id($post_id);
    if (!$tid || !function_exists('imagecreatetruecolor')) return icd_img($post_id, 'full');
    $up = wp_upload_dir();
    $dir = $up['basedir'] . '/icd-cutout'; $file = $dir . '/' . $tid . '.png';
    if (!file_exists($file)) {
        $src = get_attached_file($tid);
        if (!$src || !file_exists($src)) return icd_img($post_id, 'full');
        $info = @getimagesize($src); if (!$info) return icd_img($post_id, 'full');
        $im = $info[2] === IMAGETYPE_PNG ? @imagecreatefrompng($src) : ($info[2] === IMAGETYPE_WEBP ? @imagecreatefromwebp($src) : @imagecreatefromjpeg($src));
        if (!$im) return icd_img($post_id, 'full');
        $w0 = imagesx($im); $h0 = imagesy($im); $sc = min(1, 720 / max($w0, $h0)); $w = max(1, (int) round($w0 * $sc)); $h = max(1, (int) round($h0 * $sc));
        $o = imagecreatetruecolor($w, $h); imagealphablending($o, false); imagesavealpha($o, true);
        imagecopyresampled($o, $im, 0, 0, 0, 0, $w, $h, $w0, $h0); imagedestroy($im);
        $bg = function ($x, $y) use ($o) { $c = imagecolorat($o, $x, $y); $r = ($c >> 16) & 255; $g = ($c >> 8) & 255; $b = $c & 255; return $r > 232 && $g > 232 && $b > 232 && (max($r, $g, $b) - min($r, $g, $b)) < 18; };
        $seen = new SplFixedArray($w * $h); $q = new SplQueue();
        for ($x = 0; $x < $w; $x++) { foreach ([0, $h - 1] as $y) if ($bg($x, $y) && !$seen[$y * $w + $x]) { $seen[$y * $w + $x] = 1; $q->enqueue([$x, $y]); } }
        for ($y = 0; $y < $h; $y++) { foreach ([0, $w - 1] as $x) if ($bg($x, $y) && !$seen[$y * $w + $x]) { $seen[$y * $w + $x] = 1; $q->enqueue([$x, $y]); } }
        $clear = imagecolorallocatealpha($o, 255, 255, 255, 127);
        while (!$q->isEmpty()) {
            [$x, $y] = $q->dequeue(); imagesetpixel($o, $x, $y, $clear);
            foreach ([[1, 0], [-1, 0], [0, 1], [0, -1]] as $d) { $nx = $x + $d[0]; $ny = $y + $d[1];
                if ($nx >= 0 && $ny >= 0 && $nx < $w && $ny < $h && !$seen[$ny * $w + $nx] && $bg($nx, $ny)) { $seen[$ny * $w + $nx] = 1; $q->enqueue([$nx, $ny]); } }
        }
        /* bỏ các mảng rời nhỏ (logo hãng, chữ, bóng đổ rời): chỉ giữ mảng lớn nhất và mảng >= 6% mảng lớn nhất */
        $lab = new SplFixedArray($w * $h); $areas = []; $nl = 0;
        for ($y0 = 0; $y0 < $h; $y0++) for ($x0 = 0; $x0 < $w; $x0++) {
            $i0 = $y0 * $w + $x0; if ($lab[$i0] !== null || ((imagecolorat($o, $x0, $y0) >> 24) & 127) >= 100) continue;
            $nl++; $lab[$i0] = $nl; $st = [$i0]; $ar = 0;
            while ($st) { $i = array_pop($st); $ar++; $cx = $i % $w; $cy = intdiv($i, $w);
                foreach ([[1, 0], [-1, 0], [0, 1], [0, -1]] as $d) { $nx = $cx + $d[0]; $ny = $cy + $d[1]; if ($nx < 0 || $ny < 0 || $nx >= $w || $ny >= $h) continue; $ni = $ny * $w + $nx;
                    if ($lab[$ni] === null && ((imagecolorat($o, $nx, $ny) >> 24) & 127) < 100) { $lab[$ni] = $nl; $st[] = $ni; } } }
            $areas[$nl] = $ar;
        }
        if ($areas) { $mx = max($areas); $clr = imagecolorallocatealpha($o, 255, 255, 255, 127);
            for ($y0 = 0; $y0 < $h; $y0++) for ($x0 = 0; $x0 < $w; $x0++) { $l = $lab[$y0 * $w + $x0]; if ($l !== null && $areas[$l] < $mx * 0.16) imagesetpixel($o, $x0, $y0, $clr); } }
        $minx = $w; $miny = $h; $maxx = 0; $maxy = 0;
        for ($y = 0; $y < $h; $y++) for ($x = 0; $x < $w; $x++) { if (((imagecolorat($o, $x, $y) >> 24) & 127) < 100) { if ($x < $minx) $minx = $x; if ($x > $maxx) $maxx = $x; if ($y < $miny) $miny = $y; if ($y > $maxy) $maxy = $y; } }
        if ($maxx > $minx && $maxy > $miny) { $c = imagecrop($o, ['x' => $minx, 'y' => $miny, 'width' => $maxx - $minx + 1, 'height' => $maxy - $miny + 1]); if ($c) { imagealphablending($c, false); imagesavealpha($c, true); imagedestroy($o); $o = $c; } }
        wp_mkdir_p($dir); imagepng($o, $file, 6); imagedestroy($o);
    }
    return $up['baseurl'] . '/icd-cutout/' . $tid . '.png';
}

/** Khối gợi ý đọc tiếp (bài tư vấn) - giữ người xem ở lại site. */
function icd_related_posts($title = 'Tư vấn chọn máy', $exclude = 0, $n = 3) {
    $ps = get_posts(icd_knowledge_args($n, ['post__not_in' => $exclude ? [$exclude] : []]));
    if (count($ps) < $n) $ps = get_posts(['numberposts' => $n, 'post__not_in' => $exclude ? [$exclude] : []]);
    if (!$ps) return;
    echo '<section class="box"><div class="box__h"><h2>' . esc_html($title) . '</h2><a class="more" href="' . esc_url(home_url('/tu-van.html')) . '">Xem thêm</a></div><div class="rd">';
    foreach ($ps as $p) { $im = icd_img($p->ID, 'medium_large');
        echo '<a href="' . esc_url(get_permalink($p)) . '">' . ($im ? '<img loading="lazy" src="' . esc_url($im) . '" alt="">' : '') . '<strong>' . esc_html(get_the_title($p)) . '</strong><time>' . esc_html(get_the_date('d/m/Y', $p)) . '</time></a>'; }
    echo '</div></section>';
}
function icd_cta($text = 'Cần tư vấn chọn máy đúng nhu cầu?') {
    echo '<div class="cta"><div><b>' . esc_html($text) . '</b><span>Kỹ thuật viên ICD báo giá trong ngày. Hotline ' . esc_html(icd('hotline')) . '.</span></div><a class="btn" href="#bao-gia">Nhận báo giá</a></div>';
}

/** Nội dung cũ: cặp "☑️ Nhãn / giá trị" viết rời từng đoạn -> gộp thành bảng thông số; render shortcode ảnh chú thích. */
function icd_pretty_content($h) {
    $h = do_shortcode($h);
    $h = preg_replace('~<strong>\s*(?:☑️|✅|✔️|☑)\s*([^<]+?)\s*</strong>\s*(?:<br\s*/?>|\R|\s)+([^<\r\n][^<\r\n]*)~u', '<tr><th>$1</th><td>$2</td></tr>', $h);
    $h = preg_replace('~((?:<tr><th>.*?</td></tr>\s*){2,})~us', '<table class="spec"><tbody>$1</tbody></table>', $h);
    $h = preg_replace('~<strong>\s*Tiêu chí\s*</strong>\s*<strong>\s*Thông số kỹ thuật\s*</strong>\s*(?=<table)~u', '', $h);
    return $h;
}
add_filter('the_content', function ($c) { return (is_singular('product') || is_singular('post')) ? icd_pretty_content($c) : $c; }, 9);

function icd_cat_kids($id) {
    return get_terms(['taxonomy' => 'product_cat', 'parent' => $id, 'hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC']);
}
/** Mục danh mục cho menu: có danh mục con thì kèm bảng con bay ra bên phải. */
function icd_cat_li($t) {
    $k = icd_cat_kids($t->term_id);
    $o = '<li class="' . ($k ? 'has-kids' : '') . '"><a href="' . esc_url(get_term_link($t)) . '"><span>' . esc_html($t->name) . '</span>' . ($k ? '<i aria-hidden="true">›</i>' : '') . '</a>';
    if ($k) {
        $o .= '<ul class="cat-sub">';
        foreach ($k as $c) $o .= '<li><a href="' . esc_url(get_term_link($c)) . '"><span>' . esc_html($c->name) . '</span><em>' . (int) $c->count . '</em></a></li>';
        $o .= '</ul>';
    }
    return $o . '</li>';
}

/** Danh sách danh mục cho panel/menu: 2 mục đầu là Bán chạy + Khuyến mại, sau đó là các danh mục sản phẩm. */
function icd_cats_menu() {
    $o = '<li class="sp-hot"><a href="' . esc_url(home_url('/san-pham-ban-chay.html')) . '"><span>Sản phẩm bán chạy</span></a></li>';
    if (icd_sale_ids()) $o .= '<li class="sp-hot sp-sale"><a href="' . esc_url(home_url('/san-pham-khuyen-mai.html')) . '"><span>Khuyến mại trong tháng</span></a></li>';
    foreach (icd_top_cats() as $t) $o .= icd_cat_li($t);
    return $o;
}

/** Tiêu đề ngắt dòng theo cụm: các cụm cách nhau bằng " / ", mỗi cụm không bị cắt giữa chừng. Không có "/" thì dùng icd_nb. */
function icd_chunks($t) {
    if (strpos($t, '/') === false) return icd_nb($t);
    $o = '';
    foreach (array_filter(array_map('trim', explode('/', $t))) as $c) $o .= '<span class="ph">' . esc_html($c) . '</span> ';
    return trim($o);
}
