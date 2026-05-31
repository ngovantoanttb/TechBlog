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
            
            // Format output markup based on query context (Search/Category/Archive = Flex Row, Homepage = Grid Card)
            if ( ! empty( $search ) || $cat_id > 0 ) {
                get_template_part( 'template-parts/content-card' );
            } else {
                get_template_part( 'template-parts/content-grid' );
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

// 12. Helper to check if an email has a custom Gravatar registered (with daily Transient Caching)
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

// 13. Custom Premium Comment Formatting Callback
if ( ! function_exists( 'techjournal_custom_comment_callback' ) ) {
    function techjournal_custom_comment_callback( $comment, $args, $depth ) {
        $GLOBALS['comment'] = $comment;
        ?>
        <li <?php comment_class( 'border-b border-slate-100 last:border-0 pb-6 mb-6 list-none' ); ?> id="li-comment-<?php comment_ID(); ?>">
            <div id="comment-<?php comment_ID(); ?>" class="flex gap-4">
                <div class="shrink-0">
                    <?php
                    $is_admin_commenter = false;
                    $commenter_email = $comment->comment_author_email;
                    
                    if ( $comment->user_id ) {
                        $user = get_userdata( $comment->user_id );
                        if ( $user && in_array( 'administrator', (array) $user->roles ) ) {
                            $is_admin_commenter = true;
                            $commenter_email = $user->user_email;
                        }
                    } else if ( ! empty( $commenter_email ) ) {
                        $admin_email = get_option( 'admin_email' );
                        if ( strtolower( trim( $commenter_email ) ) === strtolower( trim( $admin_email ) ) ) {
                            $is_admin_commenter = true;
                        }
                    }
                    
                    if ( $is_admin_commenter ) {
                        if ( techjournal_has_gravatar( $commenter_email ) ) {
                            echo get_avatar( $comment, 40, '', '', array( 'class' => 'rounded-none w-10 h-10 object-cover' ) );
                        } else {
                            $logo_url = get_template_directory_uri() . '/assets/images/Logo-TechBlog.png';
                            ?>
                            <img src="<?php echo esc_url( $logo_url ); ?>" class="rounded-none w-10 h-10 object-contain block shrink-0 bg-slate-100 p-1" alt="Ảnh đại diện TechBlog" />
                            <?php
                        }
                    } else {
                        ?>
                        <img src="<?php echo get_template_directory_uri() ?>/assets/images/anony.jpg" class="rounded-none w-10 h-10 object-cover block shrink-0" alt="<?php echo esc_attr( get_comment_author() ); ?>" />
                        <?php
                    }
                    ?>
                </div>
                <div class="flex-grow">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="font-bold text-[12.5px] text-slate-805"><?php comment_author_link(); ?></span>
                        <span class="text-[10.5px] text-slate-400 font-bold">
                            <?php 
                            $comment_timestamp = get_comment_time( 'U', true );
                            $current_timestamp = time();
                            $time_diff = $current_timestamp - $comment_timestamp;
                            
                            if ( $time_diff >= -60 && $time_diff < 86400 ) {
                                if ( $time_diff < 60 ) {
                                    echo esc_html__( 'vừa xong', 'techjournal' );
                                } else {
                                    $relative = human_time_diff( $comment_timestamp, $current_timestamp );
                                    printf( esc_html__( '%s trước', 'techjournal' ), $relative );
                                }
                            } else {
                                echo get_comment_date('d/m/Y') . ' lúc ' . get_comment_time('H:i');
                            }
                            ?>
                        </span>
                    </div>
                    <div class="text-[13px] leading-relaxed text-slate-650 prose max-w-none">
                        <?php comment_text(); ?>
                    </div>
                    <div class="mt-3 flex items-center gap-4 text-[10px] font-bold uppercase tracking-wider">
                        <?php
                        comment_reply_link( array_merge( $args, array(
                            'reply_text' => esc_html__( 'Phản hồi', 'techjournal' ),
                            'depth'      => $depth,
                            'max_depth'  => $args['max_depth'],
                            'class'      => 'text-primary hover:underline'
                        ) ) );
                        ?>
                        <?php edit_comment_link( esc_html__( 'Sửa', 'techjournal' ), '<span class="text-slate-400">|</span> ', '' ); ?>
                    </div>
                </div>
            </div>
        </li>
        <?php
    }
}

// 13. Reorder and Filter Comment Form Fields (Tên -> Email -> Bình luận)
if ( ! function_exists( 'techjournal_reorder_comment_fields' ) ) {
    function techjournal_reorder_comment_fields( $fields ) {
        $new_fields = array();
        
        // Exact order required: Tên (author) -> Email (email) -> Bình luận (comment)
        // Completely omits url (Trang web) and cookies (consent checkbox)
        if ( isset( $fields['author'] ) ) {
            $new_fields['author'] = $fields['author'];
        }
        if ( isset( $fields['email'] ) ) {
            $new_fields['email'] = $fields['email'];
        }
        if ( isset( $fields['comment'] ) ) {
            $new_fields['comment'] = $fields['comment'];
        }
        
        return $new_fields;
    }
}
add_filter( 'comment_form_fields', 'techjournal_reorder_comment_fields' );

// 14. Programmatic Custom Permalinks Setup (danh-sach-bai-viet/post-name)
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

// 15. AJAX Handler for Contact Form Submission with Custom Database Logging
function techblog_handle_contact_submission() {
    // 1. Verify Nonce
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'techblog_contact_nonce' ) ) {
        wp_send_json_error( array( 'message' => esc_html__( 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang.', 'techjournal' ) ) );
    }
    
    // 2. Validate Inputs
    $name    = isset( $_POST['c_name'] ) ? sanitize_text_field( $_POST['c_name'] ) : '';
    $email   = isset( $_POST['c_email'] ) ? sanitize_email( $_POST['c_email'] ) : '';
    $subject = isset( $_POST['c_subject'] ) ? sanitize_text_field( $_POST['c_subject'] ) : '';
    $message = isset( $_POST['c_message'] ) ? sanitize_textarea_field( $_POST['c_message'] ) : '';
    
    if ( empty( $name ) || empty( $email ) || empty( $message ) ) {
        wp_send_json_error( array( 'message' => esc_html__( 'Vui lòng điền đầy đủ các trường bắt buộc.', 'techjournal' ) ) );
    }
    
    if ( ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => esc_html__( 'Địa chỉ email không hợp lệ.', 'techjournal' ) ) );
    }
    
    // 3. Log Submission into Isolated Custom Database Table 'wp_techblog_contacts'
    global $wpdb;
    $table_name = $wpdb->prefix . 'techblog_contacts';
    $db_inserted = $wpdb->insert(
        $table_name,
        array(
            'name'       => $name,
            'email'      => $email,
            'subject'    => $subject,
            'message'    => $message,
            'status'     => 'unread',
            'created_at' => current_time( 'mysql' ),
        )
    );
    
    // 4. Construct Email Notification
    $to = get_option( 'admin_email' );
    $email_subject = 'Liên hệ mới từ ' . $name;
    if ( ! empty( $subject ) ) {
        $email_subject .= ' - ' . $subject;
    }
    
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $name . ' <' . $to . '>', 
        'Reply-To: ' . $name . ' <' . $email . '>'
    );
    
    $body  = '<h2>Bạn nhận được một liên hệ mới từ website TechBlog:</h2>';
    $body .= '<p><strong>Họ tên:</strong> ' . esc_html( $name ) . '</p>';
    $body .= '<p><strong>Email:</strong> ' . esc_html( $email ) . '</p>';
    $body .= '<p><strong>Tiêu đề:</strong> ' . esc_html( $subject ) . '</p>';
    $body .= '<p><strong>Nội dung:</strong></p>';
    $body .= '<div style="background:#f4f4f5; padding:15px; border-left:4px solid #2563eb; margin:15px 0; font-style:italic; font-size:14px; line-height:1.6;">' . nl2br( esc_html( $message ) ) . '</div>';
    
    // 5. Send Email
    $sent = wp_mail( $to, $email_subject, $body, $headers );
    
    if ( $db_inserted ) {
        wp_send_json_success( array( 'message' => esc_html__( 'Liên hệ đã được gửi thành công.', 'techjournal' ) ) );
    } else {
        wp_send_json_error( array( 'message' => esc_html__( 'Không thể gửi liên hệ. Vui lòng thử lại.', 'techjournal' ) ) );
    }
}
add_action( 'wp_ajax_techblog_submit_contact', 'techblog_handle_contact_submission' );
add_action( 'wp_ajax_nopriv_techblog_submit_contact', 'techblog_handle_contact_submission' );

