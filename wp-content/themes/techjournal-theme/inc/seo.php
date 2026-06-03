<?php
/**
 * TechJournal Dynamic Google SEO and Structured Data (Schema JSON-LD)
 *
 * @package TechJournal
 * @since 1.0.0
 */

// 1. Dynamic Premium SEO & Open Graph Meta Tags Injection in wp_head
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

// 2. Google Structured Data (Schema JSON-LD) builder for perfect rich results
function techjournal_inject_schema_json_ld() {
    $schema = array();
    $site_name = get_bloginfo( 'name' );
    $site_url = home_url( '/' );
    $logo_url = get_template_directory_uri() . '/assets/images/Logo-TechBlog.png';
    
    // Core Publisher Organization schema
    $publisher = array(
        '@type' => 'Organization',
        'name' => $site_name,
        'url' => $site_url,
        'logo' => array(
            '@type' => 'ImageObject',
            'url' => $logo_url,
            'width' => 200,
            'height' => 60
        )
    );

    if ( is_front_page() || is_home() ) {
        // Website and Organization Schema
        $schema[] = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $site_name,
            'url' => $site_url,
            'potentialAction' => array(
                '@type' => 'SearchAction',
                'target' => $site_url . '?s={search_term_string}',
                'query-input' => 'required name=search_term_string'
            )
        );
        
        $schema[] = array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $site_name,
            'url' => $site_url,
            'logo' => $logo_url,
            'sameAs' => array(
                'https://www.facebook.com/TechBlog.contact',
                'https://twitter.com/techblog'
            )
        );
    } elseif ( is_single() ) {
        // TechArticle schema
        global $post;
        setup_postdata( $post );
        
        $image_url = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'large' ) : get_template_directory_uri() . '/assets/images/techblog-banne.svg';
        $author_name = (strcasecmp(get_the_author(), 'admin') === 0) ? 'Ngo Van Toan' : get_the_author();
        
        $schema[] = array(
            '@context' => 'https://schema.org',
            '@type' => 'TechArticle',
            'mainEntityOfPage' => array(
                '@type' => 'WebPage',
                '@id' => get_permalink()
            ),
            'headline' => get_the_title(),
            'image' => $image_url,
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => array(
                '@type' => 'Person',
                'name' => $author_name,
                'url' => get_author_posts_url( get_the_author_meta( 'ID' ) )
            ),
            'publisher' => $publisher,
            'description' => wp_strip_all_tags( get_the_excerpt() )
        );
    } elseif ( is_category() ) {
        // CollectionPage Schema
        $cat = get_queried_object();
        $schema[] = array(
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => single_cat_title( '', false ),
            'url' => get_category_link( $cat->term_id ),
            'description' => wp_strip_all_tags( category_description() )
        );
    } elseif ( is_page() ) {
        // WebPage, AboutPage, or ContactPage Schema
        $page_type = 'WebPage';
        if ( is_page_template( 'page-gioi-thieu.php' ) ) {
            $page_type = 'AboutPage';
        } elseif ( is_page_template( 'page-lien-he.php' ) ) {
            $page_type = 'ContactPage';
        }
        
        $schema[] = array(
            '@context' => 'https://schema.org',
            '@type' => $page_type,
            'name' => get_the_title(),
            'url' => get_permalink(),
            'description' => wp_strip_all_tags( get_the_excerpt() )
        );
    } elseif ( is_search() ) {
        // SearchResultsPage Schema
        $schema[] = array(
            '@context' => 'https://schema.org',
            '@type' => 'SearchResultsPage',
            'name' => 'Kết quả tìm kiếm cho: ' . get_search_query(),
            'url' => home_url( '/' ) . '?s=' . urlencode( get_search_query() )
        );
    }

    if ( ! empty( $schema ) ) {
        echo "\n" . '<!-- Google Structured Data (JSON-LD) injected by TechJournal -->' . "\n";
        foreach ( $schema as $item ) {
            echo '<script type="application/ld+json">' . json_encode( $item, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . '</script>' . "\n";
        }
    }
}
add_action( 'wp_head', 'techjournal_inject_schema_json_ld' );
