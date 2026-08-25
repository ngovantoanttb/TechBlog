<?php
/**
 * TechJournal Welcome Greeting / Splash Screen System
 * Supports multiple custom event campaigns and custom HTML/CSS/JS.
 *
 * @package TechJournal
 * @since 1.0.0
 */

// 1. Register Options Page under Settings menu
function techblog_register_splash_settings() {
    add_options_page(
        'Màn hình chào',                // Page Title
        'Màn hình chào (Splash)',       // Menu Title
        'manage_options',               // Capability
        'techblog-splash',              // Menu Slug
        'techblog_render_splash_page'   // Callback
    );
}
add_action( 'admin_menu', 'techblog_register_splash_settings' );

// Helper to initialize and retrieve campaigns array
function techblog_splash_get_campaigns() {
    $campaigns = get_option( 'techblog_splash_campaigns', array() );
    
    // Initialize default campaign if empty or missing
    if ( empty( $campaigns ) || ! isset( $campaigns['default'] ) ) {
        $logo         = get_option( 'techblog_splash_logo', '' );
        $logo_width   = get_option( 'techblog_splash_logo_width', 180 );
        $title        = get_option( 'techblog_splash_title', 'Chào mừng bạn đến với TechBlog' );
        $subtitle     = get_option( 'techblog_splash_subtitle', 'Cập nhật tin tức công nghệ mới nhất mỗi ngày' );
        $btn_text     = get_option( 'techblog_splash_btn_text', 'Khám phá ngay' );
        $duration     = get_option( 'techblog_splash_duration', 5 );
        $bg_color     = get_option( 'techblog_splash_bg_color', '#0f172a' );
        $text_color   = get_option( 'techblog_splash_text_color', '#ffffff' );
        $accent_color = get_option( 'techblog_splash_accent_color', '#2563eb' );
        $animation    = get_option( 'techblog_splash_animation', 'fade' );
        
        $campaigns['default'] = array(
            'id'           => 'default',
            'name'         => 'Mặc định',
            'custom_html'  => '',
            'duration'     => $duration,
            'bg_color'     => $bg_color,
            'animation'    => $animation,
            'logo'         => $logo,
            'logo_width'   => $logo_width,
            'title'        => $title,
            'subtitle'     => $subtitle,
            'btn_text'     => $btn_text,
            'text_color'   => $text_color,
            'accent_color' => $accent_color,
        );
        update_option( 'techblog_splash_campaigns', $campaigns );
    }
    
    return $campaigns;
}

// 2. Load WordPress Media Library scripts on our Settings Page only
function techblog_splash_enqueue_admin_assets( $hook ) {
    if ( 'settings_page_techblog-splash' !== $hook ) {
        return;
    }
    wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'techblog_splash_enqueue_admin_assets' );