// 16. Initialize Isolated Database Table for Contacts
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

// 17. Register custom admin menu tab for Contact Submissions
function techblog_register_contact_admin_menu() {
    add_menu_page(
        'Danh sách liên hệ',           // Page Title
        'Liên hệ',                    // Menu Title
        'manage_options',             // Capability
        'techblog-contacts',          // Menu Slug
        'techblog_render_contacts_admin_page', // Callback
        'dashicons-email',            // Icon
        25                            // Position
    );
}
add_action( 'admin_menu', 'techblog_register_contact_admin_menu' );

// 18. Create WordPress List Table Class for Custom Database Table
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class TechBlog_Contacts_List_Table extends WP_List_Table {
    
    public function __construct() {
        parent::__construct( array(
            'singular' => 'contact',
            'plural'   => 'contacts',
            'ajax'     => false
        ) );
    }
    
    public function get_columns() {
        return array(
            'cb'         => '<input type="checkbox" />',
            'name'       => 'Họ và Tên',
            'email'      => 'Email',
            'subject'    => 'Tiêu đề',
            'message'    => 'Nội dung liên hệ',
            'created_at' => 'Thời gian'
        );
    }
    
    public function get_sortable_columns() {
        return array(
            'name'       => array( 'name', false ),
            'email'      => array( 'email', false ),
            'created_at' => array( 'created_at', true )
        );
    }
    
    public function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />',
            $item['id']
        );
    }
    
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'email':
                return sprintf( '<a href="mailto:%1$s">%1$s</a>', esc_html( $item['email'] ) );
            case 'subject':
                return esc_html( $item['subject'] ? $item['subject'] : '(Không có tiêu đề)' );
            case 'message':
                return esc_html( wp_trim_words( $item['message'], 20, '...' ) );
            case 'created_at':
                return esc_html( date( 'd/m/Y - H:i', strtotime( $item['created_at'] ) ) );
            default:
                return isset( $item[ $column_name ] ) ? esc_html( $item[ $column_name ] ) : '';
        }
    }
    
    public function column_name( $item ) {
        $page = sanitize_text_field( $_REQUEST['page'] );
        
        $actions = array();
        if ( 'trash' === $item['status'] ) {
            $restore_nonce = wp_create_nonce( 'techblog_restore_contact_' . $item['id'] );
            $delete_nonce  = wp_create_nonce( 'techblog_delete_contact_' . $item['id'] );
            
            $actions['restore'] = sprintf(
                '<a href="?page=%s&action=restore&id=%s&_wpnonce=%s">Khôi phục</a>',
                $page, $item['id'], $restore_nonce
            );
            $actions['delete']  = sprintf(
                '<a href="?page=%s&action=delete&id=%s&_wpnonce=%s" onclick="return confirm(\'Bạn có chắc chắn muốn xóa vĩnh viễn liên hệ này?\')">Xóa vĩnh viễn</a>',
                $page, $item['id'], $delete_nonce
            );
        } else {
            $trash_nonce = wp_create_nonce( 'techblog_trash_contact_' . $item['id'] );
            
            $actions['view']  = sprintf(
                '<a href="?page=%s&action=view&id=%s">Xem chi tiết</a>',
                $page, $item['id']
            );
            $actions['trash'] = sprintf(
                '<a href="?page=%s&action=trash&id=%s&_wpnonce=%s" style="color: #b32d2e;">Bỏ vào thùng rác</a>',
                $page, $item['id'], $trash_nonce
            );
        }
        
        $unread_style = 'unread' === $item['status'] ? 'font-weight: bold; color: #000;' : '';
        $unread_badge = 'unread' === $item['status'] ? ' <span class="update-plugins count-1" style="background:#2271b1; color:#fff; padding:1px 6px; border-radius:10px; font-size:9px; vertical-align:middle; margin-left:4px;">Chưa đọc</span>' : '';
        
        return sprintf(
            '<a href="?page=%1$s&action=view&id=%2$s" style="%5$s"><strong>%3$s</strong></a>%6$s %4$s',
            $page,
            $item['id'],
            esc_html( $item['name'] ),
            $this->row_actions( $actions ),
            $unread_style,
            $unread_badge
        );
    }
    
    public function get_views() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'techblog_contacts';
        
        $unread   = $wpdb->get_var( "SELECT COUNT(id) FROM $table_name WHERE status = 'unread'" );
        $read     = $wpdb->get_var( "SELECT COUNT(id) FROM $table_name WHERE status = 'read'" );
        $trash    = $wpdb->get_var( "SELECT COUNT(id) FROM $table_name WHERE status = 'trash'" );
        $active   = $wpdb->get_var( "SELECT COUNT(id) FROM $table_name WHERE status != 'trash'" );
        
        $current_status = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : 'all';
        
        $views = array();
        
        $views['all'] = sprintf(
            '<a href="?page=%s" class="%s">Tất cả (%d)</a>',
            'techblog-contacts',
            ( 'all' === $current_status ) ? 'current' : '',
            $active
        );
        $views['unread'] = sprintf(
            '<a href="?page=%s&status=unread" class="%s">Chưa đọc (%d)</a>',
            'techblog-contacts',
            ( 'unread' === $current_status ) ? 'current' : '',
            $unread
        );
        $views['read'] = sprintf(
            '<a href="?page=%s&status=read" class="%s">Đã đọc (%d)</a>',
            'techblog-contacts',
            ( 'read' === $current_status ) ? 'current' : '',
            $read
        );
        $views['trash'] = sprintf(
            '<a href="?page=%s&status=trash" class="%s">Thùng rác (%d)</a>',
            'techblog-contacts',
            ( 'trash' === $current_status ) ? 'current' : '',
            $trash
        );
        
        return $views;
    }
    
    public function get_bulk_actions() {
        $current_status = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : 'all';
        if ( 'trash' === $current_status ) {
            return array(
                'bulk-restore' => 'Khôi phục',
                'bulk-delete'  => 'Xóa vĩnh viễn'
            );
        } else {
            return array(
                'bulk-read'   => 'Đánh dấu đã đọc',
                'bulk-unread' => 'Đánh dấu chưa đọc',
                'bulk-trash'  => 'Bỏ vào thùng rác'
            );
        }
    }
    
    public function prepare_items() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'techblog_contacts';
        
        $this->process_bulk_action();
        
        $per_page = 15;
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array( $columns, $hidden, $sortable );
        
        $current_status = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : 'all';
        if ( 'trash' === $current_status ) {
            $where = "WHERE status = 'trash'";
        } elseif ( 'unread' === $current_status ) {
            $where = "WHERE status = 'unread'";
        } elseif ( 'read' === $current_status ) {
            $where = "WHERE status = 'read'";
        } else {
            $where = "WHERE status != 'trash'";
        }
        
        $search = isset( $_REQUEST['s'] ) ? sanitize_text_field( $_REQUEST['s'] ) : '';
        if ( ! empty( $search ) ) {
            $where .= $wpdb->prepare(
                " AND (name LIKE %s OR email LIKE %s OR subject LIKE %s OR message LIKE %s)",
                '%' . $wpdb->esc_like( $search ) . '%',
                '%' . $wpdb->esc_like( $search ) . '%',
                '%' . $wpdb->esc_like( $search ) . '%',
                '%' . $wpdb->esc_like( $search ) . '%'
            );
        }
        
        $orderby = isset( $_GET['orderby'] ) ? sanitize_sql_orderby( $_GET['orderby'] ) : 'created_at';
        $order   = isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'DESC';
        if ( ! in_array( strtoupper( $order ), array( 'ASC', 'DESC' ) ) ) {
            $order = 'DESC';
        }
        
        $current_page = $this->get_pagenum();
        $offset = ( $current_page - 1 ) * $per_page;
        
        $total_items = $wpdb->get_var( "SELECT COUNT(id) FROM $table_name $where" );
        
        $query = "SELECT * FROM $table_name $where ORDER BY $orderby $order LIMIT $per_page OFFSET $offset";
        $this->items = $wpdb->get_results( $query, ARRAY_A );
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil( $total_items / $per_page )
        ) );
    }
    
    public function process_bulk_action() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'techblog_contacts';
        
        $action = $this->current_action();
        
        if ( isset( $_GET['action'] ) && isset( $_GET['id'] ) ) {
            $id = intval( $_GET['id'] );
            
            if ( 'trash' === $_GET['action'] ) {
                check_admin_referer( 'techblog_trash_contact_' . $id );
                $wpdb->update( $table_name, array( 'status' => 'trash' ), array( 'id' => $id ) );
                echo '<div class="notice notice-success is-dismissible"><p>Đã bỏ liên hệ vào Thùng rác.</p></div>';
            } elseif ( 'restore' === $_GET['action'] ) {
                check_admin_referer( 'techblog_restore_contact_' . $id );
                $wpdb->update( $table_name, array( 'status' => 'read' ), array( 'id' => $id ) );
                echo '<div class="notice notice-success is-dismissible"><p>Đã khôi phục liên hệ thành công.</p></div>';
            } elseif ( 'delete' === $_GET['action'] ) {
                check_admin_referer( 'techblog_delete_contact_' . $id );
                $wpdb->delete( $table_name, array( 'id' => $id ) );
                echo '<div class="notice notice-success is-dismissible"><p>Đã xóa vĩnh viễn liên hệ thành công.</p></div>';
            }
        }
        
        // Handle standard bulk actions
        $bulk_ids = isset( $_POST['bulk-delete'] ) ? $_POST['bulk-delete'] : (isset($_GET['bulk-delete']) ? $_GET['bulk-delete'] : array());
        if ( ! empty( $bulk_ids ) && is_array( $bulk_ids ) ) {
            $ids = array_map( 'intval', $bulk_ids );
            $ids_placeholder = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
            
            if ( 'bulk-trash' === $action ) {
                $wpdb->query( $wpdb->prepare( "UPDATE $table_name SET status = 'trash' WHERE id IN ($ids_placeholder)", $ids ) );
                echo '<div class="notice notice-success is-dismissible"><p>Đã bỏ các liên hệ được chọn vào Thùng rác.</p></div>';
            } elseif ( 'bulk-restore' === $action ) {
                $wpdb->query( $wpdb->prepare( "UPDATE $table_name SET status = 'read' WHERE id IN ($ids_placeholder)", $ids ) );
                echo '<div class="notice notice-success is-dismissible"><p>Đã khôi phục các liên hệ được chọn.</p></div>';
            } elseif ( 'bulk-delete' === $action ) {
                $wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE id IN ($ids_placeholder)", $ids ) );
                echo '<div class="notice notice-success is-dismissible"><p>Đã xóa vĩnh viễn các liên hệ được chọn.</p></div>';
            } elseif ( 'bulk-read' === $action ) {
                $wpdb->query( $wpdb->prepare( "UPDATE $table_name SET status = 'read' WHERE id IN ($ids_placeholder)", $ids ) );
                echo '<div class="notice notice-success is-dismissible"><p>Đã đánh dấu các liên hệ được chọn là Đã đọc.</p></div>';
            } elseif ( 'bulk-unread' === $action ) {
                $wpdb->query( $wpdb->prepare( "UPDATE $table_name SET status = 'unread' WHERE id IN ($ids_placeholder)", $ids ) );
                echo '<div class="notice notice-success is-dismissible"><p>Đã đánh dấu các liên hệ được chọn là Chưa đọc.</p></div>';
            }
        }
    }
}

