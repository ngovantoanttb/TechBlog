<?php
/**
 * TechJournal Site Maintenance and Suspension Settings
 *
 * @package TechJournal
 * @since 1.0.0
 */

// 1. Register options page in Settings menu
function techblog_register_maintenance_settings() {
    add_options_page(
        'Bảo trì & Đóng Web',           // Page Title
        'Bảo trì & Đóng Web',           // Menu Title
        'manage_options',               // Capability
        'techblog-maintenance',         // Menu Slug
        'techblog_render_maintenance_page' // Callback
    );
}
add_action( 'admin_menu', 'techblog_register_maintenance_settings' );

// 2. Register settings and fields
function techblog_register_maintenance_settings_fields() {
    register_setting( 'techblog-maintenance-group', 'techblog_site_status_mode', array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => 'normal'
    ));

    register_setting( 'techblog-maintenance-group', 'techblog_maintenance_message', array(
        'type'              => 'string',
        'sanitize_callback' => 'wp_kses_post',
        'default'           => 'Hệ thống đang được nâng cấp để mang lại trải nghiệm tốt nhất cho bạn. Chúng tôi sẽ quay trở lại sớm!'
    ));

    register_setting( 'techblog-maintenance-group', 'techblog_suspended_message', array(
        'type'              => 'string',
        'sanitize_callback' => 'wp_kses_post',
        'default'           => 'Trang web này hiện đã tạm thời dừng hoạt động theo yêu cầu của ban quản trị.'
    ));
}
add_action( 'admin_init', 'techblog_register_maintenance_settings_fields' );

// 3. Render Settings Page
function techblog_render_maintenance_page() {
    ?>
    <div class="wrap">
        <h1>Cấu hình Trạng thái Website</h1>
        <p class="description">Điều chỉnh chế độ hiển thị của trang web khi cần bảo trì hệ thống hoặc đóng trang web theo yêu cầu.</p>
        <hr style="margin: 15px 0;" />

        <form method="post" action="options.php" style="margin-top: 20px; max-width: 800px;">
            <?php
            settings_fields( 'techblog-maintenance-group' );
            $current_mode = get_option( 'techblog_site_status_mode', 'normal' );
            ?>
            
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row" style="font-weight: 600; width: 200px;">Trạng thái Website</th>
                        <td>
                            <fieldset>
                                <label style="display: block; margin-bottom: 12px; cursor: pointer;">
                                    <input type="radio" name="techblog_site_status_mode" value="normal" <?php checked( $current_mode, 'normal' ); ?> style="margin-top: -3px;" />
                                    <span style="font-weight: 600; color: #1d2327; font-size: 14px; margin-left: 5px;">Hoạt động bình thường</span>
                                    <p class="description" style="margin: 4px 0 0 24px;">Website mở công khai, hiển thị bình thường cho tất cả khách truy cập.</p>
                                </label>
                                
                                <label style="display: block; margin-bottom: 12px; cursor: pointer;">
                                    <input type="radio" name="techblog_site_status_mode" value="maintenance" <?php checked( $current_mode, 'maintenance' ); ?> style="margin-top: -3px;" />
                                    <span style="font-weight: 600; color: #d63638; font-size: 14px; margin-left: 5px;">Bảo trì hệ thống (Maintenance Mode)</span>
                                    <p class="description" style="margin: 4px 0 0 24px;">Khách truy cập vãng lai sẽ thấy trang báo trì. Quản trị viên (Admin) đã đăng nhập vẫn xem trang bình thường.</p>
                                </label>
                                
                                <label style="display: block; margin-bottom: 12px; cursor: pointer;">
                                    <input type="radio" name="techblog_site_status_mode" value="suspended" <?php checked( $current_mode, 'suspended' ); ?> style="margin-top: -3px;" />
                                    <span style="font-weight: 600; color: #1d2327; font-size: 14px; margin-left: 5px;">Tạm dừng hoạt động (Đóng trang web)</span>
                                    <p class="description" style="margin: 4px 0 0 24px;">Dừng hoạt động của trang web. Tất cả người dùng vãng lai sẽ nhận được thông báo ngưng hoạt động.</p>
                                </label>
                            </fieldset>
                        </td>
                    </tr>

                    <tr id="row-maintenance-message">
                        <th scope="row"><label for="techblog_maintenance_message" style="font-weight: 600;">Thông điệp Bảo trì</label></th>
                        <td>
                            <textarea id="techblog_maintenance_message" name="techblog_maintenance_message" class="large-text" rows="4"><?php echo esc_textarea( get_option( 'techblog_maintenance_message', 'Hệ thống đang được nâng cấp để mang lại trải nghiệm tốt nhất cho bạn. Chúng tôi sẽ quay trở lại sớm!' ) ); ?></textarea>
                            <p class="description">Nội dung giải thích lý do bảo trì hệ thống.</p>
                        </td>
                    </tr>

                    <tr id="row-suspended-message">
                        <th scope="row"><label for="techblog_suspended_message" style="font-weight: 600;">Thông điệp Đóng trang web</label></th>
                        <td>
                            <textarea id="techblog_suspended_message" name="techblog_suspended_message" class="large-text" rows="4"><?php echo esc_textarea( get_option( 'techblog_suspended_message', 'Trang web này hiện đã tạm thời dừng hoạt động theo yêu cầu của ban quản trị.' ) ); ?></textarea>
                            <p class="description">Nội dung giải thích lý do đóng hoặc ngưng cung cấp dịch vụ.</p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php submit_button( 'Lưu thay đổi', 'primary', 'submit', true ); ?>
        </form>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            function toggleStatusMessageRows() {
                var selectedVal = $('input[name="techblog_site_status_mode"]:checked').val();
                if (selectedVal === 'normal') {
                    $('#row-maintenance-message').hide();
                    $('#row-suspended-message').hide();
                } else if (selectedVal === 'maintenance') {
                    $('#row-maintenance-message').show();
                    $('#row-suspended-message').hide();
                } else if (selectedVal === 'suspended') {
                    $('#row-maintenance-message').hide();
                    $('#row-suspended-message').show();
                }
            }
            toggleStatusMessageRows();
            $('input[name="techblog_site_status_mode"]').change(toggleStatusMessageRows);
        });
    </script>
    <?php
}

// 4. Intercept visitor requests and redirect based on site status
function techblog_maintenance_suspended_check() {
    // Check if in admin dashboard, running WP-CLI, cron, or doing AJAX
    if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
        return;
    }

    // Exclude global login screen
    global $pagenow;
    if ( 'wp-login.php' === $pagenow ) {
        return;
    }

    // Exclude administrators (users with manage_options capability)
    if ( current_user_can( 'manage_options' ) ) {
        return;
    }

    $mode = get_option( 'techblog_site_status_mode', 'normal' );

    if ( 'maintenance' === $mode ) {
        // Send HTTP 503 Service Unavailable (best practice for SEO / search bots)
        header( 'HTTP/1.1 503 Service Unavailable', true, 503 );
        header( 'Retry-After: 3600' ); // Retry in 1 hour
        
        $template_path = get_template_directory() . '/templates/maintenance.php';
        if ( file_exists( $template_path ) ) {
            include $template_path;
            exit;
        }
    } elseif ( 'suspended' === $mode ) {
        // Send HTTP 503 Service Unavailable or 403 Forbidden
        header( 'HTTP/1.1 503 Service Unavailable', true, 503 );
        
        $template_path = get_template_directory() . '/templates/suspended.php';
        if ( file_exists( $template_path ) ) {
            include $template_path;
            exit;
        }
    }
}
add_action( 'template_redirect', 'techblog_maintenance_suspended_check', 1 );
