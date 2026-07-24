<?php
/**
 * The single post template file - TechBlog Style
 *
 * @package TechJournal
 * @since 1.0.0
 */

get_header(); ?>

<main class="pt-6 sm:pt-8 pb-section-gap bg-background min-h-screen">
    <div class="max-w-container-max mx-auto px-0 sm:px-4 grid grid-cols-1 md:grid-cols-12 gap-y-8 gap-x-0 md:gap-10 items-start">
        
        <!-- Left: Main Post Reading Area -->
        <div class="col-span-1 md:col-span-8 bg-white border border-slate-100/80 p-5 sm:p-8 premium-shadow min-w-0">
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
                    <nav class="flex items-center gap-1.5 text-xs sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-5" aria-label="Breadcrumb">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-primary transition-all">Trang Chủ</a>
                        <?php echo techjournal_get_svg( 'chevron-right', 'w-3.5 h-3.5 text-slate-400 fill-current' ); ?>
                        <?php if ( $category_to_show ) : ?>
                            <a href="<?php echo esc_url( get_category_link( $category_to_show->term_id ) ); ?>" class="hover:text-primary transition-all"><?php echo esc_html( $category_to_show->name ); ?></a>
                            <?php echo techjournal_get_svg( 'chevron-right', 'w-3.5 h-3.5 text-slate-400 fill-current' ); ?>
                        <?php endif; ?>
                        <span class="text-slate-600 truncate max-w-[150px] sm:max-w-[300px] inline-block"><?php the_title(); ?></span>
                    </nav>

                    <!-- Article Title -->
                    <h1 class="font-display text-2xl sm:text-3xl md:text-4xl text-slate-900 font-extrabold tracking-tight leading-tight mb-5 break-words">
                        <?php the_title(); ?>
                    </h1>

                    <!-- Article Meta -->
                    <div class="flex flex-wrap items-center gap-y-2.5 gap-x-4 pb-5 border-b border-slate-100 text-xs sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-6">
                        <span class="flex items-center gap-1">
                            <?php echo techjournal_get_svg( 'user', 'w-4 h-4 text-slate-400 fill-current' ); ?> <?php echo (strcasecmp(get_the_author(), 'admin') === 0) ? 'Admin TechBlog' : get_the_author(); ?>
                        </span>
                        <span class="flex items-center gap-1">
                            <?php echo techjournal_get_svg( 'calendar', 'w-4 h-4 text-slate-400 fill-current' ); ?> <?php echo get_the_date(); ?>
                        </span>
                        <span class="flex items-center gap-1">
                            <?php echo techjournal_get_svg( 'clock', 'w-4 h-4 text-slate-400 fill-current' ); ?> <?php echo $read_time; ?> phút đọc
                        </span>
                        <span class="flex items-center gap-1">
                            <?php echo techjournal_get_svg( 'eye', 'w-4 h-4 text-slate-400 fill-current' ); ?> <?php echo $views; ?> lượt xem
                        </span>
                    </div>

                    <!-- Featured Thumbnail - Flattened & Optimized -->
                    <!-- <?php if ( has_post_thumbnail() ) : ?>
                        <div class="mb-8 bg-slate-950 aspect-[16/10] overflow-hidden">
                            <?php echo techblog_render_post_thumbnail( get_the_ID(), 'techjournal-hero', 'w-full h-full object-cover opacity-95', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
                        </div>
                    <?php endif; ?> -->

                    <!-- Rich Post Body -->
                    <div class="prose max-w-none text-slate-600 text-xl sm:text-base leading-relaxed space-y-6 [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:my-4 [&_ol]:list-decimal [&_ol]:pl-6 [&_ol]:my-4 [&_li]:my-1.5">
                        <?php
                        $content = get_the_content();
                        
                        // Render styled Terminal window codeblocks dynamically (TechBlog code theme - Flattened)
                        $content = preg_replace_callback(
                            // '/`<pre><code[^>]*>(.*?)<\/code><\/pre>`/is',
                            '/<pre><code[^>]*>(.*?)<\/code><\/pre>/is',
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
                        <span class="font-display text-xs font-black uppercase tracking-wider text-slate-400">CHIA SẺ BÀI VIẾT</span>
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Facebook -->
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="bg-[#1877f2] hover:bg-[#166fe5] text-white px-3.5 py-1.5 text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-1.5 active:scale-95">
                                <?php echo techjournal_get_svg( 'facebook', 'w-3.5 h-3.5' ); ?> Facebook
                            </a>
                            <!-- Telegram -->
                            <a href="https://t.me/share/url?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" rel="noopener noreferrer" class="bg-[#0088cc] hover:bg-[#0077b5] text-white px-3.5 py-1.5 text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-1.5 active:scale-95">
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
                            })(this)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3.5 py-1.5 text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-1.5 active:scale-95 cursor-pointer border border-slate-200">
                                <?php echo techjournal_get_svg( 'link', 'w-3.5 h-3.5' ); ?> Sao Chép Link
                            </button>
                        </div>
                    </div>

                    <!-- ================= RELATED ARTICLES SECTION (Compact Cards Grid - Flattened) ================= -->
                    <section class="mt-12 pt-8 border-t border-slate-100" aria-label="Related Articles">
                        <h2 class="font-display text-sm font-black text-slate-800 uppercase tracking-tight mb-6 anony-section-title">Bài Viết Cùng Chủ Đề</h2>
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
        <aside class="col-span-1 md:col-span-4 grid grid-cols-1 gap-10 min-w-0 p-5 md:p-0">

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
                <div class=" ">
                    <h3 class="font-display text-sm font-black text-slate-800 uppercase tracking-tight mb-5 border-b border-slate-200 pb-3 relative anony-section-title">Bài Viết Nổi Bật</h3>
                    <div class="space-y-4">
                        <?php
                        $rank = 1;
                        while ( $sidebar_query->have_posts() ) : $sidebar_query->the_post();
                        ?>
                            <div class="flex flex-row md:flex-col lg:flex-row gap-3 md:gap-2.5 items-start group/item py-3.5 border-b border-slate-100/50 last:border-0">
                                <a href="<?php the_permalink(); ?>" class="w-32 sm:w-36 md:w-full lg:w-20 aspect-[16/10] lg:aspect-[16/9] overflow-hidden shrink-0 bg-slate-100 shadow-sm block relative">
                                    <?php echo techblog_render_post_thumbnail( get_the_ID(), 'techjournal-thumb', 'w-full h-full object-cover group-hover/item:scale-105 transition-transform duration-500' ); ?>
                                </a>
                                <div class="flex-grow min-w-0 w-full">
                                    <h4 class="font-display text-xs sm:text-[12.5px] font-bold text-slate-800 group-hover/item:text-primary transition-colors leading-snug break-words">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h4>
                                    <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 md:hidden leading-normal">
                                        <?php echo esc_html( wp_trim_words( get_the_excerpt(), 12, '...' ) ); ?>
                                    </p>
                                    <div class="flex items-center gap-3 text-[11px] sm:text-[9px] text-slate-400 mt-1.5 font-bold uppercase tracking-wider">
                                        <span class="flex items-center gap-0.5">
                                            <?php echo techjournal_get_svg( 'calendar', 'w-3.5 h-3.5 text-slate-400 fill-current' ); ?>
                                            <?php echo get_the_date(); ?>
                                        </span>
                                        <span>•</span>
                                        <span class="flex items-center gap-0.5">
                                            <?php echo techjournal_get_svg( 'eye', 'w-3.5 h-3.5 text-slate-400 fill-current' ); ?>
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
            <div class=" ">
                <h3 class="font-display text-sm font-black text-slate-800 uppercase tracking-tight mb-5 border-b border-slate-200 pb-3 relative anony-section-title">Bài Viết Mới</h3>
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
                            ?>
                            <div class="flex gap-2.5 items-center group py-2.5 border-b border-slate-100/50 last:border-0">
                                <a href="<?php the_permalink(); ?>" class="w-16 sm:w-20 aspect-[16/9] overflow-hidden shrink-0 bg-slate-100 shadow-sm block relative">
                                    <?php echo techblog_render_post_thumbnail( get_the_ID(), 'techjournal-thumb', 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500' ); ?>
                                </a>
                                <div class="flex-grow min-w-0">
                                    <h4 class="font-display text-xs sm:text-[12px] font-bold text-slate-700 hover:text-primary transition-colors leading-snug break-words">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h4>
                                    <p class="text-[11px] sm:text-[9px] text-slate-400 mt-1 font-bold uppercase tracking-wider flex items-center gap-1">
                                        <?php echo techjournal_get_svg( 'calendar', 'w-3.5 h-3.5 text-slate-400 fill-current' ); ?> <?php echo get_the_date(); ?>
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
