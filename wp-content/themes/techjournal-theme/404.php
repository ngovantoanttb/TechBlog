<?php
/**
 * The template for displaying 404 pages (Not Found) - TechBlog Style
 *
 * @package TechJournal
 * @since 1.0.0
 */

get_header(); ?>

<main class="pt-6 sm:pt-8 pb-section-gap bg-background min-h-[70vh] flex items-center justify-center">
    <div class="text-center max-w-lg bg-white p-8 sm:p-16 border border-slate-100/80 premium-shadow">
        
        <div class="w-20 h-20 bg-primary/10 flex items-center justify-center text-primary mx-auto mb-6 border border-primary/20">
            <?php echo techjournal_get_svg( 'info', 'w-10 h-10 text-primary fill-current' ); ?>
        </div>
        
        <h1 class="font-display text-6xl font-black text-slate-300 tracking-tighter mb-2">404</h1>
        <h2 class="font-display text-xl font-extrabold text-slate-800 uppercase tracking-tight mb-4">Không Tìm Thấy Trang</h2>
        
        <p class="text-slate-500 text-[13px] leading-relaxed mb-8">
            Trang bạn đang tìm kiếm có thể đã bị xóa, thay đổi tên hoặc tạm thời không khả dụng. Hãy quay lại trang chủ của hệ thống.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="bg-primary hover:bg-[#cc0000] text-white font-bold py-2.5 px-6 text-xs uppercase tracking-wider transition-all duration-300 active:scale-95 shadow-[0_4px_12px_rgba(255,0,0,0.15)] text-center">
                Về Trang Chủ
            </a>
            
        </div>
    </div>
</main>

<?php get_footer(); ?>
