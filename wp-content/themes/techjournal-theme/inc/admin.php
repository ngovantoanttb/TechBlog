<?php
/**
 * TechJournal Admin Panel Contact Management Interfaces
 *
 * @package TechJournal
 * @since 1.0.0
 */

// 1. Register custom admin menu tab for Contact Submissions
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

// 2. Create WordPress List Table Class for Custom Database Table
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

// 3. Render Custom Isolated Contact Admin Page with Visual Editor Detail Screen
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
