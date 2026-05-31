<?php
/**
 * TechJournal Helper and Utility Functions
 *
 * @package TechJournal
 * @since 1.0.0
 */

// 1. Centralized TechBlog Placeholder Image Helper
function techblog_get_placeholder_img() {
    return get_template_directory_uri() . '/assets/images/placeholder-thumbnail.svg';
}

// 2. Calculate Read Time Helper (Words / 200 words per minute average)
function techjournal_calculate_read_time( $content ) {
    $word_count = str_word_count( strip_tags( $content ) );
    $read_time = ceil( $word_count / 200 );
    return $read_time > 0 ? $read_time : 1;
}

// 3. Post Views Count Analytics & Metadata Helpers (Strictly tracking '_view_count')
function techjournal_get_post_views( $post_id ) {
    $count_key = '_view_count';
    
    // Check transient cache first for views count to minimize db calls
    $cache_key = 'techblog_views_' . $post_id;
    $count = get_transient( $cache_key );
    
    if ( false === $count ) {
        $count = get_post_meta( $post_id, $count_key, true );
        if ( $count === '' ) {
            $count = 0;
            delete_post_meta( $post_id, $count_key );
            add_post_meta( $post_id, $count_key, '0' );
        } else {
            $count = intval( $count );
        }
        // Cache views count for 1 hour
        set_transient( $cache_key, $count, HOUR_IN_SECONDS );
    }
    
    return intval( $count );
}

// 4. Safe post view tracking to avoid DB thrashing and duplicate hits
function techjournal_is_bot() {
    $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if ( empty( $user_agent ) ) {
        return true; 
    }
    $bots = array(
        'googlebot', 'bingbot', 'yandexbot', 'slurp', 'duckduckbot', 'baiduspider', 
        'sogou', 'exabot', 'facebot', 'ia_archiver', 'twitterbot', 'linkedinbot',
        'yandex', 'naver', 'ahrefs', 'semrush', 'screaming', 'lighthouse'
    );
    foreach ( $bots as $bot ) {
        if ( stripos( $user_agent, $bot ) !== false ) {
            return true;
        }
    }
    return false;
}

function techjournal_track_post_view( $post_id ) {
    if ( ! is_single() ) return;
    if ( techjournal_is_bot() ) return; // Skip bots!
    
    if ( empty( $post_id ) ) {
        global $post;
        $post_id = $post->ID;
    }
    
    // Check cookie to avoid double counting within a single user session (2 hours duration)
    $cookie_name = 'techblog_viewed_post_' . $post_id;
    if ( isset( $_COOKIE[ $cookie_name ] ) ) {
        return;
    }
    
    $count_key = '_view_count';
    $count = get_post_meta( $post_id, $count_key, true );
    if ( $count === '' ) {
        $count = 1;
        delete_post_meta( $post_id, $count_key );
        add_post_meta( $post_id, $count_key, '1' );
    } else {
        $count = intval( $count ) + 1;
        update_post_meta( $post_id, $count_key, $count );
    }
    
    // Set transient cache value
    $cache_key = 'techblog_views_' . $post_id;
    set_transient( $cache_key, $count, HOUR_IN_SECONDS );
    
    // Set cookie for 2 hours
    setcookie( $cookie_name, '1', time() + 7200, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
}

// Track views dynamically on single template redirect
function techjournal_track_views_action() {
    if ( is_single() ) {
        techjournal_track_post_view( get_the_ID() );
    }
}
add_action( 'template_redirect', 'techjournal_track_views_action' );

// 5. Helper to check if an email has a custom Gravatar registered (with daily Transient Caching)
if ( ! function_exists( 'techjournal_has_gravatar' ) ) {
    function techjournal_has_gravatar( $email ) {
        if ( empty( $email ) ) {
            return false;
        }
        $hash = md5( strtolower( trim( $email ) ) );
        $transient_key = 'has_gravatar_' . substr( $hash, 0, 20 );
        $has_gravatar = get_transient( $transient_key );
        
        if ( false === $has_gravatar ) {
            $url = 'https://www.gravatar.com/avatar/' . $hash . '?d=404';
            $response = wp_remote_head( $url, array( 'timeout' => 2 ) );
            if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
                $has_gravatar = 'yes';
            } else {
                $has_gravatar = 'no';
            }
            set_transient( $transient_key, $has_gravatar, DAY_IN_SECONDS );
        }
        
        return 'yes' === $has_gravatar;
    }
}
