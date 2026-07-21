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

                <!-- Load More Button Block - Flattened -->
                <?php 
                global $wp_query;
                if ( have_posts() && $wp_query->found_posts > 9 ) : 
                ?>
                <div class="text-center mt-10">
                    <button id="techblog-load-more-btn" 
                            data-page="1" 
                            data-post-type="post" 
                            data-cat-id="0"
                            data-search="<?php echo esc_attr($search_query); ?>"
                            class="bg-primary hover:bg-primary/95 text-white font-bold px-8 py-3 text-[11px] uppercase tracking-wider transition-all duration-300 shadow-md hover:shadow-lg active:scale-95 cursor-pointer inline-flex items-center gap-2">
                        <span>XEM THÊM BÀI VIẾT</span>
                        <span id="load-more-spinner" class="animate-spin hidden">
                            <?php echo techjournal_get_svg( 'sync', 'w-4 h-4 fill-current' ); ?>
                        </span>
                    </button>
                </div>
                <?php endif; ?>

            </div>
            
            <!-- Right: TechBlog Sidebar (lg:col-span-4 to align with homepage) -->
            <aside class="col-span-12 lg:col-span-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-6">

                <!-- Sidebar: BÀI VIẾT NỔI BẬT (Pinned/Sticky system) -->
                <div class="bg-white border border-slate-100 p-6 shadow-sm">
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
                                    <div class="flex gap-4 items-center group/item py-3.5 border-b border-slate-100/50 last:border-0">
                                        <div class="font-display text-2xl font-black text-slate-700 group-hover/item:text-primary transition-colors w-10 shrink-0 text-left tracking-tighter">
                                            <?php echo sprintf('%02d', $rank); ?>
                                        </div>
                                        
                                        <?php 
                                        $thumb_url = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
                                        if (!$thumb_url) {
                                            $thumb_url = techblog_get_placeholder_img();
                                        }
                                        ?>
                                        <a href="<?php the_permalink(); ?>" class="w-24 aspect-[16/9] overflow-hidden shrink-0 bg-slate-100 shadow-sm block relative">
                                            <img src="<?php echo esc_url($thumb_url); ?>" class="w-full h-full object-cover group-hover/item:scale-105 transition-transform duration-500" alt="<?php the_title_attribute(); ?>" />
                                        </a>
                                        
                                        <div class="flex-grow min-w-0">
                                            <h4 class="font-display text-[12.5px] font-bold text-slate-800 group-hover/item:text-primary transition-colors leading-snug break-words">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h4>
                                            <div class="flex items-center gap-3 text-[9px] text-slate-400 mt-1.5 font-bold uppercase tracking-wider">
                                                <span class="flex items-center gap-0.5">
                                                    <?php echo techjournal_get_svg( 'calendar', 'w-3.5 h-3.5 text-slate-400 fill-current' ); ?>
                                                    <?php echo get_the_date(); ?>
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
