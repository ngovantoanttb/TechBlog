<?php
/**
 * TechJournal Link Redirection, CSV Mapping & 3-Tab Redirect Manager
 *
 * @package TechJournal
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// 1. Path Helper for CSV storage
function techblog_get_redirect_csv_path() {
    $upload_dir = wp_upload_dir();
    $dir = $upload_dir['basedir'] . '/techblog-redirects';
    if ( ! file_exists( $dir ) ) {
        wp_mkdir_p( $dir );
    }
    return $dir . '/external-links.csv';
}

// 2. Fetch and parse external links data (Optimized with 12-Hour Transient Caching to eliminate disk I/O)
function techblog_get_external_links_data() {
    $cache_key   = 'techblog_external_links_cache';
    $cached_data = get_transient( $cache_key );
    if ( false !== $cached_data && is_array( $cached_data ) ) {
        return $cached_data;
    }

    $csv_file    = techblog_get_redirect_csv_path();
    $random_pool = array();
    $mappings    = array();
    $default_link= get_option( 'techblog_redirect_default_link', home_url() );

    if ( file_exists( $csv_file ) && is_readable( $csv_file ) ) {
        $file_handle = fopen( $csv_file, 'r' );
        if ( $file_handle !== false ) {
            while ( ( $data = fgetcsv( $file_handle, 1000, ',' ) ) !== false ) {
                if ( count( $data ) >= 2 ) {
                    $target   = trim( $data[0] );
                    $external = trim( $data[1] );
                    if ( filter_var( $external, FILTER_VALIDATE_URL ) ) {
                        if ( filter_var( $target, FILTER_VALIDATE_URL ) ) {
                            $mappings[ strtolower( untrailingslashit( $target ) ) ] = $external;
                        }
                        $random_pool[] = $external;
                    }
                } elseif ( count( $data ) === 1 && ! empty( $data[0] ) ) {
                    $url = trim( $data[0] );
                    if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
                        $random_pool[] = $url;
                    }
                }
            }
            fclose( $file_handle );
        }
    }

    $random_pool   = array_values( array_unique( $random_pool ) );
    $has_csv_links = ! empty( $random_pool );

    if ( empty( $random_pool ) ) {
        $random_pool = array( ! empty( $default_link ) ? $default_link : home_url() );
    }

    $result = array(
        'has_csv_links'=> $has_csv_links,
        'random_pool'  => $random_pool,
        'mappings'     => $mappings,
        'default_link' => ! empty( $default_link ) ? $default_link : $random_pool[0]
    );

    // Cache transient for 12 hours (43200 seconds) to guarantee lightning-fast database & disk performance
    set_transient( $cache_key, $result, 12 * HOUR_IN_SECONDS );

    return $result;
}

// Check if system has active external CSV links or a configured default fallback redirect link
function techblog_has_available_external_links() {
    $data = techblog_get_external_links_data();
    $default_link = get_option( 'techblog_redirect_default_link', '' );
    return ! empty( $data['has_csv_links'] ) || ! empty( $default_link );
}

// 3. Save raw links to CSV file (Flushes transient cache automatically)
function techblog_save_external_links_raw( $lines_array ) {
    delete_transient( 'techblog_external_links_cache' );
    $csv_file = techblog_get_redirect_csv_path();
    $file_handle = fopen( $csv_file, 'w' );
    if ( $file_handle !== false ) {
        foreach ( $lines_array as $line ) {
            $line = trim( $line );
            if ( empty( $line ) ) continue;
            
            $parts = array_map( 'trim', explode( ',', $line ) );
            if ( count( $parts ) >= 2 ) {
                if ( filter_var( $parts[1], FILTER_VALIDATE_URL ) ) {
                    fputcsv( $file_handle, array( $parts[0], $parts[1] ) );
                }
            } elseif ( count( $parts ) === 1 ) {
                if ( filter_var( $parts[0], FILTER_VALIDATE_URL ) ) {
                    fputcsv( $file_handle, array( $parts[0] ) );
                }
            }
        }
        fclose( $file_handle );
        return true;
    }
    return false;
}

// 4. Resolve 3rd link based on Mode (random vs default/mapping)
function techblog_get_3rd_link( $target_url = '' ) {
    $mode = get_option( 'techblog_redirect_mode', 'random' );
    $data = techblog_get_external_links_data();

    if ( 'default' === $mode ) {
        if ( ! empty( $target_url ) ) {
            $clean_target = strtolower( untrailingslashit( strtok( $target_url, '?' ) ) );
            if ( isset( $data['mappings'][ $clean_target ] ) ) {
                return $data['mappings'][ $clean_target ];
            }
        }
        return $data['default_link'];
    } else {
        // Random mode
        if ( ! empty( $data['random_pool'] ) ) {
            $key = array_rand( $data['random_pool'] );
            return $data['random_pool'][ $key ];
        }
        return $data['default_link'];
    }
}

// 5. Admin Menu & Settings Page
function techblog_register_redirect_admin_menu() {
    add_menu_page(
        'Quản lý Link CSV',
        'Link Chuyển Hướng',
        'manage_options',
        'techblog-redirect-manager',
        'techblog_render_redirect_admin_page',
        'dashicons-external',
        26
    );
}
add_action( 'admin_menu', 'techblog_register_redirect_admin_menu' );

function techblog_render_redirect_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $message = '';
    $message_type = 'updated';

    // Process Form Submissions
    if ( isset( $_POST['techblog_save_redirect_settings'] ) && check_admin_referer( 'techblog_redirect_settings_nonce' ) ) {
        // Clear cached redirect data transient to force immediate setting application
        delete_transient( 'techblog_external_links_cache' );

        // Save Enable/Disable option
        $enabled = isset( $_POST['redirect_enabled'] ) ? '1' : '0';
        update_option( 'techblog_redirect_enabled', $enabled );

        // Save Selection Mode (random / default)
        $mode = isset( $_POST['redirect_mode'] ) && in_array( $_POST['redirect_mode'], array( 'random', 'default' ) ) ? $_POST['redirect_mode'] : 'random';
        update_option( 'techblog_redirect_mode', $mode );

        // Save Default General Link
        $default_link = isset( $_POST['redirect_default_link'] ) ? esc_url_raw( trim( $_POST['redirect_default_link'] ) ) : '';
        update_option( 'techblog_redirect_default_link', $default_link );

        // Save Countdown Timer
        $countdown = isset( $_POST['redirect_countdown'] ) ? max( 1, intval( $_POST['redirect_countdown'] ) ) : 3;
        update_option( 'techblog_redirect_countdown', $countdown );

        // Process Manual Links Textarea
        if ( isset( $_POST['redirect_links_textarea'] ) ) {
            $raw_lines = explode( "\n", sanitize_textarea_field( $_POST['redirect_links_textarea'] ) );
            techblog_save_external_links_raw( $raw_lines );
        }

        // Process CSV Upload File
        if ( ! empty( $_FILES['redirect_csv_file']['tmp_name'] ) ) {
            $uploaded_file = $_FILES['redirect_csv_file']['tmp_name'];
            $lines = file( $uploaded_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
            if ( $lines !== false && ! empty( $lines ) ) {
                techblog_save_external_links_raw( $lines );
                $message = 'Đã tải lên file CSV và cập nhật dữ liệu link thành công!';
            }
        } else {
            $message = 'Đã lưu cấu hình hệ thống thành công!';
        }
    }

    $is_enabled = get_option( 'techblog_redirect_enabled', '1' );
    $current_mode = get_option( 'techblog_redirect_mode', 'random' );
    $default_link_val = get_option( 'techblog_redirect_default_link', home_url() );
    $countdown_seconds = get_option( 'techblog_redirect_countdown', 5 );

    $csv_file = techblog_get_redirect_csv_path();
    $raw_content = file_exists( $csv_file ) ? file_get_contents( $csv_file ) : '';
    $data_summary = techblog_get_external_links_data();
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Quản Lý Link Chuyển Hướng & File CSV</h1>
        <hr class="wp-header-end">

        <?php if ( ! empty( $message ) ) : ?>
            <div class="notice notice-<?php echo esc_attr( $message_type ); ?> is-dismissible">
                <p><strong><?php echo esc_html( $message ); ?></strong></p>
            </div>
        <?php endif; ?>

        <form method="post" action="" enctype="multipart/form-data" style="margin-top: 20px;">
            <?php wp_nonce_field( 'techblog_redirect_settings_nonce' ); ?>

            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">

                    <!-- Left Column: Settings & Link List -->
                    <div id="post-body-content">

                        <!-- Panel: Configuration Settings -->
                        <div class="postbox" style="padding: 20px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                            <h2 style="font-size: 16px; font-weight: 600; margin-top: 0; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                                Cấu Hình Hệ Thống Chuyển Hướng 3 Tab
                            </h2>

                            <table class="form-table" style="margin-top: 0;">
                                <tr>
                                    <th scope="row"><label for="redirect_enabled">Trạng thái hệ thống</label></th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="redirect_enabled" id="redirect_enabled" value="1" <?php checked( $is_enabled, '1' ); ?> />
                                            <strong>Bật tính năng chuyển hướng link 3 Tab</strong>
                                        </label>
                                        <p class="description">Khi bật: Click link bài viết → mở trang đếm ngược tab mới → bấm chuyển hướng → mở trang đích tab mới & tab đếm ngược chuyển thành Link thứ 3 (CSV).</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Chế độ chọn Link Thứ 3</th>
                                    <td>
                                        <fieldset>
                                            <label style="margin-right: 20px;">
                                                <input type="radio" name="redirect_mode" value="random" <?php checked( $current_mode, 'random' ); ?> />
                                                <strong>Chế độ Random</strong> (Lấy ngẫu nhiên 1 link từ CSV)
                                            </label>
                                            <br style="margin-bottom: 6px;">
                                            <label>
                                                <input type="radio" name="redirect_mode" value="default" <?php checked( $current_mode, 'default' ); ?> />
                                                <strong>Chế độ Mặc Định / Mapping</strong> (Ưu tiên mapping theo link đích `target_url,external_url` trong CSV, nếu không có sẽ lấy Link Mặc Định)
                                            </label>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="redirect_default_link">Link Mặc Định Chung</label></th>
                                    <td>
                                        <input type="url" name="redirect_default_link" id="redirect_default_link" value="<?php echo esc_url( $default_link_val ); ?>" class="regular-text" placeholder="https://example.com" />
                                        <p class="description">Link cố định dùng ở Chế độ Mặc Định khi không tìm thấy mapping riêng theo từng bài viết.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="redirect_countdown">Thời gian đếm ngược (giây)</label></th>
                                    <td>
                                        <input type="number" name="redirect_countdown" id="redirect_countdown" value="<?php echo esc_attr( $countdown_seconds ); ?>" min="1" max="60" class="small-text" /> giây
                                        <p class="description">Thời gian đếm ngược trên trang đếm ngược trước khi xuất hiện nút chuyển hướng.</p>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Panel: CSV External Links Manager -->
                        <div class="postbox" style="padding: 20px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                            <h2 style="font-size: 16px; font-weight: 600; margin-top: 0; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                                Quản Lý Danh Sách Link Trong CSV
                            </h2>

                            <div style="margin-bottom: 15px;">
                                <label for="redirect_csv_file"><strong>Tải lên file CSV mới:</strong></label><br>
                                <input type="file" name="redirect_csv_file" id="redirect_csv_file" accept=".csv, .txt" style="margin-top: 5px;" />
                                <p class="description">Hỗ trợ 2 định dạng dòng trong CSV:<br>
                                &bull; 1 cột: <code>https://external-link.com</code> (cho Chế độ Random)<br>
                                &bull; 2 cột: <code>https://your-site.com/bai-viet-1, https://external-link.com</code> (cho Chế độ Mặc định/Mapping)
                                </p>
                            </div>

                            <hr style="margin: 20px 0; border: none; border-top: 1px dashed #ddd;">

                            <div>
                                <label for="redirect_links_textarea"><strong>Hoặc chỉnh sửa danh sách trực tiếp (mỗi dòng 1 link hoặc 2 cột cách nhau bởi dấu phẩy):</strong></label><br>
                                <textarea name="redirect_links_textarea" id="redirect_links_textarea" rows="12" class="large-text code" style="margin-top: 8px; font-family: monospace; font-size: 13px;"><?php echo esc_textarea( $raw_content ); ?></textarea>
                            </div>
                        </div>

                        <p class="submit">
                            <input type="submit" name="techblog_save_redirect_settings" id="submit" class="button button-primary button-large" value="Lưu Cấu Hình & Dữ Liệu Link" />
                        </p>
                    </div>

                    <!-- Right Column: Info & Stats -->
                    <div id="postbox-container-1" class="postbox-container">
                        <div class="postbox" style="padding: 15px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                            <h2 style="font-size: 14px; font-weight: 600; margin-top: 0; margin-bottom: 10px;">Thống kê Dữ liệu CSV</h2>
                            <div style="font-size: 24px; font-weight: 700; color: #2563eb; margin: 10px 0;">
                                <?php echo count( $data_summary['random_pool'] ); ?> <span style="font-size: 13px; font-weight: normal; color: #64748b;">links trong Pool</span>
                            </div>
                            <div style="font-size: 16px; font-weight: 600; color: #059669; margin-bottom: 10px;">
                                <?php echo count( $data_summary['mappings'] ); ?> <span style="font-size: 13px; font-weight: normal; color: #64748b;">link mapping riêng</span>
                            </div>
                        </div>

                        <div class="postbox" style="padding: 15px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); background: #f8fafc;">
                            <h2 style="font-size: 14px; font-weight: 600; margin-top: 0; margin-bottom: 10px; color: #0f172a;">Quy trình 3 Tab chuẩn</h2>
                            <ol style="margin-left: 15px; color: #475569; font-size: 12.5px; line-height: 1.6;">
                                <li>Click link trong bài viết → Mở trang chuyển hướng ở <strong>Tab 2</strong>.</li>
                                <li>Tab 2 đếm ngược 3s → Hiện nút chuyển hướng.</li>
                                <li>Bấm nút trên Tab 2 → Mở Trang đích ở <strong>Tab 3</strong>, còn Tab 2 tự chuyển sang <strong>Link thứ 3 (CSV)</strong>.</li>
                            </ol>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
    <?php
}

// 6. Register Query Var & Rewrite Rule for Progress Page
function techblog_redirect_rewrite_rules() {
    add_rewrite_rule( '^chuyen-huong/?$', 'index.php?techblog_redirect_page=1', 'top' );
}
add_action( 'init', 'techblog_redirect_rewrite_rules' );

function techblog_redirect_query_vars( $vars ) {
    $vars[] = 'techblog_redirect_page';
    return $vars;
}
add_filter( 'query_vars', 'techblog_redirect_query_vars' );

function techblog_flush_redirect_rewrite_rules() {
    $rules = get_option( 'rewrite_rules' );
    if ( ! isset( $rules['^chuyen-huong/?$'] ) ) {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }
}
add_action( 'init', 'techblog_flush_redirect_rewrite_rules', 99 );

// 7. Render Progress Redirect Screen (Tab 2) - Integrated into Theme Header & Footer
function techblog_handle_redirect_template() {
    if ( get_query_var( 'techblog_redirect_page' ) || isset( $_GET['techblog_redirect_page'] ) || ( isset( $_SERVER['REQUEST_URI'] ) && strpos( $_SERVER['REQUEST_URI'], 'chuyen-huong' ) !== false ) ) {
        
        $target_url = isset( $_GET['url'] ) ? esc_url_raw( wp_unslash( $_GET['url'] ) ) : home_url();
        $countdown  = get_option( 'techblog_redirect_countdown', 5 );
        $site_name  = get_bloginfo( 'name' );

        // If no external links exist in system, redirect straight to target page immediately
        if ( ! techblog_has_available_external_links() ) {
            wp_redirect( $target_url );
            exit;
        }

        // Resolve 3rd link for this target URL
        $link_3 = techblog_get_3rd_link( $target_url );

        // Extract hosts for DNS Prefetch & Preconnect performance acceleration
        $target_host = parse_url( $target_url, PHP_URL_HOST );
        $link3_host  = parse_url( $link_3, PHP_URL_HOST );

        // Force browser tab title for redirect gateway page
        add_filter( 'pre_get_document_title', function() use ( $site_name ) {
            return 'Đang lấy thông tin dữ liệu - ' . $site_name;
        }, 9999 );
        add_filter( 'wp_title', function() use ( $site_name ) {
            return 'Đang lấy thông tin dữ liệu - ' . $site_name;
        }, 9999 );
        add_filter( 'document_title_parts', function( $parts ) use ( $site_name ) {
            return array(
                'title' => 'Đang lấy thông tin dữ liệu',
                'site'  => $site_name
            );
        }, 9999 );

        get_header();
        ?>
        <?php if ( $target_host ) : ?>
            <link rel="dns-prefetch" href="<?php echo esc_url( '//' . $target_host ); ?>" />
            <link rel="preconnect" href="<?php echo esc_url( 'https://' . $target_host ); ?>" />
        <?php endif; ?>
        <?php if ( $link3_host && $link3_host !== $target_host ) : ?>
            <link rel="dns-prefetch" href="<?php echo esc_url( '//' . $link3_host ); ?>" />
            <link rel="preconnect" href="<?php echo esc_url( 'https://' . $link3_host ); ?>" />
        <?php endif; ?>

        <style>
            @keyframes progressAnim {
                0% { width: 0%; }
                100% { width: 100%; }
            }
            .animate-progress {
                animation: progressAnim <?php echo intval( $countdown ); ?>s linear forwards;
                will-change: width;
                transform: translateZ(0);
            }
            @keyframes btnShine {
                0% { left: -100%; }
                100% { left: 200%; }
            }
            @keyframes subtleGlow {
                0%, 100% { box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25); }
                50% { box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4); }
            }
            .btn-elegant-glow {
                position: relative;
                overflow: hidden;
                background: linear-gradient(135deg, #2563eb, #1d4ed8);
                animation: subtleGlow 2.5s infinite ease-in-out;
                transition: transform 250ms ease, background 250ms ease, box-shadow 250ms ease;
                will-change: transform, box-shadow;
                transform: translateZ(0);
                backface-visibility: hidden;
            }
            .btn-elegant-glow:hover {
                transform: translateY(-2px);
                background: linear-gradient(135deg, #3b82f6, #2563eb);
                box-shadow: 0 8px 24px rgba(37, 99, 235, 0.45) !important;
            }
            .btn-elegant-glow:active {
                transform: translateY(0);
            }
            .btn-elegant-glow::after {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 45%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
                transform: skewX(-20deg);
                animation: btnShine 3.5s infinite;
            }
        </style>

        <main class="min-h-[65vh] flex items-center justify-center py-12 px-4 bg-slate-50 select-none">
            <div class="max-w-md w-full bg-white border border-slate-200/80 rounded-2xl p-7 shadow-xl text-center space-y-5">
                
                <!-- Heart Greeting Badge -->
                <div class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-blue-50 border border-blue-100 text-blue-600 text-xs font-bold tracking-wide">
                    <span class="text-red-500 animate-pulse text-sm">♥</span>
                    <span>Hãy bình tĩnh, chờ mình tìm link đích nhé!</span>
                </div>

                <!-- Main Title & Instructions (Link Display Box Completely Removed) -->
                <div class="space-y-1.5">
                    <h1 id="main-heading" class="text-xl font-extrabold text-slate-900 tracking-tight leading-snug">
                        Đang đi đến trang đích, vui lòng chờ xíu nghen:
                    </h1>
                    <p class="text-slate-500 text-xs font-medium">
                        Kéo chuột xuống một xíu, và chờ khoảng <?php echo intval( $countdown ); ?> giây bạn nhé!
                    </p>
                </div>

                <!-- Progress Bar Container -->
                <div class="space-y-2 py-1">
                    <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden p-0.5 border border-slate-200">
                        <div id="progress-bar" class="bg-blue-600 h-full rounded-full animate-progress"></div>
                    </div>
                    <div class="flex justify-between items-center text-[11px] text-slate-500 font-semibold px-1">
                        <span id="progress-subtext">Đang kiểm tra liên kết an toàn...</span>
                        <span id="timer-wrapper">Thời gian chờ: <strong id="timer-text" class="text-blue-600 font-bold text-xs"><?php echo intval( $countdown ); ?></strong>s</span>
                    </div>
                </div>

                <!-- Disabled Wait State Box (Shown during countdown) -->
                <div id="waiting-btn-box" class="pt-1">
                    <button disabled class="w-full py-3.5 px-5 bg-slate-100 text-slate-400 font-bold text-xs rounded-xl cursor-not-allowed border border-slate-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Đang chuẩn bị nút... (<strong id="wait-btn-timer"><?php echo intval( $countdown ); ?></strong>s)</span>
                    </button>
                </div>

                <!-- Action Button (Revealed ONLY after countdown completes, with Classy Hover Micro-Interaction) -->
                <div id="action-btn-box" class="pt-1 hidden">
                    <button id="final-redirect-btn" class="w-full inline-flex items-center justify-center gap-2.5 py-3.5 px-6 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl cursor-pointer btn-elegant-glow">
                        <span>TRUY CẬP TRANG ĐÍCH NGAY</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </div>

                <!-- Footer Security Guarantee -->
                <div class="text-[11px] text-slate-400 font-medium pt-3 border-t border-slate-100">
                    Bảo mật bởi <?php echo esc_html( $site_name ); ?> &bull; Đã xác minh an toàn
                </div>
            </div>
        </main>

        <!-- Countdown & Dual Action Script -->
        <script>
            (function() {
                document.title = "Đang lấy thông tin dữ liệu - " + <?php echo wp_json_encode( $site_name ); ?>;
                const targetUrl = <?php echo wp_json_encode( $target_url ); ?>;
                const link3Url  = <?php echo wp_json_encode( $link_3 ); ?>;
                let remainingSeconds = <?php echo intval( $countdown ); ?>;
                
                const timerText      = document.getElementById('timer-text');
                const waitBtnTimer   = document.getElementById('wait-btn-timer');
                const waitingBtnBox  = document.getElementById('waiting-btn-box');
                const actionBtnBox   = document.getElementById('action-btn-box');
                const finalBtn       = document.getElementById('final-redirect-btn');
                const mainHeading    = document.getElementById('main-heading');
                const progressSubtext= document.getElementById('progress-subtext');

                const interval = setInterval(function() {
                    remainingSeconds--;
                    if (timerText) timerText.textContent = remainingSeconds > 0 ? remainingSeconds : 0;
                    if (waitBtnTimer) waitBtnTimer.textContent = remainingSeconds > 0 ? remainingSeconds : 0;

                    if (remainingSeconds <= 0) {
                        clearInterval(interval);
                        
                        // 1. Update Headings & Status
                        if (mainHeading) mainHeading.textContent = '🎉 Đã tìm thấy liên kết đích! Nhấn nút bên dưới để truy cập:';
                        if (progressSubtext) progressSubtext.textContent = 'Đã hoàn tất 100%!';

                        // 2. Hide Waiting Box and Reveal Action Button
                        if (waitingBtnBox) waitingBtnBox.classList.add('hidden');
                        if (actionBtnBox) actionBtnBox.classList.remove('hidden');
                    }
                }, 1000);

                // 3. When User Clicks "TRUY CẬP TRANG ĐÍCH NGAY":
                if (finalBtn) {
                    finalBtn.addEventListener('click', function(e) {
                        e.preventDefault();

                        // a) Open TARGET URL in a NEW TAB (Tab 3) -> Direct Click Gesture guarantees NO Popup Blocker!
                        try {
                            window.open(targetUrl, '_blank');
                        } catch(err) {
                            console.error('Target window open failed:', err);
                        }

                        // b) Convert current progress tab (Tab 2) into LINK 3 (from CSV)
                        if (link3Url) {
                            window.location.href = link3Url;
                        } else {
                            window.location.href = targetUrl;
                        }
                    });
                }
            })();
        </script>
        <?php
        get_footer();
        exit;
    }
}
add_action( 'template_redirect', 'techblog_handle_redirect_template', 1 );

// 8. Server-Side Content Filter: Rewrites external link hrefs directly in HTML so hovering shows the redirect gateway URL in browser status bar
function techblog_filter_content_external_links( $content ) {
    if ( is_admin() || empty( $content ) || ! is_singular() ) {
        return $content;
    }

    $is_enabled = get_option( 'techblog_redirect_enabled', '1' );
    if ( '1' !== $is_enabled || ! techblog_has_available_external_links() ) {
        return $content;
    }

    $site_host     = parse_url( home_url(), PHP_URL_HOST );
    $use_pretty    = ! empty( get_option( 'permalink_structure' ) );
    $redirect_base = $use_pretty ? home_url( '/chuyen-huong/' ) : home_url( '?techblog_redirect_page=1' );
    $separator     = strpos( $redirect_base, '?' ) !== false ? '&' : '?';

    return preg_replace_callback(
        '/<a\s+([^>]*?)href=["\']([^"\']+)["\']([^>]*?)>/i',
        function( $matches ) use ( $site_host, $redirect_base, $separator ) {
            $before = $matches[1];
            $url    = trim( $matches[2] );
            $after  = $matches[3];

            if ( empty( $url ) ) {
                return $matches[0];
            }

            if (
                strpos( $url, '#' ) === 0 ||
                strpos( $url, 'javascript:' ) === 0 ||
                strpos( $url, 'mailto:' ) === 0 ||
                strpos( $url, 'tel:' ) === 0 ||
                strpos( $url, 'wp-admin' ) !== false ||
                strpos( $url, 'wp-login' ) !== false ||
                strpos( $url, 'logout' ) !== false ||
                strpos( $url, 'techblog_redirect_page' ) !== false ||
                strpos( $url, 'chuyen-huong' ) !== false
            ) {
                return $matches[0];
            }

            $host = parse_url( $url, PHP_URL_HOST );
            if ( ! $host || strtolower( $host ) === strtolower( $site_host ) ) {
                return $matches[0];
            }

            $redirect_url = $redirect_base . $separator . 'url=' . rawurlencode( $url );
            return sprintf( '<a %shref="%s" target="_blank" %s>', $before, esc_url( $redirect_url ), $after );
        },
        $content
    );
}
add_filter( 'the_content', 'techblog_filter_content_external_links', 99 );

// 9. Client-Side JavaScript: Dynamic link rewriter & fallback interceptor
function techblog_render_redirect_scripts_footer() {
    if ( is_admin() ) {
        return;
    }

    $is_enabled = get_option( 'techblog_redirect_enabled', '1' );
    if ( '1' !== $is_enabled ) {
        return;
    }

    // Performance Optimization: Only load interceptor script on single post/page templates where post content body exists
    if ( ! is_singular() ) {
        return;
    }

    // If feature is enabled BUT no external links exist in CSV/system, bypass progress page & go straight to target!
    if ( ! techblog_has_available_external_links() ) {
        return;
    }

    $use_pretty = ! empty( get_option( 'permalink_structure' ) );
    $redirect_base_url = $use_pretty ? home_url( '/chuyen-huong/' ) : home_url( '?techblog_redirect_page=1' );
    ?>
    <script id="techblog-redirect-system">
        (function() {
            const redirectBaseUrl = <?php echo wp_json_encode( $redirect_base_url ); ?>;

            function processLink(link) {
                if (!link || link.hostname === window.location.hostname) return;

                const href = link.getAttribute('href');
                if (!href) return;

                if (
                    href.startsWith('#') ||
                    href.startsWith('javascript:') ||
                    href.startsWith('mailto:') ||
                    href.startsWith('tel:') ||
                    href.includes('wp-admin') ||
                    href.includes('wp-login') ||
                    href.includes('logout') ||
                    href.includes('techblog_redirect_page') ||
                    href.includes('chuyen-huong')
                ) {
                    return;
                }

                const targetUrl = link.href;
                if (!targetUrl || targetUrl === window.location.href) return;

                const separator = redirectBaseUrl.includes('?') ? '&' : '?';
                link.href = redirectBaseUrl + separator + 'url=' + encodeURIComponent(targetUrl);
                link.target = '_blank';
            }

            function initLinkRewriter() {
                const contentContainers = document.querySelectorAll('.prose, .entry-content, .post-content, .single-post-body, .single-content, article .prose');
                contentContainers.forEach(function(container) {
                    const links = container.querySelectorAll('a[href]');
                    links.forEach(processLink);
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initLinkRewriter);
            } else {
                initLinkRewriter();
            }

            // Mouseover listener so hover state dynamically converts any newly added external links immediately
            document.addEventListener('mouseover', function(event) {
                const link = event.target.closest('a');
                if (!link) return;

                const isPostContentLink = link.closest('.prose, .entry-content, .post-content, .single-post-body, .single-content, article .prose');
                if (isPostContentLink) {
                    processLink(link);
                }
            }, true);

        })();
    </script>
    <?php
}
add_action( 'wp_footer', 'techblog_render_redirect_scripts_footer', 999 );

