<?php
/**
 * TechJournal Assets (CSS / JS) Enqueue and Performance Optimization
 *
 * @package TechJournal
 * @since 1.0.0
 */

// 1. Load Stylesheet & Asset Setup
function techjournal_scripts() {
    // Primary main theme styles
    wp_enqueue_style( 'techjournal-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'techjournal_scripts' );

// 2. Preconnect and DNS prefetch for Google Fonts and Tailwind CDN for ultra fast loading
function techjournal_resource_hints( $hints, $relation_type ) {
    if ( 'preconnect' === $relation_type ) {
        $hints[] = 'https://fonts.googleapis.com';
        $hints[] = 'https://fonts.gstatic.com';
        $hints[] = 'https://cdn.tailwindcss.com';
    }
    if ( 'dns-prefetch' === $relation_type ) {
        $hints[] = 'fonts.googleapis.com';
        $hints[] = 'fonts.gstatic.com';
        $hints[] = 'cdn.tailwindcss.com';
    }
    return $hints;
}
add_filter( 'wp_resource_hints', 'techjournal_resource_hints', 10, 2 );
