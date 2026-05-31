<?php
/**
 * Template Name: About Page (Trang Giới Thiệu)
 *
 * The template for displaying the premium About Page - TechBlog Editorial.
 *
 * @package TechJournal
 * @since 1.0.0
 */

get_header(); ?>

<main class="pt-6 sm:pt-8 pb-section-gap bg-background min-h-screen">
    <div class="max-w-3xl mx-auto bg-white p-6 sm:p-12 border border-slate-100/80 premium-shadow">
        
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) : the_post();
                ?>
                <!-- Page Breadcrumbs -->
                <nav class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-5" aria-label="Breadcrumb">
                    <a class="hover:text-[#ff0000] transition-all" href="<?php echo esc_url( home_url( '/' ) ); ?>">Trang Chủ</a>
                    <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                    <span class="text-slate-600 truncate"><?php the_title(); ?></span>
                </nav>
                
                <!-- Page Title -->
                <h1 class="font-display text-2xl sm:text-3xl md:text-4xl text-slate-900 font-extrabold tracking-tight leading-tight mb-6 pb-4 border-b border-slate-100">
                    <?php the_title(); ?>
                </h1>
                           <!-- Editorial Intro Quote -->
                <div class="border-l-4 border-[#ff0000] pl-6 my-8 italic text-slate-700 text-base md:text-lg leading-relaxed font-display">
                    "TechBlog ra đời với khát vọng trở thành chiếc cầu nối tri thức vững chắc, đưa những công nghệ tiên tiến nhất thế giới đến gần hơn với cộng đồng đam mê công nghệ và lập trình tại Việt Nam."
                </div>

                <!-- Page Content Body (Dynamic from Admin) -->
                <div class="prose max-w-none text-slate-600 text-[14.5px] leading-relaxed mb-8 wp-entry-content">
                    <?php the_content(); ?>
                </div>

                <!-- Strategic Pillars: Vision & Mission (2-Column Grid - Flattened) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 my-10">
                    <!-- Mission -->
                    <div class="border border-slate-100 p-6 bg-slate-50/30">
                        <div class="w-10 h-10 bg-red-50 text-[#ff0000] flex items-center justify-center mb-4 rounded-none">
                            <span class="material-symbols-outlined text-[20px]">explore</span>
                        </div>
                        <h3 class="font-display text-sm font-black text-slate-800 uppercase tracking-tight mb-2.5">Sứ Mệnh</h3>
                        <p class="text-slate-500 text-xs sm:text-[13px] leading-relaxed">
                            Cung cấp tin tức công nghệ chính xác, chuyên sâu và nhanh chóng nhất. Truyền tải kiến thức lập trình chất lượng cao đến hàng triệu lập trình viên Việt.
                        </p>
                    </div>

                    <!-- Vision -->
                    <div class="border border-slate-100 p-6 bg-slate-50/30">
                        <div class="w-10 h-10 bg-red-50 text-[#ff0000] flex items-center justify-center mb-4 rounded-none">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </div>
                        <h3 class="font-display text-sm font-black text-slate-800 uppercase tracking-tight mb-2.5">Tầm Nhìn</h3>
                        <p class="text-slate-500 text-xs sm:text-[13px] leading-relaxed">
                            Trở thành chuyên trang công nghệ uy tín hàng đầu khu vực, là địa chỉ tin cậy hàng đầu cho độc giả tìm kiếm tri thức và giải pháp kỹ thuật số mới nhất.
                        </p>
                    </div>
                </div>

                <!-- Core Values (Vertical list with high-end micro-animations) -->
                <div class="my-10 space-y-4">
                    <h3 class="font-display text-sm font-black text-slate-800 uppercase tracking-tight border-b border-slate-100 pb-3">Giá Trị Cốt Lõi</h3>
                    
                    <div class="flex gap-4 items-start py-3 group">
                        <span class="font-display text-lg font-black text-slate-300 group-hover:text-[#ff0000] transition-colors leading-none">01</span>
                        <div>
                            <h4 class="font-display text-xs sm:text-[13px] font-bold text-slate-800 uppercase tracking-wide mb-1">Chính Xác & Khách Quan</h4>
                            <p class="text-slate-500 text-xs sm:text-[13px]">Mọi bài viết đánh giá và tin tức đều được kiểm chứng độc lập bởi đội ngũ biên tập viên giàu kinh nghiệm.</p>
                        </div>
                    </div>

                    <div class="flex gap-4 items-start py-3 group border-t border-slate-100">
                        <span class="font-display text-lg font-black text-slate-300 group-hover:text-[#ff0000] transition-colors leading-none">02</span>
                        <div>
                            <h4 class="font-display text-xs sm:text-[13px] font-bold text-slate-800 uppercase tracking-wide mb-1">Chuyên Sâu & Giá Trị</h4>
                            <p class="text-slate-500 text-xs sm:text-[13px]">Đi sâu vào bản chất kỹ thuật, cung cấp các mã nguồn mẫu thực tế cùng các bài phân tích chuyên môn cực cao.</p>
                        </div>
                    </div>

                    <div class="flex gap-4 items-start py-3 group border-t border-slate-100">
                        <span class="font-display text-lg font-black text-slate-300 group-hover:text-[#ff0000] transition-colors leading-none">03</span>
                        <div>
                            <h4 class="font-display text-xs sm:text-[13px] font-bold text-slate-800 uppercase tracking-wide mb-1">Cộng Đồng Làm Trung Tâm</h4>
                            <p class="text-slate-500 text-xs sm:text-[13px]">Không ngừng lắng nghe phản hồi của độc giả để phát triển hệ sinh thái chia sẻ công nghệ lớn mạnh tại Việt Nam.</p>
                        </div>
                    </div>
                </div>

                <!-- Editorial Team: Ngô Văn Toàn (Sole Founder & Editor-in-Chief) -->
                <div class="my-10 pt-4">
                    <h3 class="font-display text-sm font-black text-slate-800 uppercase tracking-tight border-b border-slate-100 pb-3 mb-6">Đội ngũ chúng tôi</h3>
                    
                    <div class="border border-slate-100 p-6 sm:p-8 bg-slate-50/20 hover:border-[#ff0000]/30 transition-all duration-300 flex flex-col sm:flex-row gap-6 items-center">
                        <div class="w-24 h-24 sm:w-28 sm:h-28 bg-slate-100 border border-slate-200/50 flex items-center justify-center rounded-none overflow-hidden shrink-0 relative shadow-sm group">
                            <img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/images/IMG_6341.jpg" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Ngô Văn Toàn" />
                        </div>
                        <div class="flex-grow text-center sm:text-left min-w-0">
                            <h4 class="font-display text-lg font-black text-slate-900 uppercase tracking-wide">Ngô Văn Toàn</h4>
                            <span class="text-xs font-bold uppercase tracking-wider text-[#ff0000] block mt-1">Nhà sáng lập</span>
                            <p class="text-slate-500 text-xs sm:text-[13px] leading-relaxed mt-3">
                                Hi everyone! My name is Ngo Van Toan. I'm a frontend developer. I really enjoy what I do right now, in my opinion, creating programs is not just a job, but also an art that has aesthetic value.
                            </p>
                        </div>
                    </div>
                </div>
                
                
                <?php
            endwhile;
        endif;
        ?>
        
    </div>
</main>

<?php get_footer(); ?>
