<?php
/**
 * Dynamic robots.txt Generator for TechBlog
 *
 * Generates a clean, SEO-friendly robots.txt file that allows search crawlers
 * and specifically enables access for Facebook's crawlers (Facebot and facebookexternalhit).
 * It dynamically appends the correct absolute Sitemap URL using the current site domain.
 */

// Disable error reporting to prevent leaking internal paths or breaking text output
error_reporting(0);
@ini_set('display_errors', 0);

// Load WordPress environment
define('WP_USE_THEMES', false);
require_once( __DIR__ . '/wp-load.php' );

// Set header content type to plain text
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');

$site_url = home_url();

?>
User-agent: *
Disallow: /wp-admin/
Allow: /wp-admin/admin-ajax.php
Disallow: /wp-login.php
Disallow: /wp-content/plugins/
Disallow: /wp-includes/

# Specifically permit Facebook's Open Graph and sharing crawler
User-agent: Facebot
Allow: /

# Specifically permit Facebook's generic crawler
User-agent: facebookexternalhit
Allow: /

# XML Sitemap
Sitemap: <?php echo esc_url($site_url . '/sitemap.xml'); ?>
