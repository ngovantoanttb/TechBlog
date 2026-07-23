<?php
/**
 * TechJournal Security Hardening and Anti-DevTools Self-XSS Protection Module
 *
 * @package TechJournal
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// 1. File Editing in WP Admin Dashboard is ENABLED (Allow Theme/Plugin File Editor in Admin)
// if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
//     define( 'DISALLOW_FILE_EDIT', true );
// }

// 2. Hide WordPress Version & Unnecessary Metadata Information Leaks
function techjournal_cleanup_head_metadata() {
    // Remove WP version generator
    remove_action( 'wp_head', 'wp_generator' );
    // Remove Windows Live Writer manifest link
    remove_action( 'wp_head', 'wlwmanifest_link' );
    // Remove Really Simple Discovery (RSD) edit link
    remove_action( 'wp_head', 'rsd_link' );
    // Remove shortlink tag
    remove_action( 'wp_head', 'wp_shortlink_wp_head' );
    // Remove REST API link from head
    remove_action( 'wp_head', 'rest_output_link_wp_head' );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
}
add_action( 'init', 'techjournal_cleanup_head_metadata' );

// Hide WordPress version in RSS feeds & scripts/styles query strings
add_filter( 'the_generator', '__return_empty_string' );

// 3. Disable XML-RPC Services & Pingbacks to Prevent Brute-Force & DDoS Attacks
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'xmlrpc_methods', '__return_empty_array' );
function techjournal_remove_pingback_header( $headers ) {
    unset( $headers['X-Pingback'] );
    return $headers;
}
add_filter( 'wp_headers', 'techjournal_remove_pingback_header' );

// 4. Block Username Enumeration Scanners (?author=1)
function techjournal_block_user_enumeration() {
    if ( ! is_admin() && isset( $_GET['author'] ) ) {
        wp_safe_redirect( home_url( '/' ), 301 );
        exit;
    }
}
add_action( 'template_redirect', 'techjournal_block_user_enumeration' );

// 5. Inject Security HTTP Response Headers
function techjournal_send_security_headers( $headers ) {
    if ( ! is_admin() ) {
        $headers['X-Content-Type-Options'] = 'nosniff';
        $headers['X-Frame-Options']        = 'SAMEORIGIN';
        $headers['X-XSS-Protection']       = '1; mode=block';
        $headers['Referrer-Policy']        = 'strict-origin-when-cross-origin';
        $headers['Permissions-Policy']     = 'geolocation=(), microphone=(), camera=()';
    }
    return $headers;
}
add_filter( 'wp_headers', 'techjournal_send_security_headers' );

// 6. Strict Comment Sanitization & Anti-XSS Protection (Chống chèn mã độc vào bình luận)
function techjournal_sanitize_comment_author( $author ) {
    return wp_strip_all_tags( trim( $author ) );
}
add_filter( 'pre_comment_author_name', 'techjournal_sanitize_comment_author' );

function techjournal_sanitize_comment_content( $content ) {
    // Strip out all dangerous script tags, iframes, and inline event handlers
    return wp_kses_post( $content );
}
add_filter( 'pre_comment_content', 'techjournal_sanitize_comment_content' );


// 6. Register Anti-DevTools Security Toggle Setting in Settings -> General
function techjournal_register_security_settings() {
    register_setting( 'general', 'techblog_enable_anti_devtools', array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => '1'
    ) );

    add_settings_field(
        'techblog_enable_anti_devtools',
        'Bảo vệ Chống F12 & DevTools',
        'techjournal_anti_devtools_setting_callback',
        'general',
        'default'
    );
}
add_action( 'admin_init', 'techjournal_register_security_settings' );

function techjournal_anti_devtools_setting_callback() {
    $value = get_option( 'techblog_enable_anti_devtools', '1' );
    ?>
    <label>
        <input type="checkbox" name="techblog_enable_anti_devtools" value="1" <?php checked( $value, '1' ); ?> />
        <strong>Bật tính năng chặn F12 / DevTools & Cảnh báo Console cho khách truy cập</strong>
    </label>
    <p class="description">Tài khoản Quản trị viên (Admin / Developer) đang đăng nhập sẽ tự động được miễn trừ để hỗ trợ kiểm thử và debug dễ dàng.</p>
    <?php
}

// 7. Anti-DevTools & Self-XSS Console Warning Security Script
function techjournal_render_security_anti_devtools_script() {
    // 1. AUTOMATIC BYPASS: Logged-in Administrators / Developers will NEVER be blocked by Anti-DevTools
    if ( is_admin() || current_user_can( 'manage_options' ) ) {
        return;
    }

    // 2. CHECK SETTING: If Admin toggled feature OFF, bypass completely
    $enabled = get_option( 'techblog_enable_anti_devtools', '1' );
    if ( '1' !== $enabled ) {
        return;
    }
    ?>
    <script id="techblog-security-protection">
        (function() {
            // 1. Console Self-XSS Warning & Rapid Console Wipe (Clears any typed console input every 100ms)
            const printSecurityNotice = function() {
                if (window.console) {
                    console.clear();
                    console.log(
                        '%cDỪNG LẠI! / STOP!',
                        'color: red; font-size: 40px; font-weight: bold; padding: 5px 0;'
                    );
                    console.log(
                        '%cĐây là một tính năng dành cho nhà phát triển hệ thống. Nếu ai đó bảo bạn sao chép và dán bất kỳ đoạn mã nào vào đây, đó là trò lừa đảo và có thể dẫn đến việc tài khoản của bạn bị đánh cắp!',
                        'color: blue; font-size: 16px; font-weight: bold; line-height: 1.5;'
                    );
                }
            };

            printSecurityNotice();
            setInterval(printSecurityNotice, 100);

            // 2. Block Right Click (Context Menu) completely
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }, true);

            // 3. Intercept DevTools Keyboard Shortcuts (F12, Ctrl+Shift+I/J/C, Ctrl+U, Ctrl+S)
            document.addEventListener('keydown', function(e) {
                // F12 Key
                if (e.key === 'F12' || e.keyCode === 123) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('CẢNH BÁO BẢO MẬT: Thao tác mở DevTools (F12) bị vô hiệu hóa vì mục đích an toàn!');
                    return false;
                }

                // Ctrl+Shift+I / Cmd+Option+I (Inspect)
                // Ctrl+Shift+J / Cmd+Option+J (Console)
                // Ctrl+Shift+C / Cmd+Option+C (Inspect element)
                // Ctrl+U / Cmd+Option+U (View Source)
                // Ctrl+S / Cmd+S (Save Page)
                if (e.ctrlKey || e.metaKey) {
                    const key = e.key ? e.key.toLowerCase() : '';
                    const keyCode = e.keyCode;

                    if (
                        (e.shiftKey && (key === 'i' || key === 'j' || key === 'c' || keyCode === 73 || keyCode === 74 || keyCode === 67)) ||
                        (key === 'u' || keyCode === 85) ||
                        (key === 's' || keyCode === 83)
                    ) {
                        e.preventDefault();
                        e.stopPropagation();
                        alert('CẢNH BÁO BẢO MẬT: Thao tác kiểm tra mã nguồn bị vô hiệu hóa vì mục đích an toàn!');
                        return false;
                    }
                }
            }, true);

            // 4. Anti-DevTools Detection & Auto Page Reload / Lockdown
            let isDevToolsOpened = false;
            const detectDevTools = function() {
                const widthDiff = window.outerWidth - window.innerWidth > 160;
                const heightDiff = window.outerHeight - window.innerHeight > 160;

                const start = performance.now();
                (function() {}.constructor('debugger')());
                const end = performance.now();
                const debuggerActive = (end - start) > 100;

                if (widthDiff || heightDiff || debuggerActive) {
                    if (!isDevToolsOpened) {
                        isDevToolsOpened = true;
                        document.body.innerHTML = '<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100vh;background:#0f172a;color:#ef4444;font-family:sans-serif;font-weight:bold;font-size:22px;text-align:center;padding:20px;"><span>⛔ CẢNH BÁO: KHÔNG ĐƯỢC PHÉP MỞ DEVTOOLS</span><span style="font-size:14px;color:#94a3b8;margin-top:12px;">Website sẽ tự động tải lại để bảo vệ an toàn dữ liệu...</span></div>';
                        setTimeout(function() {
                            window.location.reload();
                        }, 1200);
                    }
                } else {
                    isDevToolsOpened = false;
                }
            };

            setInterval(detectDevTools, 500);
            window.addEventListener('resize', detectDevTools);

        })();
    </script>
    <?php
}
add_action( 'wp_footer', 'techjournal_render_security_anti_devtools_script', 9999 );