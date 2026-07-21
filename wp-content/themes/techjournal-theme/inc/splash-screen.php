<?php
/**
 * TechJournal Welcome Greeting / Splash Screen System
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

// 2. Register Settings Fields
function techblog_register_splash_fields() {
    register_setting( 'techblog-splash-group', 'techblog_splash_enabled', array(
        'type'              => 'boolean',
        'sanitize_callback' => 'absint',
        'default'           => 0
    ));

    register_setting( 'techblog-splash-group', 'techblog_splash_logo', array(
        'type'              => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'default'           => ''
    ));

    register_setting( 'techblog-splash-group', 'techblog_splash_logo_width', array(
        'type'              => 'integer',
        'sanitize_callback' => 'absint',
        'default'           => 180
    ));

    register_setting( 'techblog-splash-group', 'techblog_splash_title', array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => 'Chào mừng bạn đến với TechBlog'
    ));

    register_setting( 'techblog-splash-group', 'techblog_splash_subtitle', array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => 'Cập nhật tin tức công nghệ mới nhất mỗi ngày'
    ));

    register_setting( 'techblog-splash-group', 'techblog_splash_btn_text', array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => 'Khám phá ngay'
    ));

    register_setting( 'techblog-splash-group', 'techblog_splash_duration', array(
        'type'              => 'integer',
        'sanitize_callback' => 'absint',
        'default'           => 5
    ));

    register_setting( 'techblog-splash-group', 'techblog_splash_frequency', array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => 'session'
    ));

    register_setting( 'techblog-splash-group', 'techblog_splash_bg_color', array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_hex_color',
        'default'           => '#0f172a'
    ));

    register_setting( 'techblog-splash-group', 'techblog_splash_text_color', array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_hex_color',
        'default'           => '#ffffff'
    ));

    register_setting( 'techblog-splash-group', 'techblog_splash_accent_color', array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_hex_color',
        'default'           => '#2563eb'
    ));

    register_setting( 'techblog-splash-group', 'techblog_splash_animation', array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => 'fade'
    ));
}
add_action( 'admin_init', 'techblog_register_splash_fields' );

// 3. Load WordPress Media Library scripts on our Settings Page only
function techblog_splash_enqueue_admin_assets( $hook ) {
    if ( 'settings_page_techblog-splash' !== $hook ) {
        return;
    }
    wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'techblog_splash_enqueue_admin_assets' );

// 4. Render WordPress Settings Page
function techblog_render_splash_page() {
    ?>
    <div class="wrap">
        <h1>Cấu hình Màn hình chào (Welcome Splash Screen)</h1>
        <p class="description">Hiển thị một màn hình chào đẹp mắt trước khi khách hàng truy cập vào nội dung chính của website.</p>
        <hr style="margin: 15px 0;" />

        <form method="post" action="options.php" style="margin-top: 20px; max-width: 800px;">
            <?php
            settings_fields( 'techblog-splash-group' );
            
            $enabled      = get_option( 'techblog_splash_enabled', 0 );
            $logo         = get_option( 'techblog_splash_logo', '' );
            $logo_width   = get_option( 'techblog_splash_logo_width', 180 );
            $title        = get_option( 'techblog_splash_title', 'Chào mừng bạn đến với TechBlog' );
            $subtitle     = get_option( 'techblog_splash_subtitle', 'Cập nhật tin tức công nghệ mới nhất mỗi ngày' );
            $btn_text     = get_option( 'techblog_splash_btn_text', 'Khám phá ngay' );
            $duration     = get_option( 'techblog_splash_duration', 5 );
            $frequency    = get_option( 'techblog_splash_frequency', 'session' );
            $bg_color     = get_option( 'techblog_splash_bg_color', '#0f172a' );
            $text_color   = get_option( 'techblog_splash_text_color', '#ffffff' );
            $accent_color = get_option( 'techblog_splash_accent_color', '#2563eb' );
            $animation    = get_option( 'techblog_splash_animation', 'fade' );
            ?>

            <table class="form-table" role="presentation">
                <tbody>
                    <!-- Enable/Disable -->
                    <tr>
                        <th scope="row"><label for="techblog_splash_enabled">Kích hoạt màn hình chào</label></th>
                        <td>
                            <select id="techblog_splash_enabled" name="techblog_splash_enabled">
                                <option value="0" <?php selected( $enabled, 0 ); ?>>Tắt</option>
                                <option value="1" <?php selected( $enabled, 1 ); ?>>Bật</option>
                            </select>
                            <p class="description">Chọn "Bật" để hiển thị màn hình chào ngoài trang chủ.</p>
                        </td>
                    </tr>

                    <!-- Logo Upload -->
                    <tr>
                        <th scope="row"><label for="techblog_splash_logo">Tải lên Logo</label></th>
                        <td>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <input type="text" id="techblog_splash_logo" name="techblog_splash_logo" value="<?php echo esc_url( $logo ); ?>" class="regular-text" placeholder="https://..." />
                                <button type="button" id="techblog_splash_logo_btn" class="button">Chọn ảnh</button>
                            </div>
                            <div style="margin-top: 10px;">
                                <img id="techblog_splash_logo_preview" src="<?php echo esc_url( $logo ); ?>" style="max-height: 100px; display: <?php echo $logo ? 'block' : 'none'; ?>; border: 1px solid #ccc; padding: 5px; background: #f0f0f0;" />
                            </div>
                            <p class="description">Nếu để trống, hệ thống sẽ sử dụng biểu tượng hình tròn hoặc bỏ qua logo.</p>
                        </td>
                    </tr>

                    <!-- Logo Width -->
                    <tr>
                        <th scope="row"><label for="techblog_splash_logo_width">Độ rộng của Logo (px)</label></th>
                        <td>
                            <input type="number" id="techblog_splash_logo_width" name="techblog_splash_logo_width" value="<?php echo intval( $logo_width ); ?>" class="small-text" min="50" max="600" />
                            <p class="description">Độ rộng hiển thị tối đa của logo tính bằng pixel (mặc định: 180).</p>
                        </td>
                    </tr>

                    <!-- Title Text -->
                    <tr>
                        <th scope="row"><label for="techblog_splash_title">Tiêu đề chính</label></th>
                        <td>
                            <input type="text" id="techblog_splash_title" name="techblog_splash_title" value="<?php echo esc_attr( $title ); ?>" class="large-text" required />
                            <p class="description">Lời chào chính (Ví dụ: Chào mừng bạn đến với TechBlog).</p>
                        </td>
                    </tr>

                    <!-- Subtitle Text -->
                    <tr>
                        <th scope="row"><label for="techblog_splash_subtitle">Phụ đề / Mô tả ngắn</label></th>
                        <td>
                            <input type="text" id="techblog_splash_subtitle" name="techblog_splash_subtitle" value="<?php echo esc_attr( $subtitle ); ?>" class="large-text" />
                            <p class="description">Mô tả ngắn gọn hoặc câu slogan ngay bên dưới tiêu đề.</p>
                        </td>
                    </tr>

                    <!-- Button Text -->
                    <tr>
                        <th scope="row"><label for="techblog_splash_btn_text">Chữ trên nút bấm</label></th>
                        <td>
                            <input type="text" id="techblog_splash_btn_text" name="techblog_splash_btn_text" value="<?php echo esc_attr( $btn_text ); ?>" class="regular-text" required />
                            <p class="description">Chữ hiển thị trên nút đóng màn hình chào (Mặc định: Khám phá ngay).</p>
                        </td>
                    </tr>

                    <!-- Auto-close Duration -->
                    <tr>
                        <th scope="row"><label for="techblog_splash_duration">Thời gian tự động đóng (giây)</label></th>
                        <td>
                            <input type="number" id="techblog_splash_duration" name="techblog_splash_duration" value="<?php echo intval( $duration ); ?>" class="small-text" min="0" max="60" />
                            <p class="description">Nhập số giây để tự động đóng màn hình chào. Nhập <b>0</b> nếu muốn người dùng bắt buộc phải tự bấm nút để vào trang.</p>
                        </td>
                    </tr>

                    <!-- Display Frequency -->
                    <tr>
                        <th scope="row"><label for="techblog_splash_frequency">Tần suất hiển thị</label></th>
                        <td>
                            <select id="techblog_splash_frequency" name="techblog_splash_frequency">
                                <option value="always" <?php selected( $frequency, 'always' ); ?>>Luôn hiển thị (Mỗi lần tải lại trang)</option>
                                <option value="session" <?php selected( $frequency, 'session' ); ?>>Một lần mỗi phiên (Session Storage - Khuyên dùng)</option>
                                <option value="day" <?php selected( $frequency, 'day' ); ?>>Một lần mỗi ngày (Local Storage - 24 giờ)</option>
                            </select>
                            <p class="description">Tần suất hiển thị lại màn hình chào đối với cùng một trình duyệt của khách truy cập.</p>
                        </td>
                    </tr>

                    <!-- Background Color -->
                    <tr>
                        <th scope="row"><label for="techblog_splash_bg_color">Màu nền màn hình</label></th>
                        <td>
                            <input type="color" id="techblog_splash_bg_color" name="techblog_splash_bg_color" value="<?php echo esc_attr( $bg_color ); ?>" />
                            <p class="description">Chọn màu nền của màn hình chào (Khuyên dùng màu tối để hiển thị sang trọng hơn).</p>
                        </td>
                    </tr>

                    <!-- Text Color -->
                    <tr>
                        <th scope="row"><label for="techblog_splash_text_color">Màu chữ chính</label></th>
                        <td>
                            <input type="color" id="techblog_splash_text_color" name="techblog_splash_text_color" value="<?php echo esc_attr( $text_color ); ?>" />
                            <p class="description">Màu sắc của tiêu đề và phụ đề.</p>
                        </td>
                    </tr>

                    <!-- Accent Color -->
                    <tr>
                        <th scope="row"><label for="techblog_splash_accent_color">Màu nhấn của nút bấm</label></th>
                        <td>
                            <input type="color" id="techblog_splash_accent_color" name="techblog_splash_accent_color" value="<?php echo esc_attr( $accent_color ); ?>" />
                            <p class="description">Màu nền của nút bấm đóng màn hình chào.</p>
                        </td>
                    </tr>

                    <!-- Exit Animation -->
                    <tr>
                        <th scope="row"><label for="techblog_splash_animation">Hiệu ứng biến mất</label></th>
                        <td>
                            <select id="techblog_splash_animation" name="techblog_splash_animation">
                                <option value="fade" <?php selected( $animation, 'fade' ); ?>>Mờ dần (Fade Out)</option>
                                <option value="slide-up" <?php selected( $animation, 'slide-up' ); ?>>Trượt lên phía trên (Slide Up)</option>
                                <option value="zoom-out" <?php selected( $animation, 'zoom-out' ); ?>>Thu nhỏ & Mờ dần (Zoom Out)</option>
                            </select>
                            <p class="description">Hiệu ứng chuyển cảnh khi màn hình chào đóng lại.</p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php submit_button( 'Lưu cấu hình màn hình chào', 'primary', 'submit', true ); ?>
        </form>
    </div>

    <!-- Media Library integration script -->
    <script type="text/javascript">
        jQuery(document).ready(function($) {
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
                    $('#techblog_splash_logo').val(attachment.url);
                    $('#techblog_splash_logo_preview').attr('src', attachment.url).show();
                });
                mediaUploader.open();
            });
        });
    </script>
    <?php
}

// 5. Inject Splash Screen into the Frontend on wp_body_open
function techblog_render_welcome_splash() {
    // Only show on frontend, not in admin/REST/AJAX
    if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
        return;
    }

    $enabled = get_option( 'techblog_splash_enabled', 0 );
    if ( ! $enabled ) {
        return;
    }

    $logo         = get_option( 'techblog_splash_logo', '' );
    $logo_width   = get_option( 'techblog_splash_logo_width', 180 );
    $title        = get_option( 'techblog_splash_title', 'Chào mừng bạn đến với TechBlog' );
    $subtitle     = get_option( 'techblog_splash_subtitle', 'Cập nhật tin tức công nghệ mới nhất mỗi ngày' );
    $btn_text     = get_option( 'techblog_splash_btn_text', 'Khám phá ngay' );
    $duration     = get_option( 'techblog_splash_duration', 5 );
    $frequency    = get_option( 'techblog_splash_frequency', 'session' );
    $bg_color     = get_option( 'techblog_splash_bg_color', '#0f172a' );
    $text_color   = get_option( 'techblog_splash_text_color', '#ffffff' );
    $accent_color = get_option( 'techblog_splash_accent_color', '#2563eb' );
    $animation    = get_option( 'techblog_splash_animation', 'fade' );

    // Inline critical styles for instant loading
    ?>
    <style type="text/css">
        #techblog-splash-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background-color: <?php echo esc_attr( $bg_color ); ?>;
            color: <?php echo esc_attr( $text_color ); ?>;
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            box-sizing: border-box;
            font-family: "Be Vietnam Pro", system-ui, sans-serif;
            text-align: center;
            opacity: 1;
            visibility: visible;
            transition: all 0.6s cubic-bezier(0.25, 1, 0.5, 1);
        }

        #techblog-splash-content {
            max-width: 480px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.8s cubic-bezier(0.25, 1, 0.5, 1);
        }

        #techblog-splash-content.active {
            transform: scale(1);
            opacity: 1;
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

        /* Exit Animations */
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
    </style>

    <!-- Prevent Flash of Unstyled Content (FOUC) via early JS execution -->
    <script type="text/javascript">
        (function() {
            var frequency = <?php echo json_encode( $frequency ); ?>;
            var isUserAdmin = <?php echo current_user_can( 'manage_options' ) ? 'true' : 'false'; ?>;
            
            // Allow administrators to bypass cookies/localstorage for testing purposes, but keep standard frequency for guests
            var shouldShow = true;
            if (frequency === 'session') {
                if (sessionStorage.getItem('techblog_splash_shown')) {
                    shouldShow = false;
                }
            } else if (frequency === 'day') {
                var lastShown = localStorage.getItem('techblog_splash_shown_date');
                if (lastShown) {
                    var today = new Date().toDateString();
                    if (lastShown === today) {
                        shouldShow = false;
                    }
                }
            }

            if (!shouldShow) {
                // If it shouldn't show, we inject a style to hide the overlay immediately
                document.write('<style type="text/css">#techblog-splash-overlay { display: none !important; }</style>');
            }
        })();
    </script>

    <!-- Welcome Splash Screen Overlay markup -->
    <div id="techblog-splash-overlay" role="dialog" aria-modal="true" aria-labelledby="splash-title">
        <div id="techblog-splash-content">
            <!-- Logo Section -->
            <?php if ( ! empty( $logo ) ) : ?>
                <img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $title ); ?> Logo" class="techblog-splash-logo-img" style="width: <?php echo intval( $logo_width ); ?>px; display: block; margin: 0 auto 8px auto;" />
            <?php else : ?>
                <!-- Sleek minimal fallback logo if none loaded -->
                <div style="background-color: <?php echo esc_attr( $accent_color ); ?>; width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 28px; color: #ffffff; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2); margin: 0 auto 8px auto;">
                    TB
                </div>
            <?php endif; ?>

            <!-- Title & Subtitle -->
            <div style="display: flex; flex-direction: column; align-items: center; gap: 10px; width: 100%;">
                <h1 id="splash-title" class="techblog-splash-title" style="text-align: center; width: 100%;"><?php echo esc_html( $title ); ?></h1>
                <?php if ( ! empty( $subtitle ) ) : ?>
                    <p class="techblog-splash-subtitle" style="text-align: center; width: 100%; margin: 0 auto;"><?php echo esc_html( $subtitle ); ?></p>
                <?php endif; ?>
            </div>

            <!-- CTA Button -->
            <button id="techblog-splash-btn" class="techblog-splash-btn" autofocus>
                <span><?php echo esc_html( $btn_text ); ?></span>
            </button>

            <!-- Countdown Timer Progress Indicator -->
            <?php if ( intval( $duration ) > 0 ) : ?>
                <div class="techblog-splash-progress-container">
                    <div id="techblog-splash-progress-bar" class="techblog-splash-progress-bar"></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Script to manage showing, counting down, and exit animations -->
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var overlay = document.getElementById('techblog-splash-overlay');
            var content = document.getElementById('techblog-splash-content');
            var closeBtn = document.getElementById('techblog-splash-btn');
            var progressBar = document.getElementById('techblog-splash-progress-bar');
            
            // If the element is hidden (due to sessionStorage check above), do nothing
            if (!overlay || window.getComputedStyle(overlay).display === 'none') {
                return;
            }

            var duration = <?php echo intval( $duration ); ?>;
            var frequency = <?php echo json_encode( $frequency ); ?>;
            var animationType = <?php echo json_encode( $animation ); ?>;

            // Set flags in storage immediately upon display
            if (frequency === 'session') {
                sessionStorage.setItem('techblog_splash_shown', 'true');
            } else if (frequency === 'day') {
                localStorage.setItem('techblog_splash_shown_date', new Date().toDateString());
            }

            // Lock page scroll when active
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';

            // Phase 1: Fade-in/Pop-in the inner content smoothly
            setTimeout(function() {
                content.classList.add('active');
                
                // Start progress bar shrink animation
                if (progressBar) {
                    progressBar.style.transform = 'scaleX(0)';
                }
            }, 100);

            // Close function handles exit animation and cleanup
            function closeSplash() {
                // Determine animation class
                var exitClass = 'exit-fade';
                if (animationType === 'slide-up') {
                    exitClass = 'exit-slide-up';
                } else if (animationType === 'zoom-out') {
                    exitClass = 'exit-zoom-out';
                }

                overlay.classList.add(exitClass);

                // Release scrolling locks
                document.documentElement.style.overflow = '';
                document.body.style.overflow = '';

                // Clean up DOM after transition finishes
                setTimeout(function() {
                    overlay.style.display = 'none';
                    overlay.remove();
                }, 600);
            }

            // Bind click to button
            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeSplash();
                });
            }

            // Auto close after duration
            if (duration > 0) {
                var autoCloseTimer = setTimeout(function() {
                    closeSplash();
                }, duration * 1000);
                
                // Clear timer if user manual-closes early
                if (closeBtn) {
                    closeBtn.addEventListener('click', function() {
                        clearTimeout(autoCloseTimer);
                    });
                }
            }
        });
    </script>
    <?php
}
add_action( 'wp_body_open', 'techblog_render_welcome_splash', 5 );
