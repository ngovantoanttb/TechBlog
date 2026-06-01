<?php
/**
 * The single post template file - TechBlog Style
 *
 * @package TechJournal
 * @since 1.0.0
 */

get_header(); ?>

<main class="pt-6 sm:pt-8 pb-section-gap bg-background min-h-screen">
    <div class="max-w-container-max mx-auto px-4 grid grid-cols-12 gap-6 items-start">
        
        <!-- Left: Main Post Reading Area -->
        <div class="col-span-12 lg:col-span-8 bg-white border border-slate-100/80 p-5 sm:p-8 premium-shadow">
            <?php
            if ( have_posts() ) :
                while ( have_posts() ) : the_post();
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
                    $read_time = techjournal_calculate_read_time( get_the_content() );
                    $views = techjournal_get_post_views( get_the_ID() );
                    ?>
                    
                    <!-- Breadcrumbs (TechBlog Editorial style) -->
                    <nav class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-5" aria-label="Breadcrumb">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-primary transition-all">Trang Chủ</a>
                        <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                        <?php if ( $category_to_show ) : ?>
                            <a href="<?php echo esc_url( get_category_link( $category_to_show->term_id ) ); ?>" class="hover:text-primary transition-all"><?php echo esc_html( $category_to_show->name ); ?></a>
                            <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                        <?php endif; ?>
                        <span class="text-slate-600 truncate max-w-[150px] sm:max-w-[300px] inline-block"><?php the_title(); ?></span>
                    </nav>

                    <!-- Article Title -->
                    <h1 class="font-display text-2xl sm:text-3xl md:text-4xl text-slate-900 font-extrabold tracking-tight leading-tight mb-5 break-words">
                        <?php the_title(); ?>
                    </h1>

                    <!-- Article Meta -->
                    <div class="flex flex-wrap items-center gap-y-2.5 gap-x-4 pb-5 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-6">
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px] text-primary">person</span> <?php echo (strcasecmp(get_the_author(), 'admin') === 0) ? 'Admin TechBlog' : get_the_author(); ?>
                        </span>
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px] text-primary">calendar_today</span> <?php echo get_the_date(); ?>
                        </span>
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px] text-primary">schedule</span> <?php echo $read_time; ?> phút đọc
                        </span>
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px] text-primary">visibility</span> <?php echo $views; ?> lượt xem
                        </span>
                    </div>

                    <!-- Featured Thumbnail - Flattened -->
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="border border-slate-100/50 mb-8 bg-slate-950 aspect-[16/10] overflow-hidden">
                            <img class="w-full h-full object-cover opacity-95" src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'large' ) ); ?>" alt="<?php the_title_attribute(); ?>" />
                        </div>
                    <?php endif; ?>

                    <!-- Rich Post Body -->
                    <div class="prose max-w-none text-slate-600 text-[14.5px] leading-relaxed space-y-6">
                        <?php
                        $content = get_the_content();
                        
                        // Render styled Terminal window codeblocks dynamically (TechBlog code theme - Flattened)
                        $content = preg_replace_callback(
                            '/`<pre><code[^>]*>(.*?)<\/code><\/pre>`/is',
                            function ( $matches ) {
                                $code = html_entity_decode( $matches[1] );
                                return '<div class="my-6 border border-slate-700/60 overflow-hidden bg-slate-900 shadow-sm font-code text-xs">
                                    <div class="bg-slate-950 px-4 py-2 border-b border-slate-800 flex justify-between items-center text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                                        <span class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 bg-red-500"></span>
                                            <span class="w-2.5 h-2.5 bg-yellow-500"></span>
                                            <span class="w-2.5 h-2.5 bg-green-500"></span>
                                            <span class="ml-2 font-bold font-sans">TechBlog Terminal</span>
                                        </span>
                                        <button onclick="navigator.clipboard.writeText(this.nextElementSibling.innerText); alert(\'Đã copy code vào Clipboard!\')" class="hover:text-primary transition-all text-[10px] font-bold py-0.5 px-1.5 border border-slate-800 hover:bg-slate-900 active:scale-95 cursor-pointer">COPY CODE</button>
                                    </div>
                                    <pre class="p-4 text-emerald-400 overflow-x-auto"><code>' . esc_html( trim( $code ) ) . '</code></pre>
                                </div>';
                            },
                            $content
                        );
                        
                        echo apply_filters( 'the_content', $content );
                        ?>
                    </div>

                    <!-- Social Share CTA (Minimalistic TechBlog style - Left Aligned) -->
                    <div class="mt-10 py-5 border-y border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <span class="font-display text-[11px] font-black uppercase tracking-wider text-slate-400">CHIA SẺ BÀI VIẾT</span>
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Facebook -->
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="bg-[#1877f2] hover:bg-[#166fe5] text-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-wider transition-all flex items-center gap-1.5 active:scale-95">
                                <?php echo techjournal_get_svg( 'facebook', 'w-3.5 h-3.5' ); ?> Facebook
                            </a>
                            <!-- Zalo -->
                            <a href="https://sp.zalo.me/share_to_zalo?url=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="bg-[#0068ff] hover:bg-[#0056d6] text-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-wider transition-all flex items-center gap-1.5 active:scale-95">
                                <?php echo techjournal_get_svg( 'zalo', 'w-3.5 h-3.5' ); ?> Zalo
                            </a>
                            <!-- Telegram -->
                            <a href="https://t.me/share/url?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" rel="noopener noreferrer" class="bg-[#0088cc] hover:bg-[#0077b5] text-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-wider transition-all flex items-center gap-1.5 active:scale-95">
                                <?php echo techjournal_get_svg( 'telegram', 'w-3.5 h-3.5' ); ?> Telegram
                            </a>
                            <!-- Copy Link -->
                            <button onclick="(function(btn){
                                var text = '<?php echo esc_js(get_permalink()); ?>';
                                if (navigator.clipboard && window.isSecureContext) {
                                    navigator.clipboard.writeText(text).then(function() {
                                        alert('Đã sao chép liên kết bài viết vào bộ nhớ tạm!');
                                    }).catch(function() {
                                        fallbackCopy(text);
                                    });
                                } else {
                                    fallbackCopy(text);
                                }
                                function fallbackCopy(val) {
                                    var textArea = document.createElement('textarea');
                                    textArea.value = val;
                                    textArea.style.position = 'fixed';
                                    textArea.style.opacity = '0';
                                    document.body.appendChild(textArea);
                                    textArea.focus();
                                    textArea.select();
                                    try {
                                        var successful = document.execCommand('copy');
                                        if (successful) {
                                            alert('Đã sao chép liên kết bài viết vào bộ nhớ tạm!');
                                        } else {
                                            prompt('Không thể tự động sao chép. Hãy nhấn Ctrl+C để sao chép liên kết:', val);
                                        }
                                    } catch (err) {
                                        prompt('Không thể tự động sao chép. Hãy nhấn Ctrl+C để sao chép liên kết:', val);
                                    }
                                    document.body.removeChild(textArea);
                                }
                            })(this)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-wider transition-all flex items-center gap-1.5 active:scale-95 cursor-pointer border border-slate-200">
                                <?php echo techjournal_get_svg( 'link', 'w-3.5 h-3.5' ); ?> Sao Chép Link
                            </button>
                        </div>
                    </div>

                    <!-- ================= RELATED ARTICLES SECTION (Compact Cards Grid - Flattened) ================= -->
                    <section class="mt-12 pt-8 border-t border-slate-100" aria-label="Related Articles">
                        <h3 class="font-display text-sm font-black text-slate-800 uppercase tracking-tight mb-6 anony-section-title">Bài Viết Cùng Chủ Đề</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <?php
                            // Fetch related posts from the SAME category
                            $cats = get_the_category( get_the_ID() );
                            $cat_ids = array();
                            if ( ! empty( $cats ) ) {
                                foreach ( $cats as $c ) {
                                    $cat_ids[] = $c->term_id;
                                }
                            }
                            
                            $related_query = new WP_Query( array(
                                'post_type'           => 'post',
                                'posts_per_page'      => 3,
                                'post__not_in'        => array( get_the_ID() ),
                                'category__in'        => $cat_ids,
                                'ignore_sticky_posts' => true,
                                'orderby'             => 'rand'
                            ) );

                            if ( $related_query->have_posts() ) :
                                while ( $related_query->have_posts() ) : $related_query->the_post();
                                    get_template_part( 'template-parts/content', 'grid' );
                                endwhile;
                                wp_reset_postdata();
                            else :
                                echo '<p class="text-slate-400 text-xs italic">Chưa có bài viết liên quan.</p>';
                            endif;
                            ?>
                        </div>
                    </section>

                    <!-- Comments area -->
                    <section class="mt-12 pt-8 border-t border-slate-100" id="comments">
                        <?php
                        if ( comments_open() || get_comments_number() ) :
                            comments_template();
                        endif;
                        ?>
                    </section>

                    <?php
                endwhile;
            endif;
            ?>
        </div>
        
        <!-- Right Sidebar (TechBlog Premium Sidebar Style) -->
        <aside class="col-span-12 lg:col-span-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-6">

            <!-- Sidebar: BÀI VIẾT NỔI BẬT (Featured/Pinned Posts - Strictly Pinned/Sticky system) -->
            <?php
            $sticky_ids = get_option( 'sticky_posts' );
            if ( ! empty( $sticky_ids ) ) :
                $filtered_sticky = array_diff( $sticky_ids, array( get_the_ID() ) );
                if ( ! empty( $filtered_sticky ) ) :
                    $sidebar_query = new WP_Query( array(
                        'post_type'           => 'post',
                        'post__in'            => $filtered_sticky,
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
                                <!-- Rank Number with high-end serif style and safe margin spacing -->
                                <div class="font-display text-2xl font-black text-slate-200 group-hover/item:text-primary transition-colors w-10 shrink-0 text-left tracking-tighter">
                                    <?php echo sprintf('%02d', $rank); ?>
                                </div>
                                
                                <!-- Thumbnail Image - Flattened -->
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
                                        <span>•</span>
                                        <span class="flex items-center gap-0.5">
                                            <span class="material-symbols-outlined text-[12px]">visibility</span>
                                            <?php echo techjournal_get_post_views( get_the_ID() ); ?> xem
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
        endif; 
        ?>
            
            <!-- Sidebar: BÀI VIẾT MỚI (Latest posts - Max 7 - Flattened) -->
            <div class="bg-white border border-slate-100 p-6 shadow-sm">
                <h4 class="font-display text-sm font-black text-slate-800 uppercase tracking-tight mb-5 border-b border-slate-200 pb-3 relative anony-section-title">Bài Viết Mới</h4>
                <div class="space-y-4">
                    <?php
                    $sidebar_latest = new WP_Query( array(
                        'post_type'           => 'post',
                        'posts_per_page'      => 7,
                        'post__not_in'        => array( get_the_ID() ),
                        'ignore_sticky_posts' => true,
                        'orderby'             => 'date',
                        'order'               => 'DESC'
                    ) );
                    
                    if ( $sidebar_latest->have_posts() ) :
                        while ( $sidebar_latest->have_posts() ) : $sidebar_latest->the_post();
                            $sidebar_thumb = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
                            if ( ! $sidebar_thumb ) {
                                $sidebar_thumb = techblog_get_placeholder_img();
                            }
                            ?>
                            <div class="flex gap-3 items-center group py-2.5 border-b border-slate-100/50 last:border-0">
                                <a href="<?php the_permalink(); ?>" class="w-12 h-12 overflow-hidden shrink-0 bg-slate-100 shadow-sm block relative">
                                    <img src="<?php echo esc_url($sidebar_thumb); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="<?php the_title_attribute(); ?>" />
                                </a>
                                <div class="flex-grow min-w-0">
                                    <h5 class="font-display text-[12px] font-bold text-slate-700 hover:text-primary transition-colors leading-snug break-words">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h5>
                                    <p class="text-[9px] text-slate-400 mt-1 font-bold uppercase tracking-wider flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[11px] text-primary">calendar_today</span> <?php echo get_the_date(); ?>
                                    </p>
                                </div>
                            </div>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        echo '<p class="text-slate-400 text-xs italic">Đang cập nhật...</p>';
                    endif;
                    ?>
                </div>
            </div>
        </aside>
        
    </div>
</main>

<?php get_footer(); ?>
