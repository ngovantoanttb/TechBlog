<?php
/**
 * Dynamic llms.txt Generator for TechBlog
 *
 * Follows the official llms.txt standard specification (https://llmstxt.org).
 * Generates a clean Markdown file containing site summary, category index,
 * pages, and recent articles tailored for LLM crawlers and AI search tools.
 */

// Disable error reporting to prevent breaking text format
error_reporting(0);
@ini_set('display_errors', 0);

// Load WordPress environment
define('WP_USE_THEMES', false);
require_once( __DIR__ . '/wp-load.php' );

// Set header content type to markdown / plain text
header('Content-Type: text/markdown; charset=utf-8');
header('Cache-Control: public, max-age=3600'); // Cache for 1 hour

$site_name = get_bloginfo('name');
$site_desc = get_bloginfo('description');
if (empty($site_desc) || $site_desc === 'Một trang web sử dụng WordPress' || $site_desc === 'Just another WordPress site') {
    $site_desc = 'Kênh thông tin công nghệ, đánh giá sản phẩm, thủ thuật phần mềm và hướng dẫn lập trình.';
}

$is_full = isset($_GET['full']) && $_GET['full'] == '1';

?>
# <?php echo esc_html($site_name); ?>

> <?php echo esc_html($site_desc); ?>

Website <?php echo esc_html($site_name); ?> cung cấp thông tin, hướng dẫn thực hành và đánh giá mới nhất về công nghệ thông tin, phần mềm, thủ thuật máy tính và lập trình.

## Chuyên mục chính

<?php
$categories = get_terms(array(
    'taxonomy'   => 'category',
    'hide_empty' => true,
    'orderby'    => 'count',
    'order'      => 'DESC'
));

if (!is_wp_error($categories) && !empty($categories)) {
    foreach ($categories as $cat) {
        $cat_link = get_category_link($cat->term_id);
        $cat_desc = !empty($cat->description) ? trim(strip_tags($cat->description)) : 'Tổng hợp các bài viết và tin tức thuộc chuyên mục ' . $cat->name;
        echo '- [' . esc_html($cat->name) . '](' . esc_url($cat_link) . '): ' . esc_html($cat_desc) . "\n";
    }
}
?>

## Bài viết mới nhất

<?php
$posts = get_posts(array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 20,
    'orderby'        => 'date',
    'order'          => 'DESC'
));

if (!empty($posts)) {
    foreach ($posts as $post) {
        $permalink = get_permalink($post->ID);
        $excerpt = wp_strip_all_tags(get_the_excerpt($post->ID));
        $excerpt = str_replace(array("\r", "\n"), ' ', $excerpt);
        if (mb_strlen($excerpt) > 150) {
            $excerpt = mb_substr($excerpt, 0, 147) . '...';
        }
        echo '- [' . esc_html($post->post_title) . '](' . esc_url($permalink) . '): ' . esc_html($excerpt) . "\n";

        if ($is_full) {
            $content = wp_strip_all_tags($post->post_content);
            echo "\n> " . trim($content) . "\n\n---\n\n";
        }
    }
}
?>

## Các trang chính

- [Trang chủ](<?php echo esc_url(home_url('/')); ?>): Trang chủ cập nhật tin tức và bài viết công nghệ mới nhất.
<?php
$pages = get_posts(array(
    'post_type'      => 'page',
    'post_status'    => 'publish',
    'posts_per_page' => 10,
    'orderby'        => 'title',
    'order'          => 'ASC'
));

if (!empty($pages)) {
    foreach ($pages as $page) {
        $permalink = get_permalink($page->ID);
        echo '- [' . esc_html($page->post_title) . '](' . esc_url($permalink) . ")\n";
    }
}
?>
