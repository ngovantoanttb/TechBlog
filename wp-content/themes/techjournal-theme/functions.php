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
