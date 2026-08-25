<?php
/**
 * TechJournal Assets (CSS / JS) Enqueue and Performance Optimization
 *
 * @package TechJournal
 * @since 1.0.0
 */

// 1. Load Stylesheets & Asset Setup (With Dynamic Cache-Busting versioning)
function techjournal_scripts() {
    $theme_dir = get_template_directory();
    $theme_uri = get_template_directory_uri();
    
    // Main compiled Tailwind & custom styles
    $main_css_path = $theme_dir . '/css/main.min.css';
    $main_version  = file_exists( $main_css_path ) ? filemtime( $main_css_path ) : '1.0.0';
    wp_enqueue_style( 'techjournal-main', $theme_uri . '/css/main.min.css', array(), $main_version );

    // Primary main theme style.css
    $style_path = $theme_dir . '/style.css';
    $style_version = file_exists( $style_path ) ? filemtime( $style_path ) : '1.0.0';
    wp_enqueue_style( 'techjournal-style', get_stylesheet_uri(), array('techjournal-main'), $style_version );
}
add_action( 'wp_enqueue_scripts', 'techjournal_scripts' );

// 2. Preconnect and DNS prefetch for Google Fonts for ultra fast loading
function techjournal_resource_hints( $hints, $relation_type ) {
    if ( 'preconnect' === $relation_type ) {
        $hints[] = 'https://fonts.googleapis.com';
        $hints[] = 'https://fonts.gstatic.com';
    }
    if ( 'dns-prefetch' === $relation_type ) {
        $hints[] = 'fonts.googleapis.com';
        $hints[] = 'fonts.gstatic.com';
    }
    return $hints;
}
add_filter( 'wp_resource_hints', 'techjournal_resource_hints', 10, 2 );

// 3. Disable WP Emojis for faster DOM rendering & smaller payload
function techjournal_disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'techjournal_disable_emojis' );

// 4. Deregister wp-embed script on front-end
function techjournal_deregister_embed_script() {
    if ( ! is_admin() ) {
        wp_deregister_script( 'wp-embed' );
    }
}
add_action( 'wp_footer', 'techjournal_deregister_embed_script' );

// 5. Disable Heartbeat API on front-end and slow down in Admin to 60s
function techjournal_optimize_heartbeat( $settings ) {
    $settings['interval'] = 60;
    return $settings;
}
add_filter( 'heartbeat_settings', 'techjournal_optimize_heartbeat' );

function techjournal_disable_frontend_heartbeat() {
    if ( ! is_admin() ) {
        wp_deregister_script( 'heartbeat' );
    }
}
add_action( 'init', 'techjournal_disable_frontend_heartbeat', 1 );

