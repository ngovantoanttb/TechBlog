<?php
/**
 * TechJournal Theme Functions and Definitions - Main Entry Point
 *
 * This file enqueues the modular sub-systems located within the `/inc/` directory.
 * Adheres strictly to the professional WordPress Theme architecture.
 *
 * @package TechJournal
 * @since 1.0.0
 */

// Define directory constants
define( 'TECHJOURNAL_THEME_DIR', get_template_directory() );
define( 'TECHJOURNAL_THEME_INC', TECHJOURNAL_THEME_DIR . '/inc' );

// 1. Core Theme Setup & Database tables initialization
require_once TECHJOURNAL_THEME_INC . '/setup.php';

// 2. Scripts and Styles Management with resource-hint optimizations
require_once TECHJOURNAL_THEME_INC . '/assets.php';

// 3. Helper utilities (read time, safe view counting, gravatar caching)
require_once TECHJOURNAL_THEME_INC . '/helpers.php';

// 4. Dynamic SEO Metadata and highly structured Google Schema JSON-LD
require_once TECHJOURNAL_THEME_INC . '/seo.php';

// 5. Stylized Comments layouts and field order filtering
require_once TECHJOURNAL_THEME_INC . '/comments.php';

// 6. Server-side AJAX controllers and client-side Skeleton loader outputs
require_once TECHJOURNAL_THEME_INC . '/ajax.php';

// 7. Custom isolated admin dashboard contacts listing & details screens
require_once TECHJOURNAL_THEME_INC . '/admin.php';

/**
 * Register Facebook App ID setting in Settings -> General
 */
function techjournal_register_seo_settings() {
    register_setting('general', 'fb_app_id', array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => ''
    ));

    add_settings_field(
        'fb_app_id',
        'Facebook App ID',
        'techjournal_fb_app_id_callback',
        'general',
        'default'
    );
}
add_action('admin_init', 'techjournal_register_seo_settings');

function techjournal_fb_app_id_callback() {
    $value = get_option('fb_app_id', '');
    echo '<input type="text" id="fb_app_id" name="fb_app_id" value="' . esc_attr($value) . '" class="regular-text" />';
    echo '<p class="description">Điền App ID từ trang Facebook Developer để tối ưu hóa crawler và chia sẻ liên kết.</p>';
}

// 8. Site maintenance and suspension management controls
require_once TECHJOURNAL_THEME_INC . '/maintenance.php';

// 9. Welcome Greeting / Splash screen system
require_once TECHJOURNAL_THEME_INC . '/splash-screen.php';

