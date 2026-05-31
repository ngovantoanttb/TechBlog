<?php
/**
 * TechJournal Theme Functions and Definitions
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

// 2. Load Stylesheet & Asset Setup
function techjournal_scripts() {
    wp_enqueue_style( 'techjournal-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'techjournal_scripts' );

// 3. Centralized TechBlog Placeholder Image Helper
function techblog_get_placeholder_img() {
    return get_template_directory_uri() . '/assets/images/placeholder-thumbnail.svg';
}

// 4. Calculate Read Time Helper (Words / 200 words per minute average)
function techjournal_calculate_read_time( $content ) {
    $word_count = str_word_count( strip_tags( $content ) );
    $read_time = ceil( $word_count / 200 );
    return $read_time > 0 ? $read_time : 1;
}

// 5. Post Views Count Analytics & Metadata Helpers (Strictly tracking '_view_count')
function techjournal_get_post_views( $post_id ) {
    $count_key = '_view_count';
    $count = get_post_meta( $post_id, $count_key, true );
    if ( $count === '' ) {
        delete_post_meta( $post_id, $count_key );
        add_post_meta( $post_id, $count_key, '0' );
        return 0;
    }
    return intval( $count );
}

function techjournal_track_post_view( $post_id ) {
    if ( ! is_single() ) return;
    if ( empty( $post_id ) ) {
        global $post;
        $post_id = $post->ID;
    }
    $count_key = '_view_count';
    $count = get_post_meta( $post_id, $count_key, true );
    if ( $count === '' ) {
        $count = 0;
        delete_post_meta( $post_id, $count_key );
        add_post_meta( $post_id, $count_key, '1' );
    } else {
        $count++;
        update_post_meta( $post_id, $count_key, $count );
    }
}

// Track views dynamically on single template redirect
function techjournal_track_views_action() {
    if ( is_single() ) {
        techjournal_track_post_view( get_the_ID() );
    }
}
add_action( 'template_redirect', 'techjournal_track_views_action' );

// 6. Dynamic Premium SEO & Open Graph Meta Tags Injection in wp_head
function techjournal_seo_meta_tags() {
    global $post;
    
    $site_name = get_bloginfo( 'name' );
    $description = get_bloginfo( 'description' );
    $url = home_url( '/' );
    $image = get_template_directory_uri() . '/assets/images/techblog-banne.svg';
    $title = get_bloginfo( 'name' );
    
    if ( is_single() || is_page() ) {
        setup_postdata( $post );
        $title = get_the_title() . ' - ' . $site_name;
        $url = get_permalink();
        
        // Excerpt as description
        $post_excerpt = get_the_excerpt();
        if ( ! empty( $post_excerpt ) ) {
            $description = wp_strip_all_tags( $post_excerpt );
        } else {
            $description = wp_trim_words( wp_strip_all_tags( get_the_content() ), 30, '...' );
        }
        
        // Thumbnail as image
        if ( has_post_thumbnail() ) {
            $image = get_the_post_thumbnail_url( get_the_ID(), 'large' );
        }
    } elseif ( is_category() ) {
        $cat = get_queried_object();
        $title = single_cat_title( '', false ) . ' - ' . $site_name;
        $url = get_category_link( $cat->term_id );
        if ( ! empty( $cat->description ) ) {
            $description = wp_strip_all_tags( $cat->description );
        }
    }
    
    ?>
    <!-- SEO & Open Graph Meta Tags (TechBlog Premium SEO Integration) -->
    <meta name="description" content="<?php echo esc_attr( $description ); ?>" />
    <meta property="og:site_name" content="<?php echo esc_attr( $site_name ); ?>" />
    <meta property="og:type" content="<?php echo ( is_single() ) ? 'article' : 'website'; ?>" />
    <meta property="og:title" content="<?php echo esc_attr( $title ); ?>" />
    <meta property="og:description" content="<?php echo esc_attr( $description ); ?>" />
    <meta property="og:url" content="<?php echo esc_url( $url ); ?>" />
    <meta property="og:image" content="<?php echo esc_url( $image ); ?>" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>" />
    <meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>" />
    <meta name="twitter:image" content="<?php echo esc_url( $image ); ?>" />
    <?php
}
add_action( 'wp_head', 'techjournal_seo_meta_tags', 1 );

// 7. Server-Side AJAX Load More Posts handler (Supports search, archive, category, and standard list layout grids)
function techblog_load_more_posts() {
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    $cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 3, // strictly load 3 posts each click
        'offset'         => $offset,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => 'publish'
    );
    
    if ( $cat_id > 0 ) {
        $args['cat'] = $cat_id;
    }
    
    if ( ! empty( $search ) ) {
        $args['s'] = $search;
    }
    
    $query = new WP_Query( $args );
    
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            
            $post_image = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
            if ( ! $post_image ) {
                $post_image = techblog_get_placeholder_img();
            }
            
            $cats = get_the_category();
            $category_to_show = null;
            if ( ! empty( $cats ) ) {
                foreach($cats as $c) {
                    if($c->term_id != get_option('default_category')) {
                        $category_to_show = $c;
                        break;
                    }
                }
                if (!$category_to_show) {
                    $category_to_show = $cats[0];
                }
            }
            
            // Format output markup based on query context (Search/Category/Archive = Flex Row, Homepage = Grid Card)
            if ( ! empty( $search ) || is_archive() || is_category() ) {
                ?>
                <article class="group bg-white flex flex-col md:flex-row gap-6 cursor-pointer transition-all duration-300 border-b border-slate-100/80 last:border-0">
                    <!-- Image container on Left -->
                    <a href="<?php the_permalink(); ?>" class="w-full md:w-[320px] aspect-[16/10] sm:aspect-[16/9] md:aspect-[1.5/1] overflow-hidden block relative bg-slate-950 shrink-0">
                        <img class="w-full h-full object-cover transition-transform duration-750 group-hover:scale-102 opacity-95 group-hover:opacity-100" src="<?php echo esc_url($post_image); ?>" alt="<?php the_title_attribute(); ?>" />
                        <?php if ($category_to_show) : ?>
                            <span class="absolute top-3 left-3 bg-[#ff0000] text-white text-[9px] font-black uppercase px-2.5 py-1 tracking-widest shadow-sm">
                                <?php echo esc_html($category_to_show->name); ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    
                    <!-- Text Content on Right -->
                    <div class="flex flex-col flex-grow min-w-0 pt-1 md:pt-2 md:pl-2 pr-4 md:pr-6">
                        <h3 class="font-display text-base sm:text-lg md:text-[20px] text-slate-800 hover:text-primary transition-colors font-bold leading-snug mb-3 break-words">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        
                        <!-- Meta Info Line -->
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5 text-[10px] sm:text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-4">
                            <span>BY <span class="text-[#ff0000] font-black"><?php the_author(); ?></span></span>
                            <span>•</span>
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px] text-blue-500">schedule</span>
                                <?php echo get_the_date('d/m/Y'); ?>
                            </span>
                        </div>
                        
                        <!-- Excerpt -->
                        <p class="text-slate-500 text-xs sm:text-sm leading-relaxed mb-6">
                            <?php echo wp_trim_words( get_the_excerpt(), 25, '...' ); ?>
                        </p>
                        
                        <!-- READ MORE Button -->
                        <a href="<?php the_permalink(); ?>" class="border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300 text-[10px] font-bold uppercase tracking-wider text-slate-600 px-5 py-2 w-fit active:scale-95 transition-all mt-1">
                            READ MORE
                        </a>
                    </div>
                </article>
                <?php
            } else {
                ?>
                <article class="group flex flex-col cursor-pointer transition-all duration-300">
                    <!-- Image container with absolute Category Badge overlay -->
                    <a href="<?php the_permalink(); ?>" class="aspect-[16/10] overflow-hidden block relative bg-slate-950">
                        <img class="w-full h-full object-cover transition-transform duration-750 group-hover:scale-102 opacity-95 group-hover:opacity-100" src="<?php echo esc_url($post_image); ?>" alt="<?php the_title_attribute(); ?>" />
                        <?php if ($category_to_show) : ?>
                            <span class="absolute bottom-3 left-3 bg-[#ff0000] text-white text-[9px] font-black uppercase px-2.5 py-1 tracking-widest shadow-sm">
                                <?php echo esc_html($category_to_show->name); ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <div class="pt-4 flex flex-col flex-grow">
                        <h3 class="font-display text-[14px] sm:text-base text-slate-800 group-hover:text-[#ff0000] transition-colors font-bold leading-snug mb-2 break-words">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        
                        <!-- Date and Clock at the bottom -->
                        <div class="flex items-center gap-1.5 text-[11px] text-slate-400 font-medium mt-auto">
                            <span class="material-symbols-outlined text-[13px] text-blue-500">schedule</span>
                            <?php echo get_the_date('d/m/Y'); ?>
                        </div>
                    </div>
                </article>
                <?php
            }
        }
        wp_reset_postdata();
    }
    wp_die();
}
add_action( 'wp_ajax_techblog_load_more', 'techblog_load_more_posts' );
add_action( 'wp_ajax_nopriv_techblog_load_more', 'techblog_load_more_posts' );

// 8. Client-side Javascript AJAX orchestrator script output injected directly in the wp_footer
function techblog_load_more_js_footer() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('techblog-load-more-btn');
            if (btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const spinner = document.getElementById('load-more-spinner');
                    const page = parseInt(btn.getAttribute('data-page'), 10);
                    const catId = parseInt(btn.getAttribute('data-cat-id'), 10);
                    const search = btn.getAttribute('data-search') || '';
                    
                    // Show spinner and disable button interaction
                    if (spinner) spinner.classList.remove('hidden');
                    btn.classList.add('opacity-75', 'pointer-events-none');
                    
                    // Initial load was 9 posts, subsequently load 3 posts each click
                    const offset = 9 + (page - 1) * 3;
                    
                    const formData = new FormData();
                    formData.append('action', 'techblog_load_more');
                    formData.append('offset', offset);
                    formData.append('cat_id', catId);
                    formData.append('search', search);
                    
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(html => {
                        // Restore state & hide loading spinners
                        if (spinner) spinner.classList.add('hidden');
                        btn.classList.remove('opacity-75', 'pointer-events-none');
                        
                        if (html.trim() !== '') {
                            const grid = document.getElementById('techblog-post-grid');
                            if (grid) {
                                // Create temp wrapper element to convert text to HTML nodes
                                const tempDiv = document.createElement('div');
                                tempDiv.innerHTML = html;
                                const newCards = Array.from(tempDiv.children);
                                
                                newCards.forEach(card => {
                                    card.style.opacity = '0';
                                    card.style.transform = 'translateY(15px)';
                                    card.style.transition = 'all 400ms cubic-bezier(0.25, 1, 0.5, 1)';
                                    grid.appendChild(card);
                                    
                                    // Trigger layout reflow & trigger CSS entry animation
                                    setTimeout(() => {
                                        card.style.opacity = '1';
                                        card.style.transform = 'translateY(0)';
                                    }, 50);
                                });
                            }
                            
                            // Advance to next paginated load offset
                            btn.setAttribute('data-page', page + 1);
                        } else {
                            // No more posts found
                            btn.textContent = 'HẾT BÀI VIẾT';
                            btn.classList.add('bg-slate-400', 'hover:bg-slate-400', 'cursor-not-allowed');
                            setTimeout(() => {
                                btn.style.display = 'none';
                            }, 1500);
                        }
                    })
                    .catch(error => {
                        console.error('TechBlog AJAX Load More failed:', error);
                        if (spinner) spinner.classList.add('hidden');
                        btn.classList.remove('opacity-75', 'pointer-events-none');
                    });
                });
            }
        });
    </script>
    <?php
}
add_action( 'wp_footer', 'techblog_load_more_js_footer', 100 );
