<?php
/**
 * The front-page template file - TechBlog Style
 *
 * @package TechJournal
 * @since 1.0.0
 */

get_header(); ?>

<main class="pt-6 sm:pt-8 pb-section-gap bg-background min-h-screen">
    <div class="max-w-container-max mx-auto px-4">
        
        <!-- Unified Premium Layout Grid (Resolves all structural gaps and alignment shifts) -->
        <div class="grid grid-cols-12 gap-6 items-start">
            
            <!-- Left Column: Content Section (Newsflash, Hero Slider, Thumbnails, and Article List) -->
            <div class="col-span-12 lg:col-span-8 space-y-8">
                
                <!-- NEWSFLASH Ticker Bar -->
                <div class="bg-white border border-slate-100 flex items-center justify-between h-10 px-4 overflow-hidden gap-4 shadow-sm">
                    <div class="flex items-center gap-2 flex-grow min-w-0">
                        <span class="bg-[#ff0000] text-white text-[10px] font-black uppercase px-2.5 py-1.5 flex items-center gap-1 shrink-0 tracking-wider">
                            <span class="material-symbols-outlined text-[12px]">bolt</span> NEWSFLASH
                        </span>
                        <div class="relative h-6 flex-grow overflow-hidden font-bold text-[12px] text-slate-700 select-none">
                            <div id="homepage-newsflash-track" class="absolute w-full transition-all duration-500 ease-in-out left-0" style="top: 0px;">
                                <?php 
                                $nf_posts = get_posts( array( 'post_type' => 'post', 'posts_per_page' => 5 ) );
                                foreach ( $nf_posts as $idx => $nf_post ) : 
                                ?>
                                    <div class="h-6 flex items-center justify-between gap-4 w-full" data-index="<?php echo $idx; ?>">
                                        <a href="<?php echo get_permalink($nf_post->ID); ?>" class="hover:text-primary transition-colors truncate min-w-0 flex-grow">
                                            <?php echo esc_html($nf_post->post_title); ?>
                                        </a>
                                        <span class="text-[10px] text-slate-400 shrink-0 font-medium ml-auto">
                                            <?php echo human_time_diff( get_post_time('U', false, $nf_post), current_time('timestamp') ) . ' trước'; ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button id="nf-prev-btn" class="w-6 h-6 border border-slate-200 text-slate-400 hover:text-slate-700 hover:border-slate-300 flex items-center justify-center cursor-pointer active:scale-95 transition-all">
                            <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                        </button>
                        <button id="nf-next-btn" class="w-6 h-6 border border-slate-200 text-slate-400 hover:text-slate-700 hover:border-slate-300 flex items-center justify-center cursor-pointer active:scale-95 transition-all">
                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                        </button>
                    </div>
                </div>

                <!-- Main Hero Slider -->
                <?php 
                $slider_posts = get_posts( array( 'post_type' => 'post', 'posts_per_page' => 7 ) );
                $slide_count = count( $slider_posts );
                if ( $slide_count === 0 ) $slide_count = 1;
                $track_width = $slide_count * 100;
                $slide_width = 100 / $slide_count;
                ?>
                <div class="relative w-full aspect-[4/3] sm:aspect-[16/10] bg-slate-900 overflow-hidden group/hero-slider border border-slate-100 shadow-sm">
                    <div class="w-full h-full relative overflow-hidden" id="hero-slider-wrapper">
                        <div class="flex h-full transition-transform duration-700 ease-[cubic-bezier(0.25,1,0.5,1)]" id="hero-slider-track" style="width: <?php echo $track_width; ?>%; transform: translateX(0%);">
                            <?php 
                            foreach ( $slider_posts as $idx => $sp ) : 
                                $sp_img = get_the_post_thumbnail_url($sp->ID, 'large');
                                if ( !$sp_img ) $sp_img = get_the_post_thumbnail_url($sp->ID, 'medium_large');
                                if ( !$sp_img ) $sp_img = get_the_post_thumbnail_url($sp->ID, 'full');
                                if ( !$sp_img ) $sp_img = techblog_get_placeholder_img();
                                $sp_cats = get_the_category($sp->ID);
                                $sp_cat_name = !empty($sp_cats) ? $sp_cats[0]->name : 'Tin tức';
                            ?>
                                <div class="h-full shrink-0 relative hero-slide" style="width: <?php echo $slide_width; ?>%;" data-slide-index="<?php echo $idx; ?>">
                                    <a href="<?php echo get_permalink($sp->ID); ?>" class="absolute inset-0 block">
                                        <img src="<?php echo esc_url($sp_img); ?>" class="w-full h-full object-cover transform scale-100 group-hover/hero-slider:scale-102 transition-transform duration-[6000ms] ease-out" alt="<?php echo esc_attr($sp->post_title); ?>" />
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent"></div>
                                        
                                        <div class="absolute inset-x-0 bottom-0 p-5 sm:p-8 flex flex-col justify-end text-white max-w-[80%] sm:max-w-[80%] space-y-2 pointer-events-none">
                                            <span class="bg-[#ff0000] text-white text-[9px] font-black uppercase px-2.5 py-1 self-start tracking-widest shadow-sm">
                                                <?php echo esc_html($sp_cat_name); ?>
                                            </span>
                                            <h2 class="font-display text-base sm:text-xl md:text-2xl font-extrabold tracking-tight leading-snug drop-shadow-sm break-words">
                                                <?php echo esc_html($sp->post_title); ?>
                                            </h2>
                                            <div class="flex items-center gap-2 text-[10px] text-slate-300 font-bold uppercase tracking-wider">
                                                <span>BY Admin TechBlog</span>
                                                <span>•</span>
                                                <span><?php echo get_the_date('d/m/Y', $sp->ID); ?></span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Controls on the bottom-right of the slider (Xanh chủ đạo thay vì màu đỏ) -->
                    <div class="absolute bottom-0 right-0 z-20 flex bg-primary h-10 select-none items-center">
                        <button id="hero-prev-btn" class="w-10 h-full hover:bg-black/20 text-white flex items-center justify-center cursor-pointer transition-colors border-r border-white/10 active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        </button>
                        <button id="hero-next-btn" class="w-10 h-full hover:bg-black/20 text-white flex items-center justify-center cursor-pointer transition-colors active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </button>
                    </div>
                </div>

                <!-- Thumbnail indicators horizontally underneath the slider -->
                <div class="hidden sm:grid gap-2" style="grid-template-columns: repeat(7, minmax(0, 1fr));">
                        <?php 
                        foreach ( $slider_posts as $idx => $sp ) : 
                            $sp_thumb = get_the_post_thumbnail_url($sp->ID, 'thumbnail');
                            if ( !$sp_thumb ) $sp_thumb = techblog_get_placeholder_img();
                        ?>
                        <div class="hero-thumb-btn aspect-[16/10] cursor-pointer transition-all relative group" data-thumb-index="<?php echo $idx; ?>">
                                <!-- Small upward triangle indicator placed above the border, visible only when active -->
                                <div class="absolute -top-2 left-1/2 -translate-x-1/2 w-0 h-0 border-l-[6px] border-l-transparent border-r-[6px] border-r-transparent border-b-[6px] border-b-primary transition-opacity duration-300 pointer-events-none indicator-triangle <?php echo $idx === 0 ? '' : 'opacity-0'; ?>"></div>
                                <div class="w-full h-full overflow-hidden relative">
                                    <img src="<?php echo esc_url($sp_thumb); ?>" class="w-full h-full object-cover" alt="" />
                                    <!-- Dark overlay, active = opacity-100 (make image a bit darker) -->
                                    <div class="absolute inset-0 bg-black/30 pointer-events-none transition-opacity duration-300 active-overlay <?php echo $idx === 0 ? 'opacity-100' : 'opacity-0'; ?>"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <!-- ================= ARTICLES LIST SECTION ================= -->
                <div class="space-y-6 pt-4">
                    <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-3 relative">
                        <div class="flex items-center gap-1.5 relative">
                            <span class="material-symbols-outlined text-primary text-[20px] font-bold">local_fire_department</span>
                            <h2 class="font-display text-[16px] font-black text-slate-800 uppercase tracking-tight">
                                Bài viết mới
                            </h2>
                            <div class="absolute bottom-[-13px] left-0 right-0 h-[3px] bg-primary"></div>
                        </div>
                    </div>
                    
                    <div id="techblog-post-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        <?php
                        $latest_query = new WP_Query( array(
                            'post_type'           => 'post',
                            'posts_per_page'      => 9,
                            'ignore_sticky_posts' => true,
                            'orderby'             => 'date',
                            'order'               => 'DESC'
                        ) );

                        if ( $latest_query->have_posts() ) :
                            while ( $latest_query->have_posts() ) : $latest_query->the_post();
                                get_template_part( 'template-parts/content-grid' );
                            endwhile;
                            wp_reset_postdata();
                        else :
                            echo '<p class="text-slate-500 col-span-3 text-center py-12 bg-white border border-slate-100">Chưa có bài viết mới.</p>';
                        endif;
                        ?>
                    </div>

                    <!-- Load More Button Block -->
                    <div class="text-center mt-10">
                        <button id="techblog-load-more-btn" 
                                data-page="1" 
                                data-post-type="post" 
                                data-cat-id="0"
                                data-search=""
                                class="bg-primary hover:bg-primary/95 text-white font-bold px-8 py-3 text-[11px] uppercase tracking-wider transition-all duration-300 shadow-md hover:shadow-lg active:scale-95 cursor-pointer inline-flex items-center gap-2">
                            <span>XEM THÊM BÀI VIẾT</span>
                            <span class="material-symbols-outlined text-[16px] animate-spin hidden" id="load-more-spinner">sync</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Right Column: Sidebar (Unified vertically without gaps) -->
            <aside class="col-span-12 lg:col-span-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-6">
                
                <!-- Sidebar Widget 1: Ranked Popular Posts Widget (Photo 5 Style) -->
                <div class="bg-white border border-slate-100 p-5 shadow-sm">
                    <h4 class="font-display text-sm font-black text-slate-800 uppercase tracking-tight mb-4 border-b border-slate-200 pb-2.5 relative anony-section-title">Xem Nhiều Nhất</h4>
                    <div class="space-y-4">
                        <?php
                        $popular_posts = new WP_Query( array(
                            'post_type'           => 'post',
                            'posts_per_page'      => 4,
                            'meta_key'            => '_view_count',
                            'ignore_sticky_posts' => true,
                            'orderby'             => 'meta_value_num',
                            'order'               => 'DESC'
                        ) );
                        
                        $p_idx = 1;
                        if ( $popular_posts->have_posts() ) :
                            while ( $popular_posts->have_posts() ) : $popular_posts->the_post();
                                if ($p_idx === 1) : 
                                    $large_img = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
                                    if ( !$large_img ) $large_img = techblog_get_placeholder_img();
                                ?>
                                    <div class="group relative flex flex-col cursor-pointer pb-4 border-b border-slate-100">
                                        <a href="<?php the_permalink(); ?>" class="aspect-[16/10] overflow-hidden block relative bg-slate-900 mb-3">
                                            <img src="<?php echo esc_url($large_img); ?>" class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-750" alt="<?php the_title_attribute(); ?>" />
                                        </a>
                                        <div class="flex gap-3 items-start">
                                            <span class="font-display text-4xl font-extrabold italic text-slate-200 group-hover:text-primary transition-colors shrink-0 tracking-tighter leading-none">01</span>
                                            <div class="flex-grow min-w-0">
                                                <h3 class="font-display text-[13px] font-black text-slate-855 group-hover:text-primary transition-colors leading-snug break-words">
                                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                                </h3>
                                                <span class="text-[9px] text-slate-400 mt-1 font-bold uppercase tracking-wider flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[11px]">calendar_today</span>
                                                    <?php echo get_the_date(); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <div class="group flex gap-3 items-start py-2.5 border-b border-slate-100/50 last:border-0">
                                        <span class="font-display text-3xl font-extrabold italic text-slate-200 group-hover:text-primary transition-colors shrink-0 tracking-tighter leading-none w-10 text-center">
                                            <?php echo sprintf('%02d', $p_idx); ?>
                                        </span>
                                        <div class="flex-grow min-w-0">
                                            <h4 class="font-display text-[12px] font-bold text-slate-700 group-hover:text-primary transition-colors leading-snug break-words">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h4>
                                            <span class="text-[9px] text-slate-400 mt-1 font-bold uppercase tracking-wider flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[11px]">calendar_today</span>
                                                <?php echo get_the_date(); ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php 
                                endif;
                                $p_idx++;
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>
                </div>

                <!-- Sidebar Widget 2: BÀI VIẾT NỔI BẬT (Featured/Pinned Posts - Strictly Pinned/Sticky system) -->
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
                                        <h5 class="font-display text-[12px] font-bold text-slate-800 group-hover/item:text-primary transition-colors leading-snug break-words">
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
            
        </div> <!-- End of Unified Premium Layout Grid -->
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Main Slider Logic
        const slides = document.querySelectorAll('.hero-slide');
        const thumbs = document.querySelectorAll('.hero-thumb-btn');
        const prevBtn = document.getElementById('hero-prev-btn');
        const nextBtn = document.getElementById('hero-next-btn');
        const track = document.getElementById('hero-slider-track');
        
        if (slides.length <= 1) return;
        
        const totalOriginal = slides.length;
        
        // Clone first slide and append it for seamless transition
        const firstSlideClone = slides[0].cloneNode(true);
        firstSlideClone.classList.add('hero-slide-clone');
        track.appendChild(firstSlideClone);
        
        // Clone last slide and prepend it for seamless transition
        const lastSlideClone = slides[slides.length - 1].cloneNode(true);
        lastSlideClone.classList.add('hero-slide-clone');
        track.insertBefore(lastSlideClone, track.firstElementChild);
        
        // Adjust track width and child slides widths to accommodate clones
        const totalSlidesCount = totalOriginal + 2;
        track.style.width = `${totalSlidesCount * 100}%`;
        
        const childSlides = track.children;
        for (let i = 0; i < childSlides.length; i++) {
            childSlides[i].style.width = `${100 / totalSlidesCount}%`;
        }
        
        let currentIdx = 0; // index for thumbnails / indicators (0 to L-1)
        let activeIdx = 1;  // physical index on the track (1 to L)
        let sliderTimer;
        let isSliderTransitioning = false;
        
        function goToSlide(index, animated = true) {
            if (isSliderTransitioning && animated) return;
            
            // Deactivate old thumbnail
            if (thumbs[currentIdx]) {
                const oldOverlay = thumbs[currentIdx].querySelector('.active-overlay');
                if (oldOverlay) {
                    oldOverlay.classList.remove('opacity-100');
                    oldOverlay.classList.add('opacity-0');
                }
                const oldTriangle = thumbs[currentIdx].querySelector('.indicator-triangle');
                if (oldTriangle) oldTriangle.classList.add('opacity-0');
            }
            
            // Set new active indicators
            activeIdx = index;
            currentIdx = (activeIdx - 1 + totalOriginal) % totalOriginal;
            
            if (animated) {
                isSliderTransitioning = true;
                track.style.transition = 'transform 700ms cubic-bezier(0.25, 1, 0.5, 1)';
            } else {
                track.style.transition = 'none';
            }
            
            const percentage = -activeIdx * (100 / totalSlidesCount);
            track.style.transform = `translateX(${percentage}%)`;
            
            // Activate new thumbnail
            if (thumbs[currentIdx]) {
                const newOverlay = thumbs[currentIdx].querySelector('.active-overlay');
                if (newOverlay) {
                    newOverlay.classList.remove('opacity-0');
                    newOverlay.classList.add('opacity-100');
                }
                const newTriangle = thumbs[currentIdx].querySelector('.indicator-triangle');
                if (newTriangle) newTriangle.classList.remove('opacity-0');
            }
            
            // Slide thumbnail track if there are more than 7 thumbnails
            const thumbsTrack = document.getElementById('hero-thumbs-track');
            if (thumbsTrack && thumbs.length > 7) {
                let shiftIdx = currentIdx - 3;
                if (shiftIdx < 0) shiftIdx = 0;
                const maxShift = thumbs.length - 7;
                if (shiftIdx > maxShift) shiftIdx = maxShift;
                const thumbWidth = thumbs[0].offsetWidth;
                const gap = 8;
                const translateX = -shiftIdx * (thumbWidth + gap);
                thumbsTrack.style.transform = `translateX(${translateX}px)`;    
            }
        }
        
        // Handle snaps on transition end
        track.addEventListener('transitionend', function() {
            isSliderTransitioning = false;
            
            // If we reached the cloned last slide at index 0
            if (activeIdx === 0) {
                track.style.transition = 'none';
                activeIdx = totalOriginal;
                const percentage = -activeIdx * (100 / totalSlidesCount);
                track.style.transform = `translateX(${percentage}%)`;
            }
            // If we reached the cloned first slide at index totalOriginal + 1
            else if (activeIdx === totalOriginal + 1) {
                track.style.transition = 'none';
                activeIdx = 1;
                const percentage = -activeIdx * (100 / totalSlidesCount);
                track.style.transform = `translateX(${percentage}%)`;
            }
        });
        
        function autoPlayNext() {
            if (!isSliderTransitioning) {
                goToSlide(activeIdx + 1, true);
            }
        }
        
        function startSliderTimer() {
            stopSliderTimer();
            sliderTimer = setInterval(autoPlayNext, 6000);
        }
        
        function stopSliderTimer() {
            if (sliderTimer) clearInterval(sliderTimer);
        }
        
        if (prevBtn) {
            prevBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (isSliderTransitioning) return;
                
                stopSliderTimer();
                goToSlide(activeIdx - 1, true);
                startSliderTimer();
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (isSliderTransitioning) return;
                
                stopSliderTimer();
                goToSlide(activeIdx + 1, true);
                startSliderTimer();
            });
        }
        
        thumbs.forEach(function(thumb) {
            thumb.addEventListener('click', function() {
                if (isSliderTransitioning) return;
                
                const idx = parseInt(thumb.getAttribute('data-thumb-index'), 10);
                stopSliderTimer();
                goToSlide(idx + 1, true);
                startSliderTimer();
            });
        });
        
        // Set initial position instantly without transition
        goToSlide(1, false);
        startSliderTimer();

        // Drag/Swipe functionality for Hero Slider (Click-hold & Swipe support)
        const wrapper = document.getElementById('hero-slider-wrapper');
        if (wrapper && track) {
            let isDragging = false;
            let startX = 0;
            let dragDistance = 0;
            let clickPrevented = false;
            let trackTranslateStart = 0;

            wrapper.style.cursor = 'grab';

            // Disable default image and link dragging to avoid conflicts
            wrapper.querySelectorAll('img, a').forEach(el => {
                el.addEventListener('dragstart', (e) => e.preventDefault());
            });

            function getPositionX(event) {
                return event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
            }

            function dragStart(e) {
                if (isSliderTransitioning) return;
                isDragging = true;
                startX = getPositionX(e);
                dragDistance = 0;
                clickPrevented = false;
                stopSliderTimer();
                
                // Temporarily disable track transition for instant response during drag
                track.style.transition = 'none';
                
                // Get starting translate X in pixels based on physical activeIdx
                const wrapperWidth = wrapper.clientWidth;
                trackTranslateStart = -activeIdx * wrapperWidth;
                
                if (e.type.includes('mouse')) {
                    wrapper.style.cursor = 'grabbing';
                }
            }

            function dragMove(e) {
                if (!isDragging) return;
                const currentX = getPositionX(e);
                dragDistance = currentX - startX;
                
                if (Math.abs(dragDistance) > 10) {
                    clickPrevented = true;
                }
                
                // Move track visually in real-time
                const newTranslateX = trackTranslateStart + dragDistance;
                track.style.transform = `translateX(${newTranslateX}px)`;
            }

            function dragEnd() {
                if (!isDragging) return;
                isDragging = false;
                
                // Restore transition for smooth snapping
                track.style.transition = '';
                wrapper.style.cursor = 'grab';
                
                const wrapperWidth = wrapper.clientWidth;
                const threshold = wrapperWidth * 0.15; // 15% swipe threshold for next/prev
                
                if (dragDistance < -threshold) {
                    goToSlide(activeIdx + 1);
                } else if (dragDistance > threshold) {
                    goToSlide(activeIdx - 1);
                } else {
                    // Snap back to current slide
                    goToSlide(activeIdx);
                }
                
                startSliderTimer();
            }

            wrapper.addEventListener('mousedown', dragStart);
            wrapper.addEventListener('mousemove', dragMove);
            wrapper.addEventListener('mouseup', dragEnd);
            wrapper.addEventListener('mouseleave', dragEnd);

            // Touch support
            wrapper.addEventListener('touchstart', dragStart, { passive: true });
            wrapper.addEventListener('touchmove', dragMove, { passive: true });
            wrapper.addEventListener('touchend', dragEnd);

            // Prevent click navigation if drag happened
            wrapper.addEventListener('click', function(e) {
                if (clickPrevented) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            }, true);
        }

        // Newsflash Ticker Logic (Rolling Vertical Slide-Up Track)
        const nfTrack = document.getElementById('homepage-newsflash-track');
        const nfPrev = document.getElementById('nf-prev-btn');
        const nfNext = document.getElementById('nf-next-btn');
        
        if (nfTrack) {
            const originalItems = Array.from(nfTrack.children);
            if (originalItems.length > 1) {
                const totalOriginal = originalItems.length;
                
                // Clone first item and append it
                const firstNfClone = originalItems[0].cloneNode(true);
                firstNfClone.classList.add('nf-clone');
                nfTrack.appendChild(firstNfClone);
                
                let nfIdx = 0;
                let nfTimer;
                let isNfTransitioning = false;
                
                function showNfItem(index, animated = true) {
                    if (isNfTransitioning && animated) return;
                    
                    if (animated) {
                        isNfTransitioning = true;
                        nfTrack.style.transition = 'top 500ms ease-in-out';
                    } else {
                        nfTrack.style.transition = 'none';
                    }
                    
                    nfIdx = index;
                    nfTrack.style.top = `-${nfIdx * 24}px`;
                }
                
                // Handle instant snaps on transition end
                nfTrack.addEventListener('transitionend', function() {
                    isNfTransitioning = false;
                    
                    // If we reached the cloned item (nfIdx === totalOriginal)
                    if (nfIdx === totalOriginal) {
                        nfTrack.style.transition = 'none';
                        nfTrack.style.top = '0px';
                        nfIdx = 0;
                    }
                });
                
                function startNfTimer() {
                    stopNfTimer();
                    nfTimer = setInterval(function() {
                        if (!isNfTransitioning) {
                            showNfItem(nfIdx + 1, true);
                        }
                    }, 4000);
                }
                
                function stopNfTimer() {
                    if (nfTimer) clearInterval(nfTimer);
                }
                
                if (nfPrev) {
                    nfPrev.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (isNfTransitioning) return;
                        
                        stopNfTimer();
                        if (nfIdx === 0) {
                            // Instantly snap to the clone first
                            nfTrack.style.transition = 'none';
                            nfTrack.style.top = `-${totalOriginal * 24}px`;
                            nfTrack.offsetHeight; // force reflow
                            
                            // Then animate to the last original item
                            showNfItem(totalOriginal - 1, true);
                        } else {
                            showNfItem(nfIdx - 1, true);
                        }
                        startNfTimer();
                    });
                }
                
                if (nfNext) {
                    nfNext.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (isNfTransitioning) return;
                        
                        stopNfTimer();
                        showNfItem(nfIdx + 1, true);
                        startNfTimer();
                    });
                }
                
                startNfTimer();
            }
        }
    });
</script>

<?php get_footer(); ?>
