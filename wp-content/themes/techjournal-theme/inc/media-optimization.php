<?php
/**
 * TechJournal Media & Image Load Optimization Module
 *
 * Handles WebP output formats, image quality compression, LCP image preloading,
 * and lazyloading/decoding optimizations across the theme.
 *
 * @package TechJournal
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * 1. Force WebP output format for uploaded & resized images when supported
 */
function techjournal_image_editor_output_format( $formats ) {
    $formats['image/jpeg'] = 'image/webp';
    $formats['image/png']  = 'image/webp';
    return $formats;
}
add_filter( 'image_editor_output_format', 'techjournal_image_editor_output_format' );

/**
 * 2. Set optimal image compression quality (82% balances crisp clarity with lightweight size)
 */
function techjournal_custom_image_quality( $quality ) {
    return 82;
}
add_filter( 'wp_editor_set_quality', 'techjournal_custom_image_quality' );
add_filter( 'jpeg_quality', 'techjournal_custom_image_quality' );

/**
 * 3. Preload single post featured image (LCP element) in <head>
 */
function techjournal_preload_post_thumbnail() {
    if ( is_singular( 'post' ) && has_post_thumbnail() ) {
        $post_id = get_queried_object_id();
        $img_id  = get_post_thumbnail_id( $post_id );
        
        if ( $img_id ) {
            $img_src    = wp_get_attachment_image_url( $img_id, 'techjournal-hero' );
            $img_srcset = wp_get_attachment_image_srcset( $img_id, 'techjournal-hero' );
            $img_sizes  = wp_get_attachment_image_sizes( $img_id, 'techjournal-hero' );
            
            if ( $img_src ) {
                echo '<link rel="preload" as="image" href="' . esc_url( $img_src ) . '"';
                if ( $img_srcset ) {
                    echo ' imagesrcset="' . esc_attr( $img_srcset ) . '"';
                }
                if ( $img_sizes ) {
                    echo ' imagesizes="' . esc_attr( $img_sizes ) . '"';
                }
                echo ' fetchpriority="high" />' . "\n";
            }
        }
    }
}
add_action( 'wp_head', 'techjournal_preload_post_thumbnail', 2 );

/**
 * 4. Add decoding="async" and loading="lazy" to all inline content images
 */
function techjournal_optimize_content_images( $content ) {
    if ( empty( $content ) || is_admin() ) {
        return $content;
    }

    // Ensure all <img> tags in the content have decoding="async"
    if ( false !== strpos( $content, '<img' ) ) {
        $content = preg_replace_callback( '/<img([^>]+)>/i', function( $matches ) {
            $img_html = $matches[0];
            
            if ( false === strpos( $img_html, 'decoding=' ) ) {
                $img_html = str_replace( '<img', '<img decoding="async"', $img_html );
            }
            if ( false === strpos( $img_html, 'loading=' ) && false === strpos( $img_html, 'fetchpriority="high"' ) ) {
                $img_html = str_replace( '<img', '<img loading="lazy"', $img_html );
            }
            return $img_html;
        }, $content );
    }

    return $content;
}
add_filter( 'the_content', 'techjournal_optimize_content_images', 99 );
