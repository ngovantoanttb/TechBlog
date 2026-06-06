<?php
/**
 * TechJournal Theme Setup and Core Initializations
 *
 * @package TechJournal
 * @since 1.0.0
 */

// 1. Core Theme Support Setup
function techjournal_setup() {
    // Add default posts and comments RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    // Let WordPress manage the document title.
    add_theme_support( 'title-tag' );

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support( 'post-thumbnails' );

    // Switch default core markup for search form, comment form, and comments to output valid HTML5.
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );

    // Register Navigation Menus
    register_nav_menus( array(
        'primary' => esc_html__( 'Primary Menu', 'techjournal' ),
    ) );
}
add_action( 'after_setup_theme', 'techjournal_setup' );

// 2. Programmatic Custom Permalinks Setup (danh-sach-bai-viet/post-name)
function techjournal_set_custom_permalinks() {
    global $wp_rewrite;
    $current_structure = get_option( 'permalink_structure' );
    $target_structure  = '/danh-sach-bai-viet/%postname%/';
    
    if ( $current_structure !== $target_structure ) {
        update_option( 'permalink_structure', $target_structure );
        $wp_rewrite->set_permalink_structure( $target_structure );
        flush_rewrite_rules();
    }
}
add_action( 'init', 'techjournal_set_custom_permalinks' );

// 3. Initialize Isolated Database Table for Contacts
function techblog_create_contact_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'techblog_contacts';
    $charset_collate = $wpdb->get_charset_collate();
    
    // We add a 'status' field to track: 'unread', 'read', 'trash'
    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        subject varchar(255) NOT NULL,
        message text NOT NULL,
        status varchar(50) DEFAULT 'unread' NOT NULL,
        created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
add_action( 'after_switch_theme', 'techblog_create_contact_table' );
add_action( 'init', 'techblog_create_contact_table', 5 );

// 4. Exclude static Pages and Privacy Policy posts from site search results
function techjournal_customize_search_query( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
        // Only return blog posts (bài viết) in search results, excluding normal pages
        $query->set( 'post_type', 'post' );
        
        // Exclude specific privacy policy page/post slugs just in case
        $excluded_slugs = array( 'chinh-sach-bao-mat', 'privacy-policy' );
        $excluded_ids = array();
        
        foreach ( $excluded_slugs as $slug ) {
            $post = get_page_by_path( $slug, OBJECT, array( 'post', 'page' ) );
            if ( $post ) {
                $excluded_ids[] = $post->ID;
            }
        }
        
        if ( ! empty( $excluded_ids ) ) {
            $current_not_in = $query->get( 'post__not_in' );
            if ( ! is_array( $current_not_in ) ) {
                $current_not_in = ! empty( $current_not_in ) ? array( $current_not_in ) : array();
            }
            $query->set( 'post__not_in', array_merge( $current_not_in, $excluded_ids ) );
        }
    }
}
add_action( 'pre_get_posts', 'techjournal_customize_search_query' );

// 5. Disable native WordPress sitemaps to prevent conflict with custom sitemap.php
add_filter( 'wp_sitemaps_enabled', '__return_false' );


