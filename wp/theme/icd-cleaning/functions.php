<?php
if (!defined('ABSPATH')) exit;
define('ICD_V', '1.0.0');

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('woocommerce');
    register_nav_menus(['primary' => 'Menu chính', 'footer' => 'Menu chân trang']);
    add_image_size('icd-card', 480, 480, false);
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('icd-font', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap', [], null);
    wp_enqueue_style('icd-main', get_theme_file_uri('assets/css/main.css'), [], filemtime(get_theme_file_path('assets/css/main.css')));
    wp_enqueue_script('icd-orphan', get_theme_file_uri('assets/js/orphan-glue.js'), [], ICD_V, true);
    wp_enqueue_script('icd-main', get_theme_file_uri('assets/js/main.js'), [], filemtime(get_theme_file_path('assets/js/main.js')), true);
});
// bỏ emoji, embed, wp-block-library cho nhẹ
add_action('wp_enqueue_scripts', function () { wp_dequeue_style('wp-block-library'); wp_dequeue_style('global-styles'); }, 100);
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

require_once get_theme_file_path('inc/options.php');
require_once get_theme_file_path('inc/helpers.php');
require_once get_theme_file_path('inc/shop.php');

// video nhúng: ép https, tải lười (nội dung cũ dùng http:// và iframe cố định kích thước)
add_filter('the_content', function ($c) {
    return preg_replace(['#(<iframe[^>]+src=["\'])(?:https?:)?//(www\.)?youtube(-nocookie)?\.com#i', '#<iframe(?![^>]*loading=)#i'], ['$1https://www.youtube$3.com', '<iframe loading="lazy"'], $c);
}, 20);
