<?php
/** Trang "Cài đặt ICD" - mọi nội dung hay đổi đều sửa ở đây, không đụng code. */
if (!defined('ABSPATH')) exit;

function icd_defaults() {
    return [
        'hotline' => '02422 009 188', 'hotline_tel' => '02422009188',
        'hotline2' => '0935 482 688', 'hotline2_tel' => '0935482688',
        'zalo' => 'https://zalo.me/0935482688', 'email' => 'khachhang.icd@gmail.com',
        'addr_hn' => 'Số 55 cụm 5 đường Anh Dũng, Thiên Lộc, Hà Nội',
        'addr_hcm' => '551/212 Lê Văn Khương, phường Tân Thới Hiệp, TP. Hồ Chí Minh',
        'hours' => 'Thứ 2 - Thứ 7: 8:00 - 17:30',
        'addr_factory' => 'Lô CN 10.3.2 KCN Phúc Yên, Phú Thọ', 'messenger' => 'https://m.me/icdgreentech', 'show_price' => '0',
        'facebook' => 'https://www.facebook.com/icdgreentech', 'youtube' => 'https://www.youtube.com/@ICD_Greentech',
        'hero_title' => 'Máy chà sàn, xe quét bụi công nghiệp chính hãng',
        'hero_text' => 'Đầy đủ dòng máy cho nhà xưởng, kho bãi, siêu thị và đô thị. Tư vấn đúng nhu cầu, báo giá trong ngày.',
        'hero_btn1' => 'Nhận báo giá ngay', 'hero_btn2' => 'Xem sản phẩm', 'hero_btn2_url' => '/danh-muc-san-pham.html',
        'hero_product' => '', 'hero_tag' => 'Nổi bật',
        'side1_title' => 'Cho thuê máy chà sàn', 'side1_sub' => 'Theo ngày, tháng, dự án', 'side1_url' => '/cho-thue-thanh-ly.html', 'side1_product' => '',
        'side2_title' => 'Thanh lý máy đã qua sử dụng', 'side2_sub' => 'Kiểm định, bảo hành', 'side2_url' => '/thanh-ly', 'side2_product' => '',
        'usp' => "Hàng chính hãng|Nguồn gốc rõ ràng, đủ CO CQ\nGiao hàng toàn quốc|Lắp đặt, hướng dẫn tại chỗ\nBảo dưỡng, sửa chữa|Kỹ thuật viên hỗ trợ nhanh\nBáo giá trong ngày|Không giỏ hàng, tư vấn trực tiếp",
        'rows' => "may-lau-san-cha-san-nha-xuong-cong-nghiep|Máy chà sàn công nghiệp|ICD Cleaning|Làm sạch sàn / nhà xưởng, / kho bãi, siêu thị|10\nxe-quet-rac,xe-quet-hut-bui-nha-xuong-khu-cong-nghiep,xe-quet-rac-hut-bui-do-thi|Xe quét bụi, xe quét rác|Nhà xưởng, đô thị|Quét sạch bụi, rác / cho nhà xưởng, / đô thị|10\nmay-hut-bui-cong-nghiep-nha-xuong-cong-suat-lon|Máy hút bụi công nghiệp|Hút khô, hút ướt|Hút bụi khô và ướt / cho nhà xưởng, / công trình|5\nxe-dien-keo-hang-nang-hang-trong-nha-xuong|Xe nâng điện, xe kéo hàng|Nâng hạ trong xưởng|Nâng hạ, kéo hàng / gọn gàng / trong nhà xưởng|5\nmay-phun-xit-ap-luc-cao-may-ve-sinh-lam-sach-cong-nghiep|Máy phun xịt áp lực cao|Rửa xe, rửa đường|Rửa xe, rửa đường, / vệ sinh / nhà xưởng|5\nxe-cho-rac|Xe chở rác|Thu gom rác|Thu gom rác / gọn gàng, / đủ tải trọng|5",
        'svc_title' => 'Dịch vụ bảo dưỡng, sửa chữa máy vệ sinh công nghiệp',
        'svc_text' => 'Đội ngũ kỹ thuật ICD xử lý sự cố, thay pin, thay phụ tùng cho máy chà sàn, xe quét, máy hút bụi mọi thương hiệu.',
        'svc_list' => "Bảo dưỡng định kỳ\nSửa chữa tại xưởng\nThay pin lithium\nPhụ tùng chính hãng",
        'quote_title' => 'Nhận báo giá thiết bị trong ngày',
        'quote_text' => 'Để lại thông tin, kỹ thuật viên ICD gọi lại tư vấn model phù hợp diện tích và ngân sách của bạn.',
        'footer_about' => 'Công ty CP Phát triển Công nghiệp và Đô thị Việt Nam. Máy và thiết bị làm sạch công nghiệp.',
    ];
}
function icd($k) {
    static $o = null;
    if ($o === null) $o = wp_parse_args(get_option('icd_options', []), icd_defaults());
    return $o[$k] ?? '';
}

