<?php
/**
 * The search results template file - TechBlog Style
 *
 * Designed to show the custom title (Photo 4), flat article grid (Photo 1),
 * and the 3-column + sidebar post list with AJAX Load More.
 *
 * @package TechJournal
 * @since 1.0.0
 */

get_header();

$search_query = get_search_query();
?>

<main class="py-8 bg-slate-50/50 min-h-screen">
    <div class="max-w-container-max mx-auto px-4 space-y-8">
        
        <!-- ================= SEARCH RESULTS HEADER (Photo 4 Style) ================= -->
        <div class="py-8 border-b border-slate-200/80 mb-6 bg-transparent">
            <h1 class="font-display text-[28px] sm:text-[32px] font-black text-slate-900 tracking-tight mb-2 uppercase">
                KẾT QUẢ TÌM KIẾM CHO: "<?php echo esc_html($search_query); ?>"
            </h1>
            <p class="text-slate-500 text-sm sm:text-base font-medium leading-relaxed max-w-3xl">
                Tìm thấy <?php global $wp_query; echo $wp_query->found_posts; ?> bài viết phù hợp với yêu cầu tìm kiếm của bạn.
            </p>
        </div>

        <!-- ================= MAIN BODY LAYOUT (TechBlog Style) ================= -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-y-8 gap-x-0 md:gap-10 items-start">
            
            <!-- Left: Search results grid (lg:col-span-8 to align with homepage) -->
            <div class="col-span-1 md:col-span-8 space-y-6 min-w-0">
                
                <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-3 relative">
                    <div class="flex items-center gap-1.5 relative">
                        <?php echo techjournal_get_svg( 'search', 'w-[20px] h-[20px] text-primary' ); ?>
                        <h2 class="font-display text-[16px] font-black text-slate-800 uppercase tracking-tight">
                            Kết quả phù hợp
                        </h2>
                        <div class="absolute bottom-[-13px] left-0 right-0 h-[3px] bg-primary"></div>
                    </div>
                </div>
                
                <div id="techblog-post-grid" class="flex flex-col gap-6">
                    <?php
                    if ( have_posts() ) :
                        while ( have_posts() ) : the_post();
                            get_template_part( 'template-parts/content-card' );
                        endwhile;
                    else :
                        echo '<div class="w-full text-center py-16 bg-white border border-slate-100 shadow-sm col-span-full">
                            ' . techjournal_get_svg( 'search_off', 'w-12 h-12 text-slate-300 mb-3 mx-auto block fill-current' ) . '
                            <p class="text-slate-500 text-sm leading-relaxed">Không tìm thấy bài viết nào phù hợp với từ khóa của bạn.</p>
                        </div>';
                    endif;
                    ?>
                </div>

                <!-- Custom Pagination -->
                <?php
                if ( function_exists( 'techjournal_pagination' ) ) {
                    techjournal_pagination();
                }
                ?>


            </div>
            
            <!-- Right: TechBlog Sidebar (lg:col-span-4 to align with homepage) -->
            <aside class="col-span-1 md:col-span-4 grid grid-cols-1 gap-10 min-w-0 p-5 md:p-0">

                <!-- Sidebar: BÀI VIẾT NỔI BẬT (Pinned/Sticky system) -->
                <div class=" ">
                    <h3 class="font-display text-sm font-black text-slate-800 uppercase tracking-tight mb-5 border-b border-slate-200 pb-3 relative anony-section-title">Bài Viết Nổi Bật</h3>
                    <div class="space-y-4">
                        <?php 
                        $sticky_ids = get_option( 'sticky_posts' );
                        $has_featured = false;
                        if ( ! empty( $sticky_ids ) ) :
                            $sidebar_query = new WP_Query( array(
                                'post_type'           => 'post',
                                'post__in'            => $sticky_ids,
                                'posts_per_page'      => 5,
                                'ignore_sticky_posts' => 1,
                            ) );
                            if ( $sidebar_query->have_posts() ) :
                                $has_featured = true;
                                $rank = 1;
                                while ( $sidebar_query->have_posts() ) : $sidebar_query->the_post();
                                ?>
                                    <div class="flex gap-3 items-start group/item py-3.5 border-b border-slate-100/50 last:border-0">
                                        <a href="<?php the_permalink(); ?>" class="w-28 sm:w-32 md:w-24 lg:w-28 aspect-[16/10] md:aspect-[4/3] overflow-hidden shrink-0 bg-slate-100 shadow-sm block relative">
                                            <?php echo techblog_render_post_thumbnail( get_the_ID(), 'techjournal-thumb', 'w-full h-full object-cover group-hover/item:scale-105 transition-transform duration-500' ); ?>
                                        </a>
                                        <div class="flex-grow min-w-0 w-full">
                                            <h4 class="font-display text-xs sm:text-[12.5px] md:text-[13px] font-bold md:font-medium text-slate-800 group-hover/item:text-primary transition-colors leading-snug break-words mb-1 md:mb-1.5">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h4>
                                            <!-- Mobile Excerpt -->
                                            <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 md:hidden leading-normal">
                                                <?php echo esc_html( wp_trim_words( get_the_excerpt(), 12, '...' ) ); ?>
                                            </p>
                                            <!-- Mobile Meta -->
                                            <div class="flex items-center flex-wrap gap-2 text-[11px] sm:text-[9px] text-slate-400 mt-1.5 font-bold uppercase tracking-wider md:hidden">
                                                <span>BY <span class="font-bold text-primary uppercase"><?php echo (strcasecmp(get_the_author(), 'admin') === 0) ? 'Admin TechBlog' : get_the_author(); ?></span></span>
                                                <span>•</span>
                                                <span class="flex items-center gap-0.5">
                                                    <?php echo techjournal_get_svg( 'clock', 'w-3.5 h-3.5 text-slate-400 fill-current' ); ?>
                                                    <?php echo get_the_date(); ?>
                                                </span>
                                            </div>
                                            <!-- Desktop Meta -->
                                            <div class="hidden md:flex items-center flex-wrap gap-x-2 gap-y-1 text-[11px] text-slate-400 font-normal">
                                                <span>BY <span class="font-bold text-primary uppercase"><?php echo (strcasecmp(get_the_author(), 'admin') === 0) ? 'Admin TechBlog' : get_the_author(); ?></span></span>
                                                <span class="inline-flex items-center gap-1">
                                                    <?php echo techjournal_get_svg( 'clock', 'w-3.5 h-3.5 text-slate-400 fill-current' ); ?>
                                                    <span><?php echo get_the_date( 'd/m/Y' ); ?></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                    $rank++;
                                endwhile;
                                wp_reset_postdata();
                            endif;
                        endif; 

                        if ( ! $has_featured ) :
                            echo '<p class="text-slate-500 text-xs py-4 text-center font-medium">Chưa có bài viết nổi bật.</p>';
                        endif;
                        ?>
                    </div>
                </div>
            </aside>
            
        </div>
        
    </div>
</main>

<?php
get_footer();
