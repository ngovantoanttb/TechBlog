<!DOCTYPE html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <!-- Meta Description (Tối ưu SEO chuẩn Lighthouse) -->
    <?php
    $meta_desc = '';
    if ( is_single() || is_page() ) {
        global $post;
        if ( ! empty( $post->post_excerpt ) ) {
            $meta_desc = wp_strip_all_tags( $post->post_excerpt );
        } else {
            $meta_desc = wp_strip_all_tags( get_the_excerpt() );
        }
        if ( empty( $meta_desc ) ) {
            $meta_desc = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
        }
    } elseif ( is_category() ) {
        $meta_desc = category_description();
    } elseif ( is_tag() ) {
        $meta_desc = tag_description();
    }
    
    if ( empty( $meta_desc ) ) {
        $meta_desc = get_bloginfo( 'description' );
    }
    
    if ( empty( $meta_desc ) ) {
        $meta_desc = 'TechBlog - Trang tin tức công nghệ, tạp chí trực tuyến chia sẻ kiến thức chuyên sâu về lập trình, thiết kế web và xu hướng công nghệ mới nhất.';
    }
    
    $meta_desc = html_entity_decode( $meta_desc, ENT_QUOTES, 'UTF-8' );
    $meta_desc = wp_strip_all_tags( $meta_desc );
    $meta_desc = preg_replace( '/\s+/', ' ', $meta_desc );
    $meta_desc = trim( $meta_desc );
    if ( mb_strlen( $meta_desc ) > 160 ) {
        $meta_desc = mb_substr( $meta_desc, 0, 157 ) . '...';
    }
    ?>
    <meta name="description" content="<?php echo esc_attr( $meta_desc ); ?>" />
    
    <!-- Facebook Open Graph & Developer Meta (Tối ưu chia sẻ Facebook và khai báo App ID) -->
    <meta property="fb:app_id" content="<?php echo esc_attr( get_option( 'fb_app_id', 'YOUR_FACEBOOK_APP_ID' ) ); ?>" />
    <meta property="og:site_name" content="<?php bloginfo( 'name' ); ?>" />
    <meta property="og:type" content="<?php echo is_single() ? 'article' : 'website'; ?>" />
    <meta property="og:title" content="<?php echo esc_attr( wp_get_document_title() ); ?>" />
    <meta property="og:description" content="<?php echo esc_attr( $meta_desc ); ?>" />
    <meta property="og:url" content="<?php echo esc_url( is_single() || is_page() ? get_permalink() : home_url( '/' ) ); ?>" />
    <?php
    $og_image = '';
    $og_image_alt = get_bloginfo( 'description' );
    if ( is_single() || is_page() ) {
        $og_image_alt = get_bloginfo( 'description' );
        if ( has_post_thumbnail() ) {
            $og_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
            $thumbnail_id = get_post_thumbnail_id( get_the_ID() );
            $alt = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
            if ( ! empty( $alt ) ) {
                $og_image_alt = $alt;
            }
        }
    }
    if ( empty( $og_image ) ) {
        $og_image = get_template_directory_uri() . '/assets/images/E0A2F353-771A-4907-AF89-9A34B03AFD42.png';
    }
    ?>
    <meta property="og:image" content="<?php echo esc_url( $og_image ); ?>" />
    <meta property="og:image:alt" content="<?php echo esc_attr( $og_image_alt ); ?>" />
    
    <!-- Favicon & Icons (Chuẩn SEO & Retina) -->
    <link rel="icon" href="<?php echo esc_url( get_template_directory_uri() . '/assets/images/favicon.svg' ); ?>" type="image/svg+xml" />
    <link rel="apple-touch-icon" href="<?php echo esc_url( get_template_directory_uri() . '/assets/images/Logo-TechBlog.png' ); ?>" />
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url( get_template_directory_uri() . '/assets/images/Logo-TechBlog.png' ); ?>" />
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo esc_url( get_template_directory_uri() . '/assets/images/Logo-TechBlog.png' ); ?>" />

    <!-- Google Fonts (Premium Be Vietnam Pro Font) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN (With console warning suppression) -->
    <script>
        (function() {
            const originalWarn = console.warn;
            console.warn = function(...args) {
                if (args[0] && typeof args[0] === 'string' && args[0].includes('cdn.tailwindcss.com')) {
                    return;
                }
                originalWarn.apply(console, args);
            };
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb', // Royal Blue (Xanh chủ đạo)
                        'primary-container': '#eff6ff',
                        background: '#f8fafc',
                        surface: '#ffffff',
                        'surface-container': '#f1f5f9',
                        'surface-container-low': '#f8fafc',
                        'on-surface': '#0f172a',
                        'on-surface-variant': '#475569',
                        'outline-variant': '#e2e8f0',
                        'slate-855': '#1e293b'
                    },
                    fontFamily: {
                        sans: ['"Be Vietnam Pro"', 'sans-serif'],
                        display: ['"Be Vietnam Pro"', 'sans-serif']
                    },
                    spacing: {
                        'section-gap': '2rem',
                        'gutter': '1.5rem'
                    },
                    maxWidth: {
                        'container-max': '1280px',
                        'article-max': '720px'
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer base {
            html, body {
                @apply bg-background text-on-surface font-sans antialiased selection:bg-primary selection:text-white;
                overflow-x: hidden;
                width: 100%;
            }
        }
        /* Custom styles to prevent layout shifts */
        .sticky-header {
            @apply sticky top-0 z-40 bg-white border-b border-slate-100 shadow-sm;
        }
        /* Custom styled bottom dots on active menus */
        .nav-link-active {
            @apply text-primary font-bold relative;
        }
        .nav-link-active::after {
            content: '';
            @apply absolute bottom-0 left-0 w-full h-0.5 bg-primary;
        }
    </style>

    <?php wp_head(); ?>

    <!-- SEO Structured Data Schema JSON-LD for Search Console Rich Snippets -->
    <?php if ( is_single() ) : global $post; ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "TechArticle",
      "headline": "<?php echo esc_attr( get_the_title() ); ?>",
      "image": [
        "<?php echo esc_url( has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'full' ) : techblog_get_placeholder_img() ); ?>"
      ],
      "datePublished": "<?php echo get_the_date('c'); ?>",
      "dateModified": "<?php echo get_the_modified_date('c'); ?>",
      "author": {
        "@type": "Person",
        "name": "Admin TechBlog"
      },
      "publisher": {
        "@type": "Organization",
        "name": "<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>",
        "logo": {
          "@type": "ImageObject",
          "url": "<?php echo esc_url( get_template_directory_uri() . '/assets/images/Logo-TechBlog-header.png' ); ?>"
        }
      },
      "description": "<?php echo esc_attr( wp_strip_all_tags( get_the_excerpt() ) ); ?>"
    }
    </script>
    <?php endif; ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
$posts_page_id = get_option( 'page_for_posts' );
$posts_page_url = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/' );
?>

<!-- Upper Header with Date and Real-time Greeting (No Ticker) -->
<div class="bg-slate-50 border-b border-slate-100 text-slate-500 py-2.5 text-[11px] font-bold hidden lg:block">
    <div class="max-w-container-max mx-auto px-4 flex items-center justify-between">
        <!-- Left: Date & Greeting -->
        <div class="flex items-center gap-4">
            <!-- Date display -->
            <div class="flex items-center gap-2">
                <?php echo techjournal_get_svg( 'calendar', 'w-4 h-4 fill-current text-slate-400' ); ?>
                <span><?php echo date_i18n('l, d F Y'); ?></span>
            </div>
            
            <!-- Divider -->
            <span class="text-slate-300">|</span>
            
            <!-- Greeting Display Only (Client-side JS to bypass WP Timezone and Caching issues) -->
            <span id="header-greeting" class="text-primary uppercase tracking-wider"></span>
        </div>
        
        <!-- Right: Social shortcuts -->
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-3 text-slate-400">
                <a href="https://www.facebook.com/TechBlog.contact/" aria-label="Facebook" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors" title="Facebook">
                    <?php echo techjournal_get_svg( 'facebook', 'w-5 h-5' ); ?>
                </a>
                <a href="https://t.me/ngovantoanttb" aria-label="Telegram" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors" title="Telegram">
                    <?php echo techjournal_get_svg( 'telegram', 'w-5 h-5' ); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Sticky Header Bar -->
<header class="sticky-header" role="banner">
    <div class="max-w-container-max mx-auto px-4 h-16 flex items-center justify-between gap-4">
        <!-- Logo (Đã thay logo techblog-logo.svg vào theo yêu cầu) -->
        <div class="flex items-center transition-all shrink-0">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="flex items-center">
                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/Logo-TechBlog-header.png' ); ?>" alt="<?php bloginfo( 'name' ); ?> Logo" class="h-8 w-auto block" />
            </a>
        </div>

        <!-- Desktop Navigation Navbar (Aligned Right - Gồm Home, Bài viết, Giới thiệu, Liên hệ và Ô Tìm kiếm cho nhập luôn) -->
        <nav class="hidden lg:flex items-center gap-6 ml-auto" role="navigation" aria-label="Desktop Primary Navigation Menu">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="text-[12px] font-black uppercase tracking-wider transition-colors py-2 <?php echo is_front_page() ? 'nav-link-active' : 'text-slate-600 hover:text-primary'; ?>">
                Trang chủ
            </a>
            
            <div class="relative group shrink-0">
                <a href="<?php echo esc_url( $posts_page_url ); ?>" class="text-[12px] font-black uppercase tracking-wider transition-colors flex items-center gap-0.5 cursor-pointer py-2 <?php echo (is_home() || is_archive() || is_category()) ? 'nav-link-active' : 'text-slate-600 hover:text-primary'; ?>">
                    Bài viết
                    <?php echo techjournal_get_svg( 'chevron-down', 'w-3.5 h-3.5 pointer-events-none transition-transform duration-200 group-hover:rotate-180 fill-current' ); ?>
                </a>
                <!-- Dropdown Menu of Categories (Zero border-radius premium design) -->
                <div class="absolute left-0 top-full mt-0 hidden group-hover:block bg-white border border-slate-200/80 shadow-md py-1.5 min-w-[200px] z-50 transform origin-top transition-all duration-200">
                    <?php
                    $exclude_ids = array();
                    $default_cat_id = (int) get_option( 'default_category' );
                    if ( $default_cat_id ) {
                        $default_cat = get_category( $default_cat_id );
                        if ( $default_cat && in_array( $default_cat->slug, array( 'chua-phan-loai', 'uncategorized' ) ) ) {
                            $exclude_ids[] = $default_cat_id;
                        }
                    }
                    $menu_cats = get_categories( array(
                        'orderby'    => 'count',
                        'order'      => 'DESC',
                        'parent'     => 0,
                        'hide_empty' => false,
                        'exclude'    => $exclude_ids
                    ) );
                    if ( ! empty( $menu_cats ) ) {
                        foreach ( $menu_cats as $cat ) {
                            $cat_link = get_category_link( $cat->term_id );
                            echo '<a href="' . esc_url( $cat_link ) . '" class="block px-4 py-2.5 text-[11px] font-bold uppercase tracking-wider text-slate-600 hover:bg-slate-50 hover:text-primary transition-all border-l-2 border-transparent hover:border-primary">' . esc_html( $cat->name ) . '</a>';
                        }
                    } else {
                        echo '<span class="block px-4 py-2 text-[10px] text-slate-400 italic">Chưa có chuyên mục</span>';
                    }
                    ?>
                </div>
            </div>

            <?php
            $about_page = get_page_by_path('gioi-thieu');
            if ( $about_page ) :
            ?>
                <a href="<?php echo esc_url( get_permalink( $about_page->ID ) ); ?>" class="text-[12px] font-black uppercase tracking-wider transition-colors py-2 <?php echo is_page( $about_page->ID ) ? 'nav-link-active' : 'text-slate-600 hover:text-primary'; ?>">
                    Giới thiệu
                </a>
            <?php endif; ?>

            <?php
            $contact_page = get_page_by_path('lien-he');
            if ( $contact_page ) :
            ?>
                <a href="<?php echo esc_url( get_permalink( $contact_page->ID ) ); ?>" class="text-[12px] font-black uppercase tracking-wider transition-colors py-2 <?php echo is_page( $contact_page->ID ) ? 'nav-link-active' : 'text-slate-600 hover:text-primary'; ?>">
                    Liên hệ
                </a>
            <?php endif; ?>

            <!-- Inline Search Form (Cho gõ nhập tìm kiếm luôn trực tiếp) -->
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="relative flex items-center bg-slate-50 border border-slate-200 focus-within:border-primary/80 transition-all pl-3.5 pr-0.5 w-[160px] lg:w-[220px] h-12">
                <input type="search" placeholder="TÌM KIẾM..." name="s" value="<?php echo get_search_query(); ?>" class="w-full h-full text-[12px] text-slate-700 placeholder-slate-400 placeholder:text-[10px] placeholder:font-bold placeholder:tracking-wider focus:outline-none bg-transparent" />
                <button type="submit" aria-label="Tìm kiếm" class="w-12 h-12 text-slate-500 hover:text-primary transition-colors cursor-pointer flex items-center justify-center shrink-0">
                    <?php echo techjournal_get_svg( 'search', 'w-[18px] h-[18px]' ); ?>
                </button>
            </form>
        </nav>

        <!-- Header Actions (Commented search button & contact CTA button as requested, only mobile hamburger kept) -->
        <div class="flex items-center gap-3">
            <?php /*
            <!-- Search trigger button (Commented out as requested) -->
            <button onclick="toggleHeaderSearch()" aria-label="Open Search Box" class="w-9 h-9 border border-slate-200 text-slate-500 hover:text-primary hover:border-primary flex items-center justify-center cursor-pointer active:scale-95 transition-all">
                <?php echo techjournal_get_svg( 'search', 'w-5 h-5 fill-current' ); ?>
            </button>
            */ ?>

            <?php /*
            <!-- Contact CTA button (Commented out as it is merged into the main navbar) -->
            <?php
            $contact_page = get_page_by_path('lien-he');
            if ( $contact_page ) :
            ?>
                <a href="<?php echo esc_url( get_permalink( $contact_page->ID ) ); ?>" class="hidden sm:flex bg-primary hover:bg-primary/95 text-white text-[10px] font-black uppercase px-4 py-2.5 tracking-wider items-center gap-1.5 transition-colors">
                    Liên hệ <?php echo techjournal_get_svg( 'arrow_right_alt', 'w-3.5 h-3.5 fill-current' ); ?>
                </a>
            <?php endif; ?>
            */ ?>

            <!-- Mobile Hamburger Menu Button -->
            <button onclick="toggleMobileCategoryDrawer()" aria-label="Open mobile categories drawer" class="lg:hidden w-12 h-12 border border-slate-200 text-slate-500 hover:text-primary hover:border-primary flex items-center justify-center cursor-pointer active:scale-95 transition-all">
                <?php echo techjournal_get_svg( 'menu', 'w-6 h-6 fill-current' ); ?>
            </button>
        </div>
    </div>

    <?php /*
    <!-- Dropdown Search Bar (Commented out as requested for inline input) -->
    <div id="header-search-bar" class="absolute left-0 w-full bg-white border-b border-slate-100 shadow-md py-4 px-4 hidden z-50">
        <div class="max-w-container-max mx-auto">
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex gap-2">
                <input type="search" placeholder="Nhập từ khóa tìm kiếm..." name="s" value="<?php echo get_search_query(); ?>" class="flex-grow bg-slate-50 border border-slate-200 px-4 py-2.5 text-[13px] font-medium focus:outline-none focus:border-primary" />
                <button type="submit" class="bg-primary hover:bg-primary/90 text-white font-bold px-6 py-2.5 text-[11px] uppercase tracking-wider transition-colors cursor-pointer">
                    Tìm kiếm
                </button>
            </form>
        </div>
    </div>
    */ ?>

    <!-- ================= CATEGORY QUICK-NAVBAR SUB-HEADER (Red for Active Category only) ================= -->
    <?php if ( is_front_page() || is_home() || is_archive() || is_search() || is_single() ) : ?>
    <div class="bg-white border-b border-slate-100/80 py-3 hidden lg:block max-lg:!hidden">
        <div class="max-w-container-max mx-auto px-4 flex items-center justify-between">
            <div class="flex flex-wrap items-center gap-2 py-1">
                <a href="<?php echo esc_url( $posts_page_url ); ?>" 
                   class="px-3.5 py-1.5 text-xs font-bold uppercase tracking-wider transition-all <?php echo (!is_category() && !is_single() && !is_search()) ? 'bg-primary text-white shadow-sm font-black' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-primary'; ?>">
                    Tất Cả
                </a>
                <?php
                $exclude_ids = array();
                $default_cat_id = (int) get_option( 'default_category' );
                if ( $default_cat_id ) {
                    $default_cat = get_category( $default_cat_id );
                    if ( $default_cat && in_array( $default_cat->slug, array( 'chua-phan-loai', 'uncategorized' ) ) ) {
                        $exclude_ids[] = $default_cat_id;
                    }
                }

                $all_cats = get_categories( array(
                    'orderby'    => 'count',
                    'order'      => 'DESC',
                    'parent'     => 0,
                    'hide_empty' => false,
                    'exclude'    => $exclude_ids
                ) );
                
                $current_cat_id = 0;
                if ( is_single() ) {
                    $post_cats = get_the_category();
                    if ( ! empty( $post_cats ) ) {
                        $current_cat_id = $post_cats[0]->term_id;
                    }
                }
                
                foreach ( $all_cats as $cat ) :
                    $cat_link = get_category_link( $cat->term_id );
                    $is_active = is_category( $cat->term_id ) || (is_single() && $current_cat_id === $cat->term_id);
                    
                    $sub_cats = get_categories( array(
                        'orderby'    => 'count',
                        'order'      => 'DESC',
                        'parent'     => $cat->term_id,
                        'hide_empty' => false
                    ) );
                    
                    if ( ! empty( $sub_cats ) ) {
                        foreach ( $sub_cats as $sub ) {
                            if ( is_category( $sub->term_id ) || (is_single() && $current_cat_id === $sub->term_id) ) {
                                $is_active = true;
                                break;
                            }
                        }
                    }
                    
                    $active_class = $is_active ? 'bg-primary text-white shadow-sm font-black' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-primary';
                    
                    if ( ! empty( $sub_cats ) ) :
                    ?>
                        <div class="relative group shrink-0 z-30">
                            <a href="<?php echo esc_url( $cat_link ); ?>" class="px-3.5 py-1.5 text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-1 <?php echo $active_class; ?>">
                                <?php echo esc_html( $cat->name ); ?>
                                <?php echo techjournal_get_svg( 'chevron-down', 'w-3.5 h-3.5 pointer-events-none fill-current' ); ?>
                            </a>
                            <!-- Dropdown Menu -->
                            <div class="absolute left-0 top-full mt-1.5 hidden group-hover:block bg-white border border-slate-200/80 shadow-md py-1.5 min-w-[180px] z-50 transform origin-top transition-all duration-200">
                                <?php foreach ( $sub_cats as $sub_cat ) : 
                                    $sub_active = is_category( $sub_cat->term_id ) || (is_single() && $current_cat_id === $sub_cat->term_id);
                                ?>
                                    <a href="<?php echo esc_url( get_category_link( $sub_cat->term_id ) ); ?>" class="block px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-primary border-b border-slate-100/50 last:border-0 transition-colors uppercase tracking-wider <?php echo $sub_active ? 'text-primary bg-slate-50 font-black' : ''; ?>">
                                        <?php echo esc_html( $sub_cat->name ); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <a href="<?php echo esc_url( $cat_link ); ?>" class="px-3.5 py-1.5 text-xs font-bold uppercase tracking-wider transition-all shrink-0 <?php echo $active_class; ?>">
                            <?php echo esc_html( $cat->name ); ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <!-- Right quick motto -->
            <div class="text-[10px] text-slate-600 font-bold uppercase tracking-wider hidden lg:block">
                Khám Phá Công Nghệ Mới Mỗi Ngày
            </div>
        </div>
    </div>
    
     <!-- Mobile category scroll list (Commented out as requested to use only the mobile menu) -->
    <!-- <div class="bg-white border-b border-slate-100/80 py-2.5 overflow-x-auto scrollbar-hide flex gap-2 px-4 lg:hidden">
        <a href="<?php echo esc_url( $posts_page_url ); ?>" 
           class="px-3.5 py-1.5 text-[10.5px] font-bold uppercase tracking-wider shrink-0 transition-all <?php echo (!is_category() && !is_single() && !is_search()) ? 'bg-primary text-white shadow-sm font-black' : 'bg-slate-50 text-slate-600'; ?>">
            Tất Cả
        </a>
        <?php
        $mobile_cats = get_categories( array(
            'orderby'    => 'count',
            'order'      => 'DESC',
            'hide_empty' => false,
            'exclude'    => $exclude_ids
        ) );
        foreach ( $mobile_cats as $cat ) :
            $is_active = is_category( $cat->term_id ) || (is_single() && $current_cat_id === $cat->term_id);
            $active_class = $is_active ? 'bg-primary text-white shadow-sm font-black' : 'bg-slate-50 text-slate-600';
            ?>
            <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="px-3.5 py-1.5 text-[10.5px] font-bold uppercase tracking-wider shrink-0 transition-all <?php echo $active_class; ?>">
                <?php echo esc_html( $cat->name ); ?>
            </a>
        <?php endforeach; ?>
    </div> -->

    <?php endif; ?>
</header>

<script>
    // Header Dropdown Search logic (Commented out as it is now inline)
    /*
    function toggleHeaderSearch() {
        const searchBar = document.getElementById('header-search-bar');
        if (searchBar.classList.contains('hidden')) {
            searchBar.classList.remove('hidden');
            searchBar.querySelector('input').focus();
        } else {
            searchBar.classList.add('hidden');
        }
    }
    */

    document.addEventListener('DOMContentLoaded', function() {
        // Greeting Display Only (Client-side JS to bypass WP Timezone and Caching issues)
        const greetingSpan = document.getElementById("header-greeting");
        if (greetingSpan) {
            const hr = new Date().getHours();
            let greeting = "";
            if (hr >= 1 && hr <= 10) greeting = "Chúc bạn buổi sáng vui vẻ";
            else if (hr >= 11 && hr <= 12) greeting = "Chúc bạn buổi trưa vui vẻ";
            else if (hr >= 13 && hr <= 17) greeting = "Chúc bạn buổi chiều vui vẻ";
            else greeting = "Chúc bạn buổi tối vui vẻ";
            greetingSpan.textContent = greeting.toUpperCase();
        }
    });
</script>