// 3. Render and Handle Options Page settings saving
function techblog_render_splash_page() {
    // Process actions first
    $campaigns = techblog_splash_get_campaigns();
    $active_campaign = get_option( 'techblog_splash_active_campaign', 'default' );
    $frequency = get_option( 'techblog_splash_frequency', 'session' );
    $global_enabled = get_option( 'techblog_splash_enabled', 0 );
    
    // A. Handle Delete Campaign Action
    if ( isset( $_GET['delete_campaign'] ) ) {
        check_admin_referer( 'techblog_splash_delete_nonce' );
        $delete_id = sanitize_text_field( $_GET['delete_campaign'] );
        if ( 'default' !== $delete_id && isset( $campaigns[ $delete_id ] ) ) {
            unset( $campaigns[ $delete_id ] );
            update_option( 'techblog_splash_campaigns', $campaigns );
            
            // Fallback active campaign if deleted
            if ( $active_campaign === $delete_id ) {
                update_option( 'techblog_splash_active_campaign', 'default' );
            }
            
            $redirect_url = admin_url( 'options-general.php?page=techblog-splash&deleted=1' );
            echo '<script type="text/javascript">window.location.href = "' . esc_url( $redirect_url ) . '";</script>';
            exit;
        }
    }
    
    // B. Handle Create Campaign Action
    if ( isset( $_POST['techblog_splash_create_campaign'] ) ) {
        check_admin_referer( 'techblog_splash_settings_nonce', 'techblog_splash_nonce' );
        $new_name = sanitize_text_field( wp_unslash( $_POST['new_campaign_name'] ) );
        if ( ! empty( $new_name ) ) {
            $new_id = 'campaign_' . time();
            $campaigns[ $new_id ] = array(
                'id'           => $new_id,
                'name'         => $new_name,
                'custom_html'  => '',
                'duration'     => 5,
                'bg_color'     => '#0f172a',
                'animation'    => 'fade',
                'logo'         => '',
                'logo_width'   => 180,
                'title'        => 'Chào mừng bạn đến với TechBlog',
                'subtitle'     => 'Cập nhật tin tức công nghệ mới nhất mỗi ngày',
                'btn_text'     => 'Khám phá ngay',
                'text_color'   => '#ffffff',
                'accent_color' => '#2563eb',
            );
            update_option( 'techblog_splash_campaigns', $campaigns );
            $redirect_url = admin_url( 'options-general.php?page=techblog-splash&edit_campaign=' . $new_id . '&created=1' );
            echo '<script type="text/javascript">window.location.href = "' . esc_url( $redirect_url ) . '";</script>';
            exit;
        }
    }
    
    // C. Handle Save Settings Action
    if ( isset( $_POST['techblog_splash_save_settings'] ) ) {
        check_admin_referer( 'techblog_splash_settings_nonce', 'techblog_splash_nonce' );
        
        // Save global options
        $global_enabled = isset( $_POST['global_enabled'] ) ? absint( $_POST['global_enabled'] ) : 0;
        $active_campaign = sanitize_text_field( $_POST['active_campaign'] );
        $frequency = sanitize_text_field( $_POST['frequency'] );
        
        update_option( 'techblog_splash_enabled', $global_enabled );
        update_option( 'techblog_splash_active_campaign', $active_campaign );
        update_option( 'techblog_splash_frequency', $frequency );
        
        // Save current edited campaign options
        $edit_id = sanitize_text_field( $_POST['edit_campaign_id'] );
        if ( isset( $campaigns[ $edit_id ] ) ) {
            $campaigns[ $edit_id ]['name']         = sanitize_text_field( wp_unslash( $_POST['name'] ) );
            // Allow unfiltered HTML/CSS/JS for administrators
            $campaigns[ $edit_id ]['custom_html']  = isset( $_POST['custom_html'] ) ? wp_unslash( $_POST['custom_html'] ) : '';
            $campaigns[ $edit_id ]['duration']     = absint( $_POST['duration'] );
            $campaigns[ $edit_id ]['bg_color']     = sanitize_hex_color( $_POST['bg_color'] );
            $campaigns[ $edit_id ]['animation']    = sanitize_text_field( $_POST['animation'] );
            
            // Fallback fields
            $campaigns[ $edit_id ]['logo']         = esc_url_raw( $_POST['logo'] );
            $campaigns[ $edit_id ]['logo_width']   = absint( $_POST['logo_width'] );
            $campaigns[ $edit_id ]['title']        = sanitize_text_field( $_POST['title'] );
            $campaigns[ $edit_id ]['subtitle']     = sanitize_text_field( $_POST['subtitle'] );
            $campaigns[ $edit_id ]['btn_text']     = sanitize_text_field( $_POST['btn_text'] );
            $campaigns[ $edit_id ]['text_color']   = sanitize_hex_color( $_POST['text_color'] );
            $campaigns[ $edit_id ]['accent_color'] = sanitize_hex_color( $_POST['accent_color'] );
            
            update_option( 'techblog_splash_campaigns', $campaigns );
        }
        
        echo '<div class="updated"><p>Đã lưu tất cả thay đổi thành công!</p></div>';
    }
    
    // Set edit target ID
    $edit_id = isset( $_GET['edit_campaign'] ) ? sanitize_text_field( $_GET['edit_campaign'] ) : 'default';
    if ( ! isset( $campaigns[ $edit_id ] ) ) {
        $edit_id = 'default';
    }
    
    // Display status notices
    if ( isset( $_GET['created'] ) ) {
        echo '<div class="updated"><p>Đã tạo sự kiện mới thành công! Vui lòng thiết lập cấu hình phía dưới.</p></div>';
    }
    if ( isset( $_GET['deleted'] ) ) {
        echo '<div class="updated"><p>Đã xóa sự kiện thành công.</p></div>';
    }
    
    $camp = $campaigns[ $edit_id ];
    ?>
    <div class="wrap">
        <h1>Cấu hình Màn hình chào (Welcome Splash Screen)</h1>
        <p class="description">Cho phép quản lý nhiều chiến dịch sự kiện và chèn giao diện tùy biến (custom HTML) cho màn hình chào.</p>
        <hr style="margin: 15px 0;" />

        <div style="background: #fff; border: 1px solid #ccd0d4; padding: 20px; margin-bottom: 20px; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
            <h2 style="margin-top:0;">1. Trạng thái hoạt động toàn cục</h2>
            
            <form method="post" action="">
                <?php wp_nonce_field( 'techblog_splash_settings_nonce', 'techblog_splash_nonce' ); ?>
                <input type="hidden" name="edit_campaign_id" value="<?php echo esc_attr( $edit_id ); ?>" />
                
                <table class="form-table" style="margin-bottom:0;">
                    <tbody>
                        <tr>
                            <th scope="row" style="width: 250px;"><label for="global_enabled">Kích hoạt màn hình chào</label></th>
                            <td>
                                <select id="global_enabled" name="global_enabled">
                                    <option value="0" <?php selected( $global_enabled, 0 ); ?>>Tắt hoàn toàn</option>
                                    <option value="1" <?php selected( $global_enabled, 1 ); ?>>Bật hoạt động</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="active_campaign">Sự kiện hoạt động</label></th>
                            <td>
                                <select id="active_campaign" name="active_campaign" style="min-width: 200px;">
                                    <?php foreach ( $campaigns as $id => $c ) : ?>
                                        <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $active_campaign, $id ); ?>>
                                            <?php echo esc_html( $c['name'] ); ?><?php echo $id === 'default' ? ' (Mặc định)' : ''; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="description" style="margin-left: 10px;">Lựa chọn sự kiện sẽ hiển thị với người dùng truy cập.</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="frequency">Tần suất hiển thị</label></th>
                            <td>
                                <select id="frequency" name="frequency">
                                    <option value="always" <?php selected( $frequency, 'always' ); ?>>Luôn hiển thị (Mỗi lần tải lại trang / F5)</option>
                                    <option value="session" <?php selected( $frequency, 'session' ); ?>>Một lần mỗi phiên (Session Storage & Cookie - Khuyên dùng)</option>
                                    <option value="day" <?php selected( $frequency, 'day' ); ?>>Một lần mỗi 24 giờ (Local Storage & Cookie 24h)</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
        </div>

        <div style="background: #fff; border: 1px solid #ccd0d4; padding: 20px; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                <h2 style="margin:0;">2. Thiết kế sự kiện: <span style="color:#2563eb;"><?php echo esc_html( $camp['name'] ); ?></span></h2>
                
                <div>
                    <label for="techblog_splash_edit_selector" style="font-weight:bold; margin-right: 5px;">Chọn sự kiện để thiết kế:</label>
                    <select id="techblog_splash_edit_selector" style="min-width: 200px; vertical-align:middle;">
                        <?php foreach ( $campaigns as $id => $c ) : ?>
                            <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $edit_id, $id ); ?>><?php echo esc_html( $c['name'] ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <?php 
                    if ( 'default' !== $edit_id ) {
                        $delete_url = wp_nonce_url( admin_url( 'options-general.php?page=techblog-splash&delete_campaign=' . $edit_id ), 'techblog_splash_delete_nonce' );
                        echo '<a href="' . esc_url( $delete_url ) . '" class="button button-link-destructive" onclick="return confirm(\'Bạn có chắc chắn muốn xóa sự kiện này?\');" style="margin-left: 10px;">Xóa sự kiện</a>';
                    }
                    ?>
                </div>
            </div>

            <table class="form-table">
                <tbody>
                    <!-- Campaign Admin Name -->
                    <tr>
                        <th scope="row"><label for="name">Tên sự kiện quản trị</label></th>
                        <td>
                            <input type="text" id="name" name="name" value="<?php echo esc_attr( $camp['name'] ); ?>" class="regular-text" required />
                            <p class="description">Đặt tên gợi nhớ để phân biệt các sự kiện (Ví dụ: Sự kiện Tết 2027).</p>
                        </td>
                    </tr>

                    <!-- Custom HTML/CSS/JS (CORE FEATURE REQUEST) -->
                    <tr>
                        <th scope="row"><label for="custom_html">Mã giao diện tùy biến (Custom HTML)</label></th>
                        <td>
                            <textarea id="custom_html" name="custom_html" rows="12" class="large-text" style="font-family: Consolas, Monaco, monospace; font-size: 13px; background:#f9f9f9;"><?php echo esc_textarea( isset( $camp['custom_html'] ) ? wp_unslash( $camp['custom_html'] ) : '' ); ?></textarea>
                            <p class="description">
                                <b>Mã HTML/CSS/JS tùy biến của riêng bạn</b>. Cho phép sử dụng thẻ <code>&lt;style&gt;</code> và <code>&lt;script&gt;</code> để tự thiết kế giao diện theo ý muốn.<br/>
                                <span style="color:#d32f2f;">Lưu ý:</span> Để trống ô này nếu muốn sử dụng thiết kế mặc định (Logo + Tiêu đề + Nút) ở phía dưới.
                            </p>
                        </td>
                    </tr>

                    <!-- Auto-close Duration -->
                    <tr>
                        <th scope="row"><label for="duration">Thời gian tự động đóng (giây)</label></th>
                        <td>
                            <input type="number" id="duration" name="duration" value="<?php echo intval( $camp['duration'] ); ?>" class="small-text" min="0" max="60" />
                            <p class="description">Số giây tự động tắt màn hình chào (Áp dụng cho cả Giao diện mặc định lẫn Giao diện tùy biến ở trên). Nhập <b>0</b> nếu muốn tắt tự động đóng.</p>
                        </td>
                    </tr>

                    <!-- Background Color -->
                    <tr>
                        <th scope="row"><label for="bg_color">Màu nền màn hình</label></th>
                        <td>
                            <input type="color" id="bg_color" name="bg_color" value="<?php echo esc_attr( $camp['bg_color'] ); ?>" />
                            <p class="description">Màu nền bao phủ toàn màn hình chào.</p>
                        </td>
                    </tr>

                    <!-- Exit Animation -->
                    <tr>
                        <th scope="row"><label for="animation">Hiệu ứng đóng</label></th>
                        <td>
                            <select id="animation" name="animation">
                                <option value="fade" <?php selected( $camp['animation'], 'fade' ); ?>>Mờ dần (Fade Out)</option>
                                <option value="slide-up" <?php selected( $camp['animation'], 'slide-up' ); ?>>Trượt lên phía trên (Slide Up)</option>
                                <option value="zoom-out" <?php selected( $camp['animation'], 'zoom-out' ); ?>>Thu nhỏ & Mờ dần (Zoom Out)</option>
                            </select>
                            <p class="description">Hiệu ứng chuyển cảnh khi hết thời gian tự động đóng hoặc bấm nút.</p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Divider showing default fields which are fallback -->
            <div style="margin: 30px 0 10px 0; border-top: 1px dashed #ccc; padding-top: 20px;" id="default-fields-section">
                <h3 style="margin-top:0; color:#666;">Cấu hình giao diện mặc định (Chỉ áp dụng nếu để trống Mã tùy biến phía trên)</h3>
            </div>

            <table class="form-table">
                <tbody>
                    <!-- Logo Upload -->
                    <tr>
                        <th scope="row"><label for="logo">Tải lên Logo</label></th>
                        <td>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <input type="text" id="logo" name="logo" value="<?php echo esc_url( $camp['logo'] ); ?>" class="regular-text" placeholder="https://..." />
                                <button type="button" id="techblog_splash_logo_btn" class="button">Chọn ảnh</button>
                            </div>
                            <div style="margin-top: 10px;">
                                <img id="techblog_splash_logo_preview" src="<?php echo esc_url( $camp['logo'] ); ?>" style="max-height: 100px; display: <?php echo $camp['logo'] ? 'block' : 'none'; ?>; border: 1px solid #ccc; padding: 5px; background: #f0f0f0;" />
                            </div>
                        </td>
                    </tr>

                    <!-- Logo Width -->
                    <tr>
                        <th scope="row"><label for="logo_width">Độ rộng của Logo (px)</label></th>
                        <td>
                            <input type="number" id="logo_width" name="logo_width" value="<?php echo intval( $camp['logo_width'] ); ?>" class="small-text" min="50" max="600" />
                        </td>
                    </tr>

                    <!-- Title Text -->
                    <tr>
                        <th scope="row"><label for="title">Tiêu đề chính</label></th>
                        <td>
                            <input type="text" id="title" name="title" value="<?php echo esc_attr( $camp['title'] ); ?>" class="large-text" />
                        </td>
                    </tr>

                    <!-- Subtitle Text -->
                    <tr>
                        <th scope="row"><label for="subtitle">Phụ đề / Mô tả ngắn</label></th>
                        <td>
                            <input type="text" id="subtitle" name="subtitle" value="<?php echo esc_attr( $camp['subtitle'] ); ?>" class="large-text" />
                        </td>
                    </tr>

                    <!-- Button Text -->
                    <tr>
                        <th scope="row"><label for="btn_text">Chữ trên nút bấm</label></th>
                        <td>
                            <input type="text" id="btn_text" name="btn_text" value="<?php echo esc_attr( $camp['btn_text'] ); ?>" class="regular-text" />
                        </td>
                    </tr>

                    <!-- Text Color -->
                    <tr>
                        <th scope="row"><label for="text_color">Màu chữ chính</label></th>
                        <td>
                            <input type="color" id="text_color" name="text_color" value="<?php echo esc_attr( $camp['text_color'] ); ?>" />
                        </td>
                    </tr>

                    <!-- Accent Color -->
                    <tr>
                        <th scope="row"><label for="accent_color">Màu nhấn của nút bấm</label></th>
                        <td>
                            <input type="color" id="accent_color" name="accent_color" value="<?php echo esc_attr( $camp['accent_color'] ); ?>" />
                        </td>
                    </tr>
                </tbody>
            </table>

            <div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 15px;">
                <input type="submit" name="techblog_splash_save_settings" class="button button-primary button-large" value="Lưu tất cả cấu hình" />
            </div>
            </form>
        </div>

        <!-- Section 3: Add new campaign -->
        <div style="background: #fff; border: 1px solid #ccd0d4; padding: 20px; margin-top: 20px; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
            <h2 style="margin-top:0;">3. Tạo sự kiện chào mừng mới</h2>
            <form method="post" action="">
                <?php wp_nonce_field( 'techblog_splash_settings_nonce', 'techblog_splash_nonce' ); ?>
                <p>Nhập tên sự kiện mới (ví dụ: Sự kiện Tết 2027, Noel, Sinh nhật Web...) để tạo cấu hình giao diện riêng:</p>
                <input type="text" name="new_campaign_name" placeholder="Tên sự kiện mới..." required class="regular-text" style="vertical-align:middle;" />
                <input type="submit" name="techblog_splash_create_campaign" class="button button-secondary" value="Tạo mới" style="vertical-align:middle;" />
            </form>
        </div>
    </div>

    <!-- Media Library integration and UI events script -->
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Edit selector redirect
            $('#techblog_splash_edit_selector').change(function() {
                window.location.href = 'options-general.php?page=techblog-splash&edit_campaign=' + $(this).val();
            });

            // Media library upload
            var mediaUploader;
            $('#techblog_splash_logo_btn').click(function(e) {
                e.preventDefault();
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                mediaUploader = wp.media({
                    title: 'Chọn logo màn hình chào',
                    button: { text: 'Sử dụng hình ảnh này' },
                    multiple: false
                });
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#logo').val(attachment.url);
                    $('#techblog_splash_logo_preview').attr('src', attachment.url).show();
                });
                mediaUploader.open();
            });

            // Dynamic toggle for default fields based on custom HTML content
            function toggleDefaultFieldsNotice() {
                var htmlContent = $('#custom_html').val().trim();
                if (htmlContent.length > 0) {
                    $('#default-fields-section h3').html('Cấu hình mặc định (ĐANG BỊ BỎ QUA - do bạn đang sử dụng Mã tùy biến ở trên)');
                    $('#default-fields-section h3').css('color', '#d63638');
                } else {
                    $('#default-fields-section h3').html('Cấu hình giao diện mặc định (Chỉ áp dụng nếu để trống Mã tùy biến phía trên)');
                    $('#default-fields-section h3').css('color', '#666');
                }
            }
            toggleDefaultFieldsNotice();
            $('#custom_html').on('input', toggleDefaultFieldsNotice);
        });
    </script>
    <?php
}