add_action('admin_menu', function () {
    add_menu_page('Cài đặt ICD', 'Cài đặt ICD', 'manage_options', 'icd-settings', 'icd_settings_page', 'dashicons-admin-generic', 3);
});
add_action('admin_init', function () {
    register_setting('icd_group', 'icd_options', ['sanitize_callback' => function ($in) {
        $out = [];
        foreach (icd_defaults() as $k => $_) $out[$k] = isset($in[$k]) ? (strpos($k, '_text') !== false || in_array($k, ['usp', 'rows', 'svc_list'], true) ? sanitize_textarea_field($in[$k]) : sanitize_text_field($in[$k])) : '';
        // mục email dùng cho form báo giá
        update_option('xqb_email', sanitize_email($out['email']));
        return $out;
    }]);
});
function icd_settings_page() {
    $tabs = [
        'lienhe' => ['Liên hệ', ['hotline' => 'Hotline chính (hiển thị)', 'hotline_tel' => 'Hotline chính (số để bấm gọi, viết liền)', 'hotline2' => 'Hotline 2 (hiển thị)', 'hotline2_tel' => 'Hotline 2 (số để bấm gọi)', 'zalo' => 'Link Zalo', 'email' => 'Email nhận yêu cầu báo giá', 'addr_hn' => 'Địa chỉ Hà Nội', 'addr_hcm' => 'Địa chỉ HCM', 'hours' => 'Giờ làm việc', 'addr_factory' => 'Địa chỉ nhà máy sản xuất', 'facebook' => 'Facebook', 'messenger' => 'Link Messenger (https://m.me/ten-fanpage)', 'youtube' => 'YouTube']],
        'trangchu' => ['Trang chủ', ['hero_tag' => 'Nhãn nhỏ trên ảnh hero', 'hero_title' => 'Tiêu đề hero', 'hero_text' => 'Mô tả hero (text)', 'hero_btn1' => 'Nút 1 (cam)', 'hero_btn2' => 'Nút 2', 'hero_btn2_url' => 'Link nút 2', 'hero_product' => 'ID sản phẩm làm ảnh hero (để trống = tự chọn)', 'side1_title' => 'Banner phải 1 - tiêu đề', 'side1_sub' => 'Banner phải 1 - mô tả', 'side1_url' => 'Banner phải 1 - link', 'side1_product' => 'Banner phải 1 - ID sản phẩm ảnh', 'side2_title' => 'Banner phải 2 - tiêu đề', 'side2_sub' => 'Banner phải 2 - mô tả', 'side2_url' => 'Banner phải 2 - link', 'side2_product' => 'Banner phải 2 - ID sản phẩm ảnh']],
        'khoi' => ['Khối sản phẩm', ['usp' => 'Dải cam kết (mỗi dòng: Tiêu đề|Mô tả)', 'rows' => 'Các hàng sản phẩm trang chủ (mỗi dòng: slug danh mục (nhiều slug ngăn bằng dấu phẩy)|Tiêu đề hàng|Nhãn banner|Tiêu đề banner|Số sản phẩm|Ảnh banner danh mục (URL, tùy chọn - bỏ trống thì banner xanh không có ảnh))', 'svc_title' => 'Khối dịch vụ - tiêu đề', 'svc_text' => 'Khối dịch vụ - mô tả (text)', 'svc_list' => 'Khối dịch vụ - danh sách (mỗi dòng 1 ý)', 'quote_title' => 'Khối báo giá - tiêu đề', 'quote_text' => 'Khối báo giá - mô tả (text)', 'footer_about' => 'Giới thiệu ở chân trang', 'show_price' => 'Hiển thị giá cho khách (1 = hiện, 0 = ẩn, chỉ hiện Liên hệ báo giá)']],
    ];
    $cur = isset($_GET['tab'], $tabs[$_GET['tab']]) ? $_GET['tab'] : 'lienhe';
    echo '<div class="wrap"><h1>Cài đặt ICD</h1><h2 class="nav-tab-wrapper">';
    foreach ($tabs as $k => $t) printf('<a class="nav-tab %s" href="%s">%s</a>', $k === $cur ? 'nav-tab-active' : '', esc_url(admin_url("admin.php?page=icd-settings&tab=$k")), esc_html($t[0]));
    echo '</h2><form method="post" action="options.php">'; settings_fields('icd_group');
    $saved = get_option('icd_options', []);
    echo '<table class="form-table">';
    foreach ($tabs[$cur][1] as $k => $label) {
        $v = icd($k); $multi = in_array($k, ['usp', 'rows', 'svc_list', 'hero_text', 'svc_text', 'quote_text'], true);
        echo '<tr><th><label>' . esc_html($label) . '</label></th><td>';
        echo $multi ? '<textarea class="large-text" rows="' . (in_array($k, ['rows'], true) ? 9 : 4) . '" name="icd_options[' . $k . ']">' . esc_textarea($v) . '</textarea>' : '<input class="regular-text" style="width:520px" name="icd_options[' . $k . ']" value="' . esc_attr($v) . '">';
        echo '</td></tr>';
    }
    echo '</table>';
    // giữ các trường của tab khác khi lưu
    foreach ($tabs as $tk => $t) if ($tk !== $cur) foreach ($t[1] as $k => $_) echo '<input type="hidden" name="icd_options[' . $k . ']" value="' . esc_attr(icd($k)) . '">';
    submit_button('Lưu cài đặt'); echo '</form></div>';
}
