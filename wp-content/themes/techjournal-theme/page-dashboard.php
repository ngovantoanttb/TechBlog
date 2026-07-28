<?php
/**
 * Template Name: Dashboard Analytics
 *
 * Front-end Analytics Dashboard Page Template for site administrators.
 *
 * @package TechJournal
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<div class="techjournal-frontend-dashboard bg-slate-50 dark:bg-slate-900 min-h-screen py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ( current_user_can( 'manage_options' ) ) : ?>
            <?php
            if ( function_exists( 'techblog_render_analytics_dashboard_page' ) ) {
                techblog_render_analytics_dashboard_page();
            } else {
                echo '<div class="p-6 bg-white dark:bg-slate-800 rounded-2xl shadow-sm text-center text-slate-600 dark:text-slate-300">Hệ thống Dashboard đang được chuẩn bị.</div>';
            }
            ?>
        <?php else : ?>
            <div class="max-w-md mx-auto my-16 p-8 bg-white dark:bg-slate-800 rounded-3xl shadow-xl border border-slate-200/80 dark:border-slate-700/80 text-center space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-rose-500/10 text-rose-500 flex items-center justify-center mx-auto text-2xl font-bold">
                    🔒
                </div>
                <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">Quyền truy cập bị từ chối</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Trang Bảng Điều Khiển Analytics chỉ dành riêng cho tài khoản Quản trị viên (Administrator).</p>
                <div class="pt-2">
                    <a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs transition-all shadow-md">
                        Đăng nhập tài khoản Admin
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