// 4. Inject Splash Screen into the Frontend on wp_body_open
function techblog_render_welcome_splash() {
    if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
        return;
    }

    // Bypass splash screen on link redirect gateway page (/chuyen-huong/)
    if ( get_query_var( 'techblog_redirect_page' ) || isset( $_GET['techblog_redirect_page'] ) || ( isset( $_SERVER['REQUEST_URI'] ) && strpos( $_SERVER['REQUEST_URI'], 'chuyen-huong' ) !== false ) ) {
        return;
    }

    $enabled = get_option( 'techblog_splash_enabled', 0 );
    if ( ! $enabled ) {
        return;
    }

    $campaigns = techblog_splash_get_campaigns();
    $active_id = get_option( 'techblog_splash_active_campaign', 'default' );
    
    if ( ! isset( $campaigns[ $active_id ] ) ) {
        return;
    }

    $frequency = get_option( 'techblog_splash_frequency', 'session' );

    // 1st Line of Defense: Server-Side Cookie Check on direct requests
    if ( 'session' === $frequency ) {
        if ( isset( $_COOKIE['techblog_splash_session_' . $active_id] ) || isset( $_COOKIE['techblog_splash_session'] ) ) {
            return;
        }
    } elseif ( 'day' === $frequency ) {
        if ( isset( $_COOKIE['techblog_splash_24h_' . $active_id] ) || isset( $_COOKIE['techblog_splash_24h'] ) ) {
            return;
        }
    }
    
    $camp         = $campaigns[ $active_id ];
    $custom_html  = isset( $camp['custom_html'] ) ? trim( stripslashes( wp_unslash( $camp['custom_html'] ) ) ) : '';
    $duration     = isset( $camp['duration'] ) ? intval( $camp['duration'] ) : 5;
    $bg_color     = isset( $camp['bg_color'] ) ? $camp['bg_color'] : '#0f172a';
    $animation    = isset( $camp['animation'] ) ? $camp['animation'] : 'fade';
    
    // Default layout fallbacks
    $logo         = isset( $camp['logo'] ) ? $camp['logo'] : '';
    $logo_width   = isset( $camp['logo_width'] ) ? intval( $camp['logo_width'] ) : 180;
    $title        = isset( $camp['title'] ) ? $camp['title'] : 'Chào mừng bạn đến với TechBlog';
    $subtitle     = isset( $camp['subtitle'] ) ? $camp['subtitle'] : '';
    $btn_text     = isset( $camp['btn_text'] ) ? $camp['btn_text'] : 'Khám phá ngay';
    $text_color   = isset( $camp['text_color'] ) ? $camp['text_color'] : '#ffffff';
    $accent_color = isset( $camp['accent_color'] ) ? $camp['accent_color'] : '#2563eb';

    // Inline critical style block
    ?>
    <style type="text/css">
        #techblog-splash-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background-color: <?php echo esc_attr( $bg_color ); ?>;
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 1;
            visibility: visible;
            transition: all 0.6s cubic-bezier(0.25, 1, 0.5, 1);
            box-sizing: border-box;
            font-family: "Be Vietnam Pro", system-ui, sans-serif;
        }

        #techblog-splash-content {
            max-width: 580px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
            padding: 24px;
            text-align: center;
            box-sizing: border-box;
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.8s cubic-bezier(0.25, 1, 0.5, 1);
        }

        #techblog-splash-content.has-custom-html {
            max-width: 100% !important;
            width: 100% !important;
            height: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            gap: 0 !important;
            transform: none !important;
        }

        #techblog-splash-content.active {
            transform: scale(1);
            opacity: 1;
        }

        #techblog-splash-content.has-custom-html.active {
            transform: none !important;
            opacity: 1;
        }

        /* Default layout specific styles */
        .techblog-splash-default-wrap {
            color: <?php echo esc_attr( $text_color ); ?>;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
            width: 100%;
        }

        .techblog-splash-title {
            font-size: 32px;
            line-height: 1.25;
            font-weight: 800;
            margin: 0 auto;
            letter-spacing: -0.5px;
            text-align: center;
            width: 100%;
        }

        @media (max-width: 640px) {
            .techblog-splash-title {
                font-size: 26px;
            }
        }

        .techblog-splash-subtitle {
            font-size: 15px;
            line-height: 1.6;
            font-weight: 500;
            opacity: 0.85;
            margin: 0 auto;
            max-width: 380px;
            text-align: center;
            width: 100%;
        }

        .techblog-splash-btn {
            background-color: <?php echo esc_attr( $accent_color ); ?>;
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 14px 36px;
            border: none;
            border-radius: 9999px;
            cursor: pointer;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -4px rgba(0, 0, 0, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            outline: none;
        }

        .techblog-splash-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.4);
            filter: brightness(1.1);
        }

        .techblog-splash-btn:active {
            transform: translateY(0);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }

        .techblog-splash-progress-container {
            width: 120px;
            height: 3px;
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 9999px;
            overflow: hidden;
            margin: 8px auto 0 auto;
        }

        .techblog-splash-progress-bar {
            height: 100%;
            background-color: #ffffff;
            width: 100%;
            transform-origin: left;
            transition: transform <?php echo intval( $duration ); ?>s linear;
        }

        .techblog-splash-logo-img {
            max-height: 90px;
            object-fit: contain;
            margin: 0 auto 8px auto;
            display: block;
            animation: techblog-bounce 3s infinite ease-in-out;
        }
        
        @keyframes techblog-bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        /* Exit Transitions styles */
        #techblog-splash-overlay.exit-fade {
            opacity: 0;
            visibility: hidden;
        }

        #techblog-splash-overlay.exit-slide-up {
            transform: translateY(-100%);
            opacity: 0;
            visibility: hidden;
        }

        #techblog-splash-overlay.exit-zoom-out {
            transform: scale(1.08);
            opacity: 0;
            visibility: hidden;
        }
    </style>

    <!-- 2nd Line of Defense: Early JS check before overlay renders to prevent FOUC / flicker (especially on cached pages) -->
    <script type="text/javascript">
        (function() {
            var frequency = <?php echo json_encode( $frequency ); ?>;
            var campaignId = <?php echo json_encode( $active_id ); ?>;
            var shouldShow = true;
            
            try {
                if (frequency === 'session') {
                    var sess1 = sessionStorage.getItem('techblog_splash_shown_' + campaignId);
                    var sess2 = sessionStorage.getItem('techblog_splash_shown');
                    var ck1   = document.cookie.indexOf('techblog_splash_session_' + encodeURIComponent(campaignId) + '=1') !== -1;
                    var ck2   = document.cookie.indexOf('techblog_splash_session=1') !== -1;

                    if (sess1 || sess2 || ck1 || ck2) {
                        shouldShow = false;
                    }
                } else if (frequency === 'day') {
                    var lastTime = localStorage.getItem('techblog_splash_time_' + campaignId) || localStorage.getItem('techblog_splash_shown_time');
                    if (lastTime) {
                        var elapsed = Date.now() - parseInt(lastTime, 10);
                        if (!isNaN(elapsed) && elapsed < 24 * 60 * 60 * 1000) {
                            shouldShow = false;
                        }
                    }
                    var ckDay1 = document.cookie.indexOf('techblog_splash_24h_' + encodeURIComponent(campaignId) + '=1') !== -1;
                    var ckDay2 = document.cookie.indexOf('techblog_splash_24h=1') !== -1;
                    if (ckDay1 || ckDay2) {
                        shouldShow = false;
                    }
                }
            } catch (e) {
                // Ignore storage errors in restricted contexts
            }

            window.__techblogSplashShouldShow = shouldShow;

            if (!shouldShow) {
                var hideStyle = document.createElement('style');
                hideStyle.id = 'techblog-splash-hide-style';
                hideStyle.textContent = '#techblog-splash-overlay { display: none !important; visibility: hidden !important; opacity: 0 !important; pointer-events: none !important; }';
                (document.head || document.documentElement).appendChild(hideStyle);
            }
        })();
    </script>

    <!-- Welcome Splash Screen Overlay markup -->
    <div id="techblog-splash-overlay" role="dialog" aria-modal="true">
        <div id="techblog-splash-content" class="<?php echo ! empty( $custom_html ) ? 'has-custom-html' : ''; ?>" style="<?php echo ! empty( $custom_html ) ? 'max-width:100%; width:100%; height:100%; padding:0; gap:0; transform:none;' : ''; ?>">
            
            <?php if ( ! empty( $custom_html ) ) : ?>
                <!-- Render custom pasted HTML layout -->
                <?php echo $custom_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php else : ?>
                <!-- Render default premium fallback layout -->
                <div class="techblog-splash-default-wrap">
                    <?php if ( ! empty( $logo ) ) : ?>
                        <img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $title ); ?> Logo" class="techblog-splash-logo-img" style="width: <?php echo intval( $logo_width ); ?>px; display: block; margin: 0 auto 8px auto;" />
                    <?php else : ?>
                        <div style="background-color: <?php echo esc_attr( $accent_color ); ?>; width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 28px; color: #ffffff; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2); margin: 0 auto 8px auto;">
                            TB
                        </div>
                    <?php endif; ?>

                    <div style="display: flex; flex-direction: column; align-items: center; gap: 10px; width: 100%;">
                        <h1 id="splash-title" class="techblog-splash-title" style="text-align: center; width: 100%;"><?php echo esc_html( $title ); ?></h1>
                        <?php if ( ! empty( $subtitle ) ) : ?>
                            <p class="techblog-splash-subtitle" style="text-align: center; width: 100%; margin: 0 auto;"><?php echo esc_html( $subtitle ); ?></p>
                        <?php endif; ?>
                    </div>

                    <button id="techblog-splash-btn" class="techblog-splash-btn" autofocus>
                        <span><?php echo esc_html( $btn_text ); ?></span>
                    </button>

                    <?php if ( intval( $duration ) > 0 ) : ?>
                        <div class="techblog-splash-progress-container">
                            <div id="techblog-splash-progress-bar" class="techblog-splash-progress-bar"></div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Script to manage showing, counting down, exit animations and persistent storage -->
    <script type="text/javascript">
        (function() {
            var frequency = <?php echo json_encode( $frequency ); ?>;
            var campaignId = <?php echo json_encode( $active_id ); ?>;
            var duration = <?php echo intval( $duration ); ?>;
            var animationType = <?php echo json_encode( $animation ); ?>;

            // Helper to record that splash was shown
            function recordSplashShown() {
                try {
                    if (frequency === 'session') {
                        sessionStorage.setItem('techblog_splash_shown_' + campaignId, '1');
                        sessionStorage.setItem('techblog_splash_shown', '1');
                        document.cookie = 'techblog_splash_session_' + encodeURIComponent(campaignId) + '=1; path=/; SameSite=Lax';
                        document.cookie = 'techblog_splash_session=1; path=/; SameSite=Lax';
                    } else if (frequency === 'day') {
                        var nowStr = Date.now().toString();
                        localStorage.setItem('techblog_splash_time_' + campaignId, nowStr);
                        localStorage.setItem('techblog_splash_shown_time', nowStr);
                        localStorage.setItem('techblog_splash_shown_date', new Date().toDateString());
                        var maxAge = 24 * 60 * 60; // 24 hours in seconds
                        document.cookie = 'techblog_splash_24h_' + encodeURIComponent(campaignId) + '=1; max-age=' + maxAge + '; path=/; SameSite=Lax';
                        document.cookie = 'techblog_splash_24h=1; max-age=' + maxAge + '; path=/; SameSite=Lax';
                    }
                } catch (e) {}
            }

            function initSplash() {
                var overlay = document.getElementById('techblog-splash-overlay');
                if (!overlay) return;

                // If early check decided not to show, remove immediately from DOM
                if (window.__techblogSplashShouldShow === false) {
                    overlay.style.display = 'none';
                    if (overlay.parentNode) overlay.parentNode.removeChild(overlay);
                    return;
                }

                // Record immediately on display so any rapid page refresh or navigation respects frequency
                recordSplashShown();

                var content = document.getElementById('techblog-splash-content');
                var closeBtn = document.getElementById('techblog-splash-btn');
                var progressBar = document.getElementById('techblog-splash-progress-bar');
                var autoCloseTimer = null;

                // Lock scroll while splash is actively displayed
                document.documentElement.style.overflow = 'hidden';
                document.body.style.overflow = 'hidden';

                // Phase 1: pop-in content smoothly
                setTimeout(function() {
                    if (content) content.classList.add('active');
                    if (progressBar) {
                        progressBar.style.transform = 'scaleX(0)';
                    }
                }, 50);

                // Close function
                function closeSplash() {
                    recordSplashShown();
                    if (autoCloseTimer) {
                        clearTimeout(autoCloseTimer);
                        autoCloseTimer = null;
                    }

                    var exitClass = 'exit-fade';
                    if (animationType === 'slide-up') {
                        exitClass = 'exit-slide-up';
                    } else if (animationType === 'zoom-out') {
                        exitClass = 'exit-zoom-out';
                    }

                    overlay.classList.add(exitClass);

                    // Unlock scrolling locks
                    document.documentElement.style.overflow = '';
                    document.body.style.overflow = '';

                    // Clean up DOM after transition finishes
                    setTimeout(function() {
                        if (overlay && overlay.parentNode) {
                            overlay.style.display = 'none';
                            overlay.parentNode.removeChild(overlay);
                        }
                    }, 600);
                }

                // Bind click to manually-configured close elements inside custom HTML or default button
                var closeElements = overlay.querySelectorAll('.techblog-splash-close');
                closeElements.forEach(function(el) {
                    el.addEventListener('click', function(e) {
                        e.preventDefault();
                        closeSplash();
                    });
                });

                if (closeBtn) {
                    closeBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        closeSplash();
                    });
                }

                // Support global JS action for custom HTML script controls
                window.techblogCloseSplash = function() {
                    closeSplash();
                };

                // Auto close after duration
                if (duration > 0) {
                    autoCloseTimer = setTimeout(function() {
                        closeSplash();
                    }, duration * 1000);
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initSplash);
            } else {
                initSplash();
            }
        })();
    </script>
    <?php
}
add_action( 'wp_body_open', 'techblog_render_welcome_splash', 5 );
