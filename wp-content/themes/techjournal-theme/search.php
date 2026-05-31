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
        <div class="grid grid-cols-12 gap-6 items-start">
            
            <!-- Left: Search results grid (lg:col-span-8 to align with homepage) -->
            <div class="col-span-12 lg:col-span-8 space-y-6">
                
                <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-3 relative">
                    <div class="flex items-center gap-1.5 relative">
                        <span class="material-symbols-outlined text-[#ff0000] text-[20px] font-bold">search</span>
                        <h2 class="font-display text-[16px] font-black text-slate-800 uppercase tracking-tight">
                            Kết quả phù hợp
                        </h2>
                        <div class="absolute bottom-[-13px] left-0 right-0 h-[3px] bg-[#ff0000]"></div>
                    </div>
                </div>
                
                <div id="techblog-post-grid" class="flex flex-col gap-6">
                    <?php
                    if ( have_posts() ) :
                        while ( have_posts() ) : the_post();
                            $post_image = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
                            if ( ! $post_image ) {
                                $post_image = techblog_get_placeholder_img();
                            }
                            
                            $cats = get_the_category();
                            $category_to_show = null;
                            if ( ! empty( $cats ) ) {
                                foreach($cats as $c) {
                                    if($c->term_id != get_option('default_category')) {
                                        $category_to_show = $c;
                                        break;
                                    }
                                }
                                if (!$category_to_show) {
                                    $category_to_show = $cats[0];
                                }
                            }
                            ?>
                            <article class="group bg-white flex flex-col md:flex-row gap-6 cursor-pointer transition-all duration-300 border-b border-slate-100/80 last:border-0">
                                <!-- Image container on Left -->
                                <a href="<?php the_permalink(); ?>" class="w-full md:w-[320px] aspect-[16/10] sm:aspect-[16/9] md:aspect-[1.5/1] overflow-hidden block relative bg-slate-950 shrink-0">
                                    <img class="w-full h-full object-cover transition-transform duration-750 group-hover:scale-102 opacity-95 group-hover:opacity-100" src="<?php echo esc_url($post_image); ?>" alt="<?php the_title_attribute(); ?>" />
                                    <?php if ($category_to_show) : ?>
                                        <span class="absolute top-3 left-3 bg-[#ff0000] text-white text-[9px] font-black uppercase px-2.5 py-1 tracking-widest shadow-sm">
                                            <?php echo esc_html($category_to_show->name); ?>
                                        </span>
                                    <?php endif; ?>
                                </a>
                                
                                <!-- Text Content on Right -->
                                <div class="flex flex-col flex-grow min-w-0 pt-1 md:pt-2 md:pl-2 pr-4 md:pr-6">
                                    <h3 class="font-display text-base sm:text-lg md:text-[20px] text-slate-800 hover:text-primary transition-colors font-bold leading-snug mb-3 break-words">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    
                                    <!-- Meta Info Line -->
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5 text-[10px] sm:text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-4">
                                        <span>BY <span class="text-[#ff0000] font-black"><?php echo (strcasecmp(get_the_author(), 'admin') === 0) ? 'Admin TechBlog' : get_the_author(); ?></span></span>
                                        <span>•</span>
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[13px] text-blue-500">schedule</span>
                                            <?php echo get_the_date('d/m/Y'); ?>
                                        </span>
                                        <span>•</span>
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[13px] text-blue-500">comment</span>
                                            <?php echo get_comments_number(); ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Excerpt -->
                                    <p class="text-slate-500 text-xs sm:text-sm leading-relaxed mb-6">
                                        <?php echo wp_trim_words( get_the_excerpt(), 25, '...' ); ?>
                                    </p>
                                    
                                    <!-- READ MORE Button -->
                                    <a href="<?php the_permalink(); ?>" class="border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300 text-[10px] font-bold uppercase tracking-wider text-slate-600 px-5 py-2 w-fit active:scale-95 transition-all mt-1">
                                        READ MORE
                                    </a>
                                </div>
                            </article>
                            <?php
                        endwhile;
                    else :
                        echo '<div class="w-full text-center py-16 bg-white border border-slate-100 shadow-sm col-span-full">
                            <span class="material-symbols-outlined text-[48px] text-slate-300 mb-3">search_off</span>
                            <p class="text-slate-500 text-sm leading-relaxed">Không tìm thấy bài viết nào phù hợp với từ khóa của bạn.</p>
                        </div>';
                    endif;
                    ?>
                </div>

                <!-- Load More Button Block - Flattened -->
                <div class="text-center mt-10">
                    <button id="techblog-load-more-btn" 
                            data-page="1" 
                            data-post-type="post" 
                            data-cat-id="0"
                            data-search="<?php echo esc_attr($search_query); ?>"
                            class="bg-[#ff0000] hover:bg-[#cc0000] text-white font-bold px-8 py-3 text-[11px] uppercase tracking-wider transition-all duration-300 shadow-md hover:shadow-lg active:scale-95 cursor-pointer inline-flex items-center gap-2">
                        <span>XEM THÊM BÀI VIẾT</span>
                        <span class="material-symbols-outlined text-[16px] animate-spin hidden" id="load-more-spinner">sync</span>
                    </button>
                </div>

            </div>
            
            <!-- Right: TechBlog Sidebar (lg:col-span-4 to align with homepage) -->
            <aside class="col-span-12 lg:col-span-4 space-y-6">

                <!-- Sidebar: BÀI VIẾT NỔI BẬT (Strictly Pinned/Sticky system) -->
                <?php 
                $sticky_ids = get_option( 'sticky_posts' );
                if ( ! empty( $sticky_ids ) ) :
                    $sidebar_query = new WP_Query( array(
                        'post_type'           => 'post',
                        'post__in'            => $sticky_ids,
                        'posts_per_page'      => 5,
                        'ignore_sticky_posts' => 1,
                    ) );
                    if ( $sidebar_query->have_posts() ) :
                ?>
                    <div class="bg-white border border-slate-100 p-6 shadow-sm">
                        <h4 class="font-display text-sm font-black text-slate-800 uppercase tracking-tight mb-5 border-b border-slate-200 pb-3 relative anony-section-title">Bài Viết Nổi Bật</h4>
                        <div class="space-y-4">
                            <?php
                            $rank = 1;
                            while ( $sidebar_query->have_posts() ) : $sidebar_query->the_post();
                            ?>
                                <div class="flex gap-4 items-center group/item py-3.5 border-b border-slate-100/50 last:border-0">
                                    <div class="font-display text-2xl font-black text-slate-200 group-hover/item:text-primary transition-colors w-10 shrink-0 text-left tracking-tighter">
                                        <?php echo sprintf('%02d', $rank); ?>
                                    </div>
                                    
                                    <?php 
                                    $thumb_url = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
                                    if (!$thumb_url) {
                                        $thumb_url = techblog_get_placeholder_img();
                                    }
                                    ?>
                                    <a href="<?php the_permalink(); ?>" class="w-14 h-14 overflow-hidden shrink-0 bg-slate-100 shadow-sm block relative">
                                        <img src="<?php echo esc_url($thumb_url); ?>" class="w-full h-full object-cover group-hover/item:scale-105 transition-transform duration-500" alt="<?php the_title_attribute(); ?>" />
                                    </a>
                                    
                                    <div class="flex-grow min-w-0">
                                        <h5 class="font-display text-[12.5px] font-bold text-slate-800 group-hover/item:text-primary transition-colors leading-snug break-words">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h5>
                                        <div class="flex items-center gap-3 text-[9px] text-slate-400 mt-1.5 font-bold uppercase tracking-wider">
                                            <span class="flex items-center gap-0.5">
                                                <span class="material-symbols-outlined text-[11px]">calendar_today</span>
                                                <?php echo get_the_date(); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php
                                $rank++;
                            endwhile;
                            wp_reset_postdata();
                            ?>
                        </div>
                    </div>
                <?php 
                    endif;
                endif; 
                ?>
            </aside>
            
        </div>
        
    </div>
</main>

<?php
get_footer();
