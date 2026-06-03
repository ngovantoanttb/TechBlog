<?php
/**
 * Dynamic XML Sitemap Generator for TechBlog
 *
 * Generates a valid XML sitemap of the site's homepage, published posts,
 * pages, categories, and tags. This script initializes the WordPress core 
 * environment directly to ensure high performance and domain independence.
 */

// Disable error reporting to prevent breaking XML syntax and leaking internal paths
error_reporting(0);
@ini_set('display_errors', 0);

// Load WordPress environment
define('WP_USE_THEMES', false);
require_once( __DIR__ . '/wp-load.php' );

// Prevent browser/caching issues
header('Content-Type: application/xml; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    <!-- Homepage -->
    <url>
        <loc><?php echo esc_url(home_url('/')); ?></loc>
        <lastmod><?php echo mysql2date('Y-m-d\TH:i:sP', get_lastpostmodified('GMT'), false); ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <?php
    // 1. Fetch published posts
    $posts = get_posts(array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'modified',
        'order'          => 'DESC'
    ));

    foreach ($posts as $post) :
        // Skip password-protected posts to prevent leaking private content structure
        if (!empty($post->post_password)) {
            continue;
        }
        $post_time = ($post->post_modified_gmt && $post->post_modified_gmt !== '0000-00-00 00:00:00') ? $post->post_modified_gmt : $post->post_date_gmt;
        $thumbnail_id = get_post_thumbnail_id($post->ID);
        $image_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'full') : '';
    ?>
    <url>
        <loc><?php echo esc_url(get_permalink($post->ID)); ?></loc>
        <lastmod><?php echo mysql2date('Y-m-d\TH:i:sP', $post_time, false); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
        <?php if ($image_url) : ?>
        <image:image>
            <image:loc><?php echo esc_url($image_url); ?></image:loc>
            <image:title><?php echo esc_html($post->post_title); ?></image:title>
        </image:image>
        <?php endif; ?>
    </url>
    <?php endforeach; ?>

    <?php
    // 2. Fetch published pages
    $front_page_id = (int) get_option('page_on_front');
    $exclude_ids = $front_page_id ? array($front_page_id) : array();
    $pages = get_posts(array(
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'exclude'        => $exclude_ids,
        'orderby'        => 'modified',
        'order'          => 'DESC'
    ));

    foreach ($pages as $page) :
        // Skip password-protected pages
        if (!empty($page->post_password)) {
            continue;
        }
        $page_time = ($page->post_modified_gmt && $page->post_modified_gmt !== '0000-00-00 00:00:00') ? $page->post_modified_gmt : $page->post_date_gmt;
        $thumbnail_id = get_post_thumbnail_id($page->ID);
        $image_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'full') : '';
    ?>
    <url>
        <loc><?php echo esc_url(get_permalink($page->ID)); ?></loc>
        <lastmod><?php echo mysql2date('Y-m-d\TH:i:sP', $page_time, false); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
        <?php if ($image_url) : ?>
        <image:image>
            <image:loc><?php echo esc_url($image_url); ?></image:loc>
            <image:title><?php echo esc_html($page->post_title); ?></image:title>
        </image:image>
        <?php endif; ?>
    </url>
    <?php endforeach; ?>

    <?php
    // 3. Fetch active categories
    $categories = get_terms(array(
        'taxonomy'   => 'category',
        'hide_empty' => true,
    ));

    if (!is_wp_error($categories) && !empty($categories)) :
        foreach ($categories as $category) :
    ?>
    <url>
        <loc><?php echo esc_url(get_category_link($category->term_id)); ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>
    <?php endforeach;
    endif;
    ?>

    <?php
    // 4. Fetch active tags
    $tags = get_terms(array(
        'taxonomy'   => 'post_tag',
        'hide_empty' => true,
    ));

    if (!is_wp_error($tags) && !empty($tags)) :
        foreach ($tags as $tag) :
    ?>
    <url>
        <loc><?php echo esc_url(get_term_link($tag)); ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.3</priority>
    </url>
    <?php endforeach;
    endif;
    ?>
</urlset>
