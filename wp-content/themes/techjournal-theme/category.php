<?php
/**
 * The category template file
 *
 * Designed to show the custom title (Photo 3), custom Bento Hero Grid (Photo 2),
 * and the 3-column + sidebar post list with AJAX Load More (Photo 1).
 *
 * @package TechJournal
 * @since 1.0.0
 */

get_header();

$cat_id = get_queried_object_id();
$category = get_category($cat_id);
$paged = ( get_query_var( 'paged' ) ) ? intval( get_query_var( 'paged' ) ) : 1;
?>

<main class="py-8 bg-slate-50/50 min-h-screen">
    <div class="max-w-container-max mx-auto px-4 space-y-8">
        
        <!-- ================= CATEGORY TITLE & DESCRIPTION HEADER (Photo 3 Style) ================= -->
        <div class="py-8 border-b border-slate-200/80 mb-6 bg-transparent">
            <h1 class="font-display text-[32px] sm:text-[36px] font-black text-slate-900 tracking-tight mb-2 uppercase">
                <?php single_cat_title(); ?>
            </h1>
            <?php if ( category_description() ) : ?>
                <p class="text-slate-500 text-sm sm:text-base font-medium leading-relaxed max-w-3xl">
                    <?php echo strip_tags(category_description()); ?>
                </p>
            <?php else : ?>
                <p class="text-slate-500 text-sm sm:text-base font-medium leading-relaxed max-w-3xl">
                    Cập nhật tin tức về công nghệ thông tin và xu hướng <?php single_cat_title(); ?> nóng hổi nhất.
                </p>
            <?php endif; ?>
        </div>

        <!-- ================= BENTO HERO GRID SECTION (Photo 2 Style) - FLATTENED ================= -->
        <?php
        $hero_posts = array();
        
        $hero_query = new WP_Query( array(
            'cat'                 => $cat_id,
            'posts_per_page'      => 4,
            'post_type'           => 'post',
            'ignore_sticky_posts' => 1,
            'orderby'             => 'date',
            'order'               => 'DESC'
        ) );
        
        if ( $hero_query->found_posts >= 5 ) {
            $hero_posts = $hero_query->posts;
        }
        
        $post_count = count( $hero_posts );
        if ( $paged === 1 && $post_count > 0 ) :
            ?>
            <style>
            #bento-hero-grid::-webkit-scrollbar {
                display: none;
            }
            #bento-hero-grid {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
            </style>
            <section id="bento-hero-grid" class="flex flex-row overflow-x-auto md:grid md:grid-cols-2 gap-4 pb-4 select-none scrollbar-none scroll-smooth" aria-label="Bento Hero Grid">
                
                <!-- Left tall block: Spans 1 full height on desktop (Post 1) -->
                <?php 
                $post = $hero_posts[0];
                setup_postdata($post);
                ?>
                <article class="relative h-[320px] sm:h-[400px] md:h-[436px] overflow-hidden group cursor-pointer bg-slate-950 shrink-0 w-[80vw] md:w-auto">
                    <a href="<?php the_permalink(); ?>" class="absolute inset-0 block z-0">
                        <?php echo techblog_render_post_thumbnail( get_the_ID(), 'techjournal-hero', 'absolute inset-y-0 -left-[30px] w-[calc(100%+60px)] max-w-none h-full object-cover opacity-90 group-hover:translate-x-[30px] transition-transform duration-700', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent z-10"></div>
                    </a>
                    
                    <div class="absolute inset-0 p-5 sm:p-7 flex flex-col justify-end z-20 pointer-events-none max-w-[80%] sm:max-w-[80%]">
                        <span class="bg-red-600 text-white text-[10px] sm:text-[9px] font-black uppercase px-2.5 py-1 self-start tracking-widest shadow-sm mb-3">
                            <?php echo esc_html( techblog_get_post_category_name( get_the_ID() ) ); ?>
                        </span>
                        <h2 class="font-display text-base sm:text-lg md:text-xl text-white font-extrabold tracking-tight leading-snug pointer-events-auto mb-2 break-words">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        
                        <div class="max-h-0 opacity-0 overflow-hidden group-hover:max-h-12 group-hover:opacity-100 transition-all duration-300 ease-in-out pointer-events-auto">
                            <div class="flex items-center gap-2 text-xs sm:text-[10px] text-slate-300 font-bold uppercase tracking-wider mt-2">
                                <span>BY <?php the_author(); ?></span>
                                <span>•</span>
                                <span class="flex items-center gap-1">
                                    <?php echo techjournal_get_svg( 'clock', 'w-3.5 h-3.5 fill-current' ); ?> <?php echo get_the_date(); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </article>
                <?php wp_reset_postdata(); ?>

                <!-- Right Column: Flex stacked grid for Post 2, Post 3, and Post 4 -->
                <div class="flex flex-row md:flex-col gap-4 shrink-0">
                    
                    <!-- Top Half of the Right side: Post 2 -->
                    <?php 
                    if ($post_count >= 2) :
                        $post = $hero_posts[1];
                        setup_postdata($post);
                        ?>
                        <article class="relative h-[320px] sm:h-[192px] md:h-[210px] overflow-hidden group cursor-pointer bg-slate-950 shrink-0 w-[80vw] md:w-auto">
                            <a href="<?php the_permalink(); ?>" class="absolute inset-0 block z-0">
                                <?php echo techblog_render_post_thumbnail( get_the_ID(), 'techjournal-card', 'absolute inset-y-0 -left-[30px] w-[calc(100%+60px)] max-w-none h-full object-cover opacity-90 group-hover:translate-x-[30px] transition-transform duration-700' ); ?>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent z-10"></div>
                            </a>
                            <div class="absolute inset-0 p-5 flex flex-col justify-end z-20 pointer-events-none max-w-[80%] sm:max-w-[80%]">
                                <span class="bg-red-600 text-white text-[10px] sm:text-[8px] font-black uppercase px-2 py-0.5 self-start tracking-widest shadow-sm mb-2">
                                    <?php echo esc_html( techblog_get_post_category_name( get_the_ID() ) ); ?>
                                </span>
                                <h3 class="font-display text-sm md:text-base text-white font-extrabold tracking-tight leading-snug pointer-events-auto break-words">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                
                                <div class="max-h-0 opacity-0 overflow-hidden group-hover:max-h-12 group-hover:opacity-100 transition-all duration-300 ease-in-out pointer-events-auto">
                                    <div class="flex items-center gap-1.5 text-xs sm:text-[10px] text-slate-300 font-bold uppercase tracking-wider mt-2">
                                        <?php echo techjournal_get_svg( 'clock', 'w-3.5 h-3.5 fill-current' ); ?>
                                        <span><?php echo get_the_date(); ?></span>
                                    </div>
                                </div>
                            </div>
                        </article>
                        <?php wp_reset_postdata(); ?>
                    <?php endif; ?>
 
                    <!-- Bottom Half of the Right side: Post 3 & Post 4 split split screen layout -->
                    <div class="flex flex-row md:grid md:grid-cols-2 gap-4 shrink-0">
                        
                        <!-- Post 3 -->
                        <?php 
                        if ($post_count >= 3) :
                            $post = $hero_posts[2];
                            setup_postdata($post);
                            ?>
                            <article class="relative h-[320px] sm:h-[192px] md:h-[210px] overflow-hidden group cursor-pointer bg-slate-950 shrink-0 w-[80vw] md:w-auto">
                                <a href="<?php the_permalink(); ?>" class="absolute inset-0 block z-0">
                                    <?php echo techblog_render_post_thumbnail( get_the_ID(), 'techjournal-card', 'absolute inset-y-0 -left-[30px] w-[calc(100%+60px)] max-w-none h-full object-cover opacity-90 group-hover:translate-x-[30px] transition-transform duration-700' ); ?>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent z-10"></div>
                                </a>
                                <div class="absolute inset-0 p-4 flex flex-col justify-end z-20 pointer-events-none max-w-[80%] sm:max-w-[80%]">
                                    <span class="bg-red-600 text-white text-[10px] sm:text-[8px] font-black uppercase px-2 py-0.5 self-start tracking-widest shadow-sm mb-2">
                                        <?php echo esc_html( techblog_get_post_category_name( get_the_ID() ) ); ?>
                                    </span>
                                    <h3 class="font-display text-sm sm:text-[13px] md:text-[14px] text-white font-extrabold tracking-tight leading-snug pointer-events-auto break-words">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    
                                    <div class="max-h-0 opacity-0 overflow-hidden group-hover:max-h-12 group-hover:opacity-100 transition-all duration-300 ease-in-out pointer-events-auto">
                                        <div class="flex items-center gap-1.5 text-xs sm:text-[10px] text-slate-300 font-bold uppercase tracking-wider mt-2">
                                            <?php echo techjournal_get_svg( 'clock', 'w-3.5 h-3.5 fill-current' ); ?>
                                            <span><?php echo get_the_date(); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                            <?php wp_reset_postdata(); ?>
                        <?php endif; ?>
 
                        <!-- Post 4 -->
                        <?php 
                        if ($post_count >= 4) :
                            $post = $hero_posts[3];
                            setup_postdata($post);
                            ?>
                            <article class="relative h-[320px] sm:h-[192px] md:h-[210px] overflow-hidden group cursor-pointer bg-slate-950 shrink-0 w-[80vw] md:w-auto">
                                <a href="<?php the_permalink(); ?>" class="absolute inset-0 block z-0">
                                    <?php echo techblog_render_post_thumbnail( get_the_ID(), 'techjournal-card', 'absolute inset-y-0 -left-[30px] w-[calc(100%+60px)] max-w-none h-full object-cover opacity-90 group-hover:translate-x-[30px] transition-transform duration-700' ); ?>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent z-10"></div>
                                </a>
                                <div class="absolute inset-0 p-4 flex flex-col justify-end z-20 pointer-events-none max-w-[80%] sm:max-w-[80%]">
                                    <span class="bg-red-600 text-white text-[10px] sm:text-[8px] font-black uppercase px-2 py-0.5 self-start tracking-widest shadow-sm mb-2">
                                        <?php echo esc_html( techblog_get_post_category_name( get_the_ID() ) ); ?>
                                    </span>
                                    <h3 class="font-display text-sm sm:text-[13px] md:text-[14px] text-white font-extrabold tracking-tight leading-snug pointer-events-auto">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    
                                    <div class="max-h-0 opacity-0 overflow-hidden group-hover:max-h-12 group-hover:opacity-100 transition-all duration-300 ease-in-out pointer-events-auto">
                                        <div class="flex items-center gap-1.5 text-xs sm:text-[10px] text-slate-300 font-bold uppercase tracking-wider mt-2">
                                            <?php echo techjournal_get_svg( 'clock', 'w-3.5 h-3.5 fill-current' ); ?>
                                            <span><?php echo get_the_date(); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                            <?php wp_reset_postdata(); ?>
                        <?php endif; ?>

                    </div>
                </div>
            </section>
            <?php
        endif;
        ?>

        <!-- ================= MAIN BODY LAYOUT (TechBlog Style) ================= -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-y-8 gap-x-0 md:gap-10 items-start">
            
            <!-- Left: Articles list grid (lg:col-span-8 to align with homepage) -->
            <div class="col-span-1 md:col-span-8 space-y-6 min-w-0">
                
                <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-3 relative">
                    <div class="flex items-center gap-1.5 relative">
                        <?php echo techjournal_get_svg( 'local_fire_department', 'w-5 h-5 text-primary fill-current' ); ?>
                        <h2 class="font-display text-[16px] font-black text-slate-800 uppercase tracking-tight">
                            Danh sách bài viết
                        </h2>
                        <div class="absolute bottom-[-13px] left-0 right-0 h-[3px] bg-primary"></div>
                    </div>
                </div>
                
                <div id="techblog-post-grid" class="flex flex-col gap-6">
                    <?php
                    $exclude_ids = wp_list_pluck( $hero_posts, 'ID' );
                    $latest_query = new WP_Query( array(
                        'post_type'      => 'post',
                        'cat'            => $cat_id,
                        'posts_per_page' => 9,
                        'paged'          => $paged,
                        'post__not_in'   => $exclude_ids,
                        'ignore_sticky_posts' => 1,
                        'order'          => 'DESC'
                    ) );

                    if ( $latest_query->have_posts() ) :
                        while ( $latest_query->have_posts() ) : $latest_query->the_post();
                            get_template_part( 'template-parts/content-card' );
                        endwhile;
                        wp_reset_postdata();
                    else :
                        echo '<p class="text-slate-500 text-center py-12 bg-white border border-slate-100 w-full">Chưa có bài viết mới.</p>';
                    endif;
                    ?>
                </div>

                <!-- Custom Pagination -->
                <?php
                if ( function_exists( 'techjournal_pagination' ) ) {
                    techjournal_pagination( $latest_query );
                }
                ?>


            </div>
            
            <!-- Right: TechBlog Sidebar (lg:col-span-4 to align with homepage) -->
            <aside class="col-span-1 md:col-span-4 grid grid-cols-1 gap-10 min-w-0">

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
                                                     <?php echo get_the_date( 'd/m/Y' ); ?>
                                                 </span>
                                                 <?php if ( get_the_modified_time( 'U' ) > get_the_time( 'U' ) + 60 ) : ?>
                                                     <span>•</span>
                                                     <span>update <?php echo get_the_modified_date( 'd/m/Y' ); ?></span>
                                                 <?php endif; ?>
                                             </div>
                                             <!-- Desktop Meta -->
                                             <div class="hidden md:flex items-center flex-wrap gap-x-2 gap-y-1 text-[11px] text-slate-400 font-normal">
                                                 <span>BY <span class="font-bold text-primary uppercase"><?php echo (strcasecmp(get_the_author(), 'admin') === 0) ? 'Admin TechBlog' : get_the_author(); ?></span></span>
                                                 <span class="inline-flex items-center gap-1">
                                                     <?php echo techjournal_get_svg( 'clock', 'w-3.5 h-3.5 text-slate-400 fill-current' ); ?>
                                                     <span><?php echo get_the_date( 'd/m/Y' ); ?></span>
                                                 </span>
                                                 <?php if ( get_the_modified_time( 'U' ) > get_the_time( 'U' ) + 60 ) : ?>
                                                     <span>•</span>
                                                     <span>update <?php echo get_the_modified_date( 'd/m/Y' ); ?></span>
                                                 <?php endif; ?>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bento Hero Grid Drag/Swipe capability (Mouse hold drag + Touch swipe)
    const bentoGrid = document.getElementById('bento-hero-grid');
    if (bentoGrid) {
        let isDown = false;
        let startX;
        let scrollLeft;
        let dragDistance = 0;
        let clickPrevented = false;

        function updateCursor() {
            if (bentoGrid.scrollWidth > bentoGrid.clientWidth) {
                bentoGrid.style.cursor = 'grab';
            } else {
                bentoGrid.style.cursor = 'default';
            }
        }

        window.addEventListener('resize', updateCursor);
        updateCursor();

        bentoGrid.addEventListener('mousedown', (e) => {
            if (bentoGrid.scrollWidth <= bentoGrid.clientWidth) return;
            isDown = true;
            startX = e.pageX - bentoGrid.offsetLeft;
            scrollLeft = bentoGrid.scrollLeft;
            dragDistance = 0;
            clickPrevented = false;
            bentoGrid.style.cursor = 'grabbing';
        });

        bentoGrid.addEventListener('mouseleave', () => {
            if (!isDown) return;
            isDown = false;
            updateCursor();
        });

        bentoGrid.addEventListener('mouseup', () => {
            if (!isDown) return;
            isDown = false;
            updateCursor();
        });

        bentoGrid.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - bentoGrid.offsetLeft;
            const walk = (x - startX) * 1.5; // adjust scroll speed multiplier
            bentoGrid.scrollLeft = scrollLeft - walk;
            dragDistance = Math.abs(x - startX);
            if (dragDistance > 10) {
                clickPrevented = true;
            }
        });

        // Prevent navigation if dragged
        bentoGrid.addEventListener('click', (e) => {
            if (clickPrevented) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, true);
    }
});
</script>

<?php
get_footer();