// 19. Render Custom Isolated Contact Admin Page with Visual Editor Detail Screen
function techblog_render_contacts_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'techblog_contacts';
    
    $action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'list';
    $id     = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
    
    // --- MODE: VIEW DETAIL (Visual Clone of the Classic Editor Post Page) ---
    if ( 'view' === $action && $id > 0 ) {
        // Mark as read immediately when viewed
        $wpdb->update( $table_name, array( 'status' => 'read' ), array( 'id' => $id ) );
        
        $contact = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ) );
        if ( ! $contact ) {
            echo '<div class="error"><p>Không tìm thấy người liên hệ này.</p></div>';
            return;
        }
        
        // Formatted editor display matching your original screenshot
        $formatted_content  = "Họ và tên: " . $contact->name . "\n";
        $formatted_content .= "Email: " . $contact->email . "\n";
        $formatted_content .= "Tiêu đề liên hệ: " . $contact->subject . "\n";
        $formatted_content .= "Nội dung liên hệ:\n" . $contact->message;
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Xem chi tiết liên hệ</h1>
            <a href="admin.php?page=techblog-contacts" class="page-title-action">Quay lại danh sách</a>
            <hr class="wp-header-end">
            
            <div id="poststuff" style="margin-top: 15px;">
                <form id="post" method="post" style="margin-bottom: 20px;">
                    <div id="post-body" class="metabox-holder columns-2">
                        
                        <!-- Left Column: Content and Custom Text Editor -->
                        <div id="post-body-content">
                            <!-- Title Box -->
                            <div id="titlediv" style="margin-bottom: 20px;">
                                <div id="titlewrap">
                                    <input type="text" name="post_title" size="30" value="<?php echo esc_attr( $contact->name ); ?>" id="title" readonly style="background-color: #fff; cursor: default; width: 100%; height: 40px; font-size: 1.7em; padding: 11px 10px; line-height: 100%; border: 1px solid #ddd; outline: none; border-radius: 4px;" placeholder="Tên người liên hệ" />
                                </div>
                            </div>
                            
                            <!-- Classic Editor Area -->
                            <div id="postdivrich" class="postarea wp-editor-expand">
                                <?php
                                wp_editor( $formatted_content, 'contact_message_editor', array(
                                    'media_buttons' => true,
                                    'textarea_name' => 'content',
                                    'textarea_rows' => 15,
                                    'teeny'         => false,
                                    'quicktags'     => true,
                                    'tinymce'       => array(
                                        'readonly' => 1
                                    )
                                ) );
                                ?>
                            </div>
                        </div>
                        
                        <!-- Right Column: Sidebar "Xuất bản" Metabox -->
                        <div id="postbox-container-1" class="postbox-container">
                            <div class="postbox" style="border: 1px solid #ccd0d4; border-radius: 4px; box-shadow: none;">
                                <div class="postbox-header" style="border-bottom: 1px solid #eee; padding: 10px 15px; background: #fafafa;">
                                    <h2 style="font-size: 14px; font-weight: 600; margin: 0; color: #23282d;">Xuất bản</h2>
                                </div>
                                <div class="inside" style="padding: 0; margin: 0;">
                                    <div id="submitdiv" class="submitbox">
                                        <div id="minor-publishing" style="padding: 15px 20px;">
                                            <div id="minor-publishing-actions" style="padding: 0 0 12px 0; border-bottom: 1px solid #eee; margin-bottom: 12px;">
                                                <a class="button" href="#" style="float: left; opacity: 0.6; pointer-events: none;" onclick="return false;">Lưu nháp</a>
                                                <div class="clear"></div>
                                            </div>
                                            
                                            <div class="misc-pub-section" style="padding: 6px 0; color: #475569;">
                                                <span class="dashicons dashicons-key" style="color:#8c8f94; vertical-align:text-bottom; margin-right:4px;"></span> Trạng thái: <strong><?php echo 'unread' === $contact->status ? 'Chưa đọc' : 'Đã đọc'; ?></strong>
                                            </div>
                                            <div class="misc-pub-section" style="padding: 6px 0; color: #475569;">
                                                <span class="dashicons dashicons-visibility" style="color:#8c8f94; vertical-align:text-bottom; margin-right:4px;"></span> Hiển thị: <strong>Công khai</strong>
                                            </div>
                                            <div class="misc-pub-section" style="padding: 6px 0; color: #475569;">
                                                <span class="dashicons dashicons-calendar" style="color:#8c8f94; vertical-align:text-bottom; margin-right:4px;"></span> Xuất bản lúc: <strong><?php echo date( 'd/m/Y \l\ú\c H:i', strtotime( $contact->created_at ) ); ?></strong>
                                            </div>
                                        </div>
                                        
                                        <div id="major-publishing-actions" style="background:#fafafa; border-top:1px solid #ddd; padding:15px 20px; display:flex; justify-content:space-between; align-items:center;">
                                            <div id="delete-action">
                                                <?php
                                                $trash_nonce = wp_create_nonce( 'techblog_trash_contact_' . $contact->id );
                                                ?>
                                                <a class="submitdelete deletion" href="<?php echo admin_url( 'admin.php?page=techblog-contacts&action=trash&id=' . $contact->id . '&_wpnonce=' . $trash_nonce ); ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa liên hệ này?');" style="color: #b32d2e; text-decoration: none; font-weight: 600;">Bỏ vào thùng rác</a>
                                            </div>
                                            <div id="publishing-action">
                                                <a href="admin.php?page=techblog-contacts" class="button button-primary button-large" style="background: #2271b1; border-color: #2271b1; color: #fff; text-shadow: none; font-weight: 600;">Quay lại</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </form>
            </div>
        </div>
        <?php
        return;
    }
    
    // --- MODE: NATIVE LIST TABLE VIEW ---
    $contacts_table = new TechBlog_Contacts_List_Table();
    $contacts_table->prepare_items();
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Người Liên Hệ</h1>
        <hr class="wp-header-end">
        
        <form id="contacts-filter" method="post" action="admin.php?page=techblog-contacts">
            <?php
            // Display top search box
            $contacts_table->search_box( 'Tìm các liên hệ', 'contacts-search' );
            // Display top tabs views
            $contacts_table->views();
            // Display the gorgeous native list table
            $contacts_table->display();
            ?>
        </form>
    </div>
    <?php
}

