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
    
    // Track device view stats (Mobile vs Desktop vs Tablet)
    techblog_track_device_view();
    
    // Set cookie for 2 hours
    setcookie( $cookie_name, '1', time() + 7200, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
}

function techblog_track_device_view() {
    $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
    
    $device = 'desktop';
    if ( function_exists( 'wp_is_mobile' ) && wp_is_mobile() ) {
        if ( preg_match( '/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $user_agent ) ) {
            $device = 'tablet';
        } else {
            $device = 'mobile';
        }
    }
    
    $stats = get_option( 'techblog_device_views_stats', array(
        'desktop' => 0,
        'mobile'  => 0,
        'tablet'  => 0,
    ) );
    
    if ( ! isset( $stats[ $device ] ) ) {
        $stats[ $device ] = 0;
    }
    
    $stats[ $device ]++;
    update_option( 'techblog_device_views_stats', $stats );
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

// 6. Premium SVG Loader Helper to fetch local theme SVGs and inject classes
function techjournal_get_svg( $icon, $classes = '' ) {
    // Map Material icon names to local SVG filenames
    $mapping = array(
        'schedule'             => 'clock',
        'comment'              => 'comment',
        'person'               => 'user',
        'calendar_today'       => 'calendar',
        'visibility'           => 'eye',
        'chevron_right'        => 'chevron-right',
        'chevron_left'         => 'chevron-left',
        'keyboard_arrow_down'  => 'chevron-down',
        'arrow_right_alt'      => 'arrow-right',
        'explore'              => 'compass-svgrepo-com',
        'local_fire_department'=> 'fire-svgrepo-com',
        'bolt'                 => 'lightning-svgrepo-com',
        'sync'                 => 'sync-svgrepo-com',
        'search'               => 'search',
        'mail'                 => 'email',
        'close'                => 'close',
        'done'                 => 'done',
        'home'                 => 'home',
        'search_off'           => 'info',
        'article'              => 'article-svgrepo-com',
        
    );
    
    if ( isset( $mapping[ $icon ] ) ) {
        $icon = $mapping[ $icon ];
    }

    $svg_path = get_template_directory() . '/assets/svg/' . $icon . '.svg';
    if ( ! file_exists( $svg_path ) ) {
        // Fallback to the dedicated /svg/ folder
        $svg_path = get_template_directory() . '/svg/' . $icon . '.svg';
    }
    if ( ! file_exists( $svg_path ) ) {
        // Fallback to assets/images
        $svg_path = get_template_directory() . '/assets/images/' . $icon . '.svg';
    }
    
    if ( file_exists( $svg_path ) ) {
        $svg = file_get_contents( $svg_path );
        if ( ! empty( $classes ) ) {
            // Strip any existing class, width, and height attributes to allow CSS/Tailwind scaling to work perfectly
            $svg = preg_replace( '/\s*class="[^"]*"/i', '', $svg );
            $svg = preg_replace( '/\s*width="[^"]*"/i', '', $svg );
            $svg = preg_replace( '/\s*height="[^"]*"/i', '', $svg );
            $svg = preg_replace( '/<svg([^>]*)>/i', '<svg$1 class="' . esc_attr( $classes ) . '">', $svg );
        }
        return $svg;
    }
    return '';
}

// 7. Custom Pagination Helper with beautiful tailwind styles matching the user's design request
function techjournal_pagination( $query = null ) {
    if ( ! $query ) {
        global $wp_query;
        $query = $wp_query;
    }
    
    $max_page = $query->max_num_pages;
    if ( $max_page <= 1 ) {
        return;
    }
    
    $paged = max( 1, intval( get_query_var( 'paged' ) ) );
    
    echo '<div class="flex items-center justify-center gap-2 mt-10" role="navigation" aria-label="Phân trang">';
    
    // Prev Button
    if ( $paged > 1 ) {
        echo '<a href="' . esc_url( get_pagenum_link( $paged - 1 ) ) . '" aria-label="Tin trước" class="w-8 h-8 bg-white border border-slate-200 text-slate-600 hover:text-red-600 hover:border-red-600 hover:bg-slate-50 flex items-center justify-center cursor-pointer active:scale-95 transition-all shadow-sm">';
        echo techjournal_get_svg( 'chevron-left', 'w-4 h-4 fill-current' );
        echo '</a>';
    } else {
        echo '<span aria-label="Tin trước" class="w-8 h-8 bg-slate-100/70 border border-slate-200/80 text-slate-300 flex items-center justify-center cursor-not-allowed select-none">';
        echo techjournal_get_svg( 'chevron-left', 'w-4 h-4 fill-current' );
        echo '</span>';
    }
    
    // Pages range calculation
    $pages_to_show = array();
    for ( $i = 1; $i <= $max_page; $i++ ) {
        if ( $i == 1 || $i == $max_page || ( $i >= $paged - 1 && $i <= $paged + 1 ) ) {
            $pages_to_show[] = $i;
        }
    }
    
    $last_page = 0;
    foreach ( $pages_to_show as $page ) {
        if ( $last_page > 0 ) {
            if ( $page - $last_page > 1 ) {
                echo '<span class="w-8 h-8 hidden sm:flex items-center justify-center text-slate-400 font-bold select-none">...</span>';
            }
        }
        
        $is_outer = ($page == 1 || $page == $max_page);
        $is_adjacent = (abs($page - $paged) <= 1);
        $responsive_class = ($is_outer && !$is_adjacent) ? 'hidden sm:flex' : 'flex';

        if ( $page == $paged ) {
            echo '<span class="w-8 h-8 bg-red-600 border border-red-600 text-white flex items-center justify-center font-bold text-sm select-none transition-all shadow-sm">' . $page . '</span>';
        } else {
            echo '<a href="' . esc_url( get_pagenum_link( $page ) ) . '" class="w-8 h-8 ' . $responsive_class . ' bg-white border border-slate-200 text-slate-700 hover:text-red-600 hover:border-red-600 hover:bg-slate-50 items-center justify-center font-bold text-sm cursor-pointer active:scale-95 transition-all shadow-sm">' . $page . '</a>';
        }
        
        $last_page = $page;
    }
    
    // Next Button
    if ( $paged < $max_page ) {
        echo '<a href="' . esc_url( get_pagenum_link( $paged + 1 ) ) . '" aria-label="Tin tiếp theo" class="w-8 h-8 bg-white border border-slate-200 text-slate-600 hover:text-red-600 hover:border-red-600 hover:bg-slate-50 flex items-center justify-center cursor-pointer active:scale-95 transition-all shadow-sm">';
        echo techjournal_get_svg( 'chevron-right', 'w-4 h-4 fill-current' );
        echo '</a>';
    } else {
        echo '<span aria-label="Tin tiếp theo" class="w-8 h-8 bg-slate-100/70 border border-slate-200/80 text-slate-300 flex items-center justify-center cursor-not-allowed select-none">';
        echo techjournal_get_svg( 'chevron-right', 'w-4 h-4 fill-current' );
        echo '</span>';
    }
    
    echo '</div>';
}

/**
 * 8. Optimized Post Thumbnail Rendering Helper
 *
 * Generates an <img> tag with full WP responsive attributes (srcset, sizes),
 * explicit width/height to avoid CLS, loading="lazy" or "eager", decoding="async",
 * and fetchpriority="high" for LCP elements.
 *
 * @param int|WP_Post|null $post        Post ID or object.
 * @param string           $size        Registered image size ('techjournal-card', 'techjournal-hero', 'techjournal-thumb', etc.).
 * @param string           $class       CSS class names for the img tag.
 * @param array            $custom_args Additional custom options (loading, fetchpriority, alt, etc.).
 * @return string HTML img element output.
 */
function techblog_render_post_thumbnail( $post = null, $size = 'techjournal-card', $class = '', $custom_args = array() ) {
    $post_id = $post ? ( is_object( $post ) ? $post->ID : intval( $post ) ) : get_the_ID();
    
    $default_args = array(
        'loading'       => 'lazy',
        'decoding'      => 'async',
        'class'         => $class,
    );

    $args = wp_parse_args( $custom_args, $default_args );

    // If title attribute isn't set, use post title
    if ( empty( $args['alt'] ) ) {
        $args['alt'] = get_the_title( $post_id );
    }

    // If eager loading or high fetchpriority is requested
    if ( isset( $args['loading'] ) && 'eager' === $args['loading'] ) {
        $args['loading'] = 'eager';
    }

    if ( has_post_thumbnail( $post_id ) ) {
        $thumbnail_id = get_post_thumbnail_id( $post_id );
        $img_html = wp_get_attachment_image( $thumbnail_id, $size, false, $args );
        if ( $img_html ) {
            return $img_html;
        }
    }

    // Fallback placeholder image if post thumbnail does not exist
    $placeholder_url = techblog_get_placeholder_img();
    $alt_text        = esc_attr( $args['alt'] );
    $class_attr      = ! empty( $args['class'] ) ? ' class="' . esc_attr( $args['class'] ) . '"' : '';
    $loading_attr    = ! empty( $args['loading'] ) ? ' loading="' . esc_attr( $args['loading'] ) . '"' : '';
    $decoding_attr   = ! empty( $args['decoding'] ) ? ' decoding="' . esc_attr( $args['decoding'] ) . '"' : '';
    $fetch_attr      = ! empty( $args['fetchpriority'] ) ? ' fetchpriority="' . esc_attr( $args['fetchpriority'] ) . '"' : '';

    return sprintf(
        '<img src="%s" alt="%s"%s%s%s%s width="640" height="360" />',
        esc_url( $placeholder_url ),
        $alt_text,
        $class_attr,
        $loading_attr,
        $decoding_attr,
        $fetch_attr
    );
}

/**
 * 9. Helper to get primary display category name for a post
 *
 * @param int|WP_Post|null $post Post ID or object.
 * @return string Category name.
 */
function techblog_get_post_category_name( $post = null ) {
    $post_id = $post ? ( is_object( $post ) ? $post->ID : intval( $post ) ) : get_the_ID();
    $cats    = get_the_category( $post_id );
    $category_to_show = null;
    if ( ! empty( $cats ) ) {
        foreach ( $cats as $c ) {
            if ( $c->term_id != get_option( 'default_category' ) ) {
                $category_to_show = $c;
                break;
            }
        }
        if ( ! $category_to_show ) {
            $category_to_show = $cats[0];
        }
    }
    return $category_to_show ? $category_to_show->name : 'Tin tức';
}


