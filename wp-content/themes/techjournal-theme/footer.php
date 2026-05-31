<!-- Mobile App-style Bottom Navigation Bar (Visible only on mobile) -->
<?php
$posts_page_id = get_option( 'page_for_posts' );
$posts_page_url = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/' );
?>
<nav class="lg:hidden fixed bottom-0 left-0 w-full bg-white/95 backdrop-blur-md border-t border-outline-variant/60 z-50 flex justify-around items-center py-2 px-4 shadow-[0_-4px_20px_rgba(0,0,0,0.05)]" aria-label="Mobile Navigation Menu">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex flex-col items-center gap-0.5 <?php echo is_front_page() ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-primary'; ?> transition-all active:scale-90" aria-label="Navigate to Home">
        <span class="material-symbols-outlined text-[24px]" aria-hidden="true" style="<?php echo is_front_page() ? "font-variation-settings: 'FILL' 1;" : ""; ?>">home</span>
        <span class="font-label-md text-[10px]">Home</span>
    </a>
    
    <a href="<?php echo esc_url( $posts_page_url ); ?>" class="flex flex-col items-center gap-0.5 <?php echo (is_home() || is_archive()) ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-primary'; ?> transition-all active:scale-90" aria-label="Navigate to Articles">
        <span class="material-symbols-outlined text-[24px]" aria-hidden="true" style="<?php echo (is_home() || is_archive()) ? "font-variation-settings: 'FILL' 1;" : ""; ?>">article</span>
        <span class="font-label-md text-[10px]">Articles</span>
    </a>
    
    <button onclick="toggleMobileCategoryDrawer()" class="flex flex-col items-center gap-0.5 text-on-surface-variant hover:text-primary transition-all active:scale-90" aria-haspopup="dialog" aria-expanded="false" aria-label="Explore engineering topics">
        <span class="material-symbols-outlined text-[24px]" aria-hidden="true">explore</span>
        <span class="font-label-md text-[10px]">Topics</span>
    </button>
    
    <?php
    $contact_page = get_page_by_path('lien-he');
    if ( $contact_page ) :
    ?>
        <a href="<?php echo esc_url( get_permalink( $contact_page->ID ) ); ?>" class="flex flex-col items-center gap-0.5 <?php echo is_page( $contact_page->ID ) ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-primary'; ?> transition-all active:scale-90" aria-label="Navigate to Contact Page">
            <span class="material-symbols-outlined text-[24px]" aria-hidden="true" style="<?php echo is_page( $contact_page->ID ) ? "font-variation-settings: 'FILL' 1;" : ""; ?>">mail</span>
            <span class="font-label-md text-[10px]">Contact</span>
        </a>
    <?php endif; ?>
</nav>

<!-- Mobile Dynamic Category Drawer overlay - Flattened -->
<div id="mobile-category-drawer" class="fixed inset-0 bg-black/40 z-50 hidden transition-all duration-300" role="dialog" aria-modal="true" aria-label="Topics Menu Drawer">
    <div class="absolute bottom-0 w-full bg-white max-h-[70vh] overflow-y-auto p-6 transition-transform duration-300 translate-y-full" id="drawer-content">
        <div class="flex justify-between items-center mb-6 pb-2 border-b border-outline-variant">
            <h3 class="font-headline-md text-headline-md text-primary font-bold">Engineering Topics</h3>
            <button onclick="toggleMobileCategoryDrawer()" aria-label="Close topics drawer" class="material-symbols-outlined text-on-surface-variant hover:text-primary p-1 bg-surface-container cursor-pointer">close</button>
        </div>
        
        <div class="grid grid-cols-2 gap-4 pb-10">
            <?php
            $drawer_cats = get_categories( array(
                'exclude' => get_option( 'default_category' )
            ) );
            if ( ! empty( $drawer_cats ) ) {
                foreach ( $drawer_cats as $cat ) {
                    echo '<a class="flex items-center gap-3 bg-surface-container-low border border-outline-variant p-4 font-label-md text-label-md text-on-surface-variant hover:bg-primary-container hover:text-white transition-all duration-150" href="' . esc_url( get_category_link( $cat->term_id ) ) . '">
                        <span class="material-symbols-outlined text-primary" aria-hidden="true">terminal</span>
                        ' . esc_html( $cat->name ) . '
                    </a>';
                }
            } else {
                echo '<p class="col-span-2 text-on-surface-variant">No categories loaded yet.</p>';
            }
            ?>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="w-full mt-section-gap bg-slate-900 border-t border-slate-800 text-slate-400" role="contentinfo">
    <div class="max-w-container-max mx-auto px-6 py-16 grid grid-cols-1 md:grid-cols-12 gap-12">
        <!-- Brand Info (Chuẩn SEO) -->
        <div class="col-span-12 md:col-span-4 lg:col-span-5 space-y-5">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="block hover:opacity-90 active:scale-95 transition-all">
                <!-- <span class="text-2xl font-display font-black text-white tracking-tighter hover:text-primary">
                    Tech<span class="text-primary">Blog</span>
                </span>
                <span class="sr-only"><?php bloginfo( 'name' ); ?></span> -->
                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/Logo-TechBlog-footer.svg' ); ?>" alt="<?php bloginfo( 'name' ); ?> Logo" class="h-12 w-auto block" style="clip-path: inset(1px);" />
            </a>
            <p class="text-slate-400 text-sm leading-relaxed max-w-sm">
                <?php 
                $site_desc = get_bloginfo( 'description' );
                if ( empty( $site_desc ) || $site_desc === 'Một trang web sử dụng WordPress' || $site_desc === 'Just another WordPress site' ) {
                    $site_desc = 'Kênh thông tin công nghệ hàng đầu, chuyên cập nhật các tin tức công nghệ mới nhất, đánh giá chi tiết thiết bị, kiến thức lập trình và giải pháp phần mềm hiện đại.';
                }
                echo esc_html( $site_desc ); 
                ?>
            </p>
            <!-- Social profiles - Flattened -->
            <div class="flex gap-3 pt-2">
                <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="w-9 h-9 bg-slate-800 border border-slate-700/60 hover:bg-primary hover:border-primary text-slate-300 hover:text-white flex items-center justify-center transition-all duration-300 active:scale-95" title="Facebook">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 fill-current" viewBox="0 0 320 512" aria-hidden="true" style="width: 15px; height: 15px;">
                        <path d="M80 299.3V512H196V299.3h86.5l13.6-103.6H196V129.3c0-30 8.3-50.5 51.4-50.5H302V3.6C292.8 2.4 261.5 0 225.1 0 149 0 97 46.4 97 131.6v64.1H7V299.3H80z"/>
                    </svg>
                </a>
                <a href="https://t.me" target="_blank" rel="noopener noreferrer" class="w-9 h-9 bg-slate-800 border border-slate-700/60 hover:bg-primary hover:border-primary text-slate-300 hover:text-white flex items-center justify-center transition-all duration-300 active:scale-95" title="Telegram">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 fill-current" viewBox="0 0 496 512" aria-hidden="true" style="width: 16px; height: 16px;">
                        <path d="M248,8C111,8,0,119,0,256S111,504,248,504,496,393,496,256,385,8,248,8ZM363,177.3,322,370.7c-3,13.5-11,16.8-22.3,10.5l-62.5-46.1L207,364c-3.4,3.4-6.3,6.3-12.9,6.3l4.5-63.7L314,195.4c5-4.4-1.1-6.9-7.7-2.5L181.8,304,120,284.7c-13.5-4.2-13.8-13.5,2.8-20L264,166C275.1,161.4,285.8,168.3,363,177.3Z"/>
                    </svg>
                </a>
                <a href="mailto:<?php echo antispambot( get_option( 'admin_email' ) ); ?>" class="w-9 h-9 bg-slate-800 border border-slate-700/60 hover:bg-primary hover:border-primary text-slate-300 hover:text-white flex items-center justify-center transition-all duration-300 active:scale-95" title="Email liên hệ">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 fill-current" viewBox="0 0 24 24" aria-hidden="true" style="width: 17px; height: 17px;">
                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                </a>
            </div>
        </div>
        
        <!-- Top categories - Flattened -->
        <div class="col-span-6 md:col-span-4 lg:col-span-4 space-y-4">
            <span class="font-display text-xs font-black uppercase tracking-wider text-slate-200 block border-b border-slate-800 pb-2.5">Chủ đề chính</span>
            <nav class="grid grid-cols-2 gap-x-4 gap-y-2.5 text-[13px]" aria-label="Footer Categories Links">
                <?php
                $exclude_ids = array();
                $default_cat_id = (int) get_option( 'default_category' );
                if ( $default_cat_id ) {
                    $default_cat = get_category( $default_cat_id );
                    if ( $default_cat && in_array( $default_cat->slug, array( 'chua-phan-loai', 'uncategorized' ) ) ) {
                        $exclude_ids[] = $default_cat_id;
                    }
                }

                $footer_cats = get_categories( array(
                    'orderby' => 'count',
                    'order'   => 'DESC',
                    'exclude' => $exclude_ids,
                    'hide_empty' => false // Hiển thị cả chuyên mục chưa có bài viết để lấy ra toàn bộ chủ đề
                ) );
                if ( ! empty( $footer_cats ) ) {
                    foreach ( $footer_cats as $cat ) {
                        echo '<a class="text-slate-400 hover:text-primary transition-colors flex items-center gap-1.5" href="' . esc_url( get_category_link( $cat->term_id ) ) . '">
                            <span class="w-1.5 h-1.5 bg-slate-850"></span>' . esc_html( $cat->name ) . '
                        </a>';
                    }
                }
                ?>
            </nav>
        </div>

        <!-- Resources / Quick Links -->
        <div class="col-span-6 md:col-span-4 lg:col-span-3 space-y-4">
            <span class="font-display text-xs font-black uppercase tracking-wider text-slate-200 block border-b border-slate-800 pb-2.5">Các trang khác</span>
            <nav class="flex flex-col gap-2.5 text-[13px]" aria-label="Footer Company Links">
                <?php
                $about_page = get_page_by_path('gioi-thieu');
                $contact_page = get_page_by_path('lien-he');
                $privacy_page = get_page_by_path('chinh-sach-bao-mat');
                
                if ( $about_page ) :  
                ?>
                    <a class="text-slate-400 hover:text-primary transition-colors flex items-center gap-1.5" href="<?php echo esc_url( get_permalink( $about_page->ID ) ); ?>">
                        <span class="w-1.5 h-1.5 bg-slate-850"></span>Giới thiệu
                    </a>
                <?php endif; ?>
                
                <?php if ( $contact_page ) : ?>
                    <a class="text-slate-400 hover:text-primary transition-colors flex items-center gap-1.5" href="<?php echo esc_url( get_permalink( $contact_page->ID ) ); ?>">
                        <span class="w-1.5 h-1.5 bg-slate-850"></span>Liên hệ
                    </a>
                <?php endif; ?>
                
                <?php 
                $privacy_url = get_privacy_policy_url();
                if ( ! empty( $privacy_url ) ) : 
                ?>
                    <a class="text-slate-400 hover:text-primary transition-colors flex items-center gap-1.5" href="<?php echo esc_url( $privacy_url ); ?>">
                        <span class="w-1.5 h-1.5 bg-slate-850"></span>Chính sách bảo mật
                    </a>
                <?php elseif ( $privacy_page ) : ?>
                    <a class="text-slate-400 hover:text-primary transition-colors flex items-center gap-1.5" href="<?php echo esc_url( get_permalink( $privacy_page->ID ) ); ?>">
                        <span class="w-1.5 h-1.5 bg-slate-850"></span>Chính sách bảo mật
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
    
    <!-- Bottom line -->
    <div class="max-w-container-max mx-auto px-6 py-8 border-t border-slate-800/60 flex justify-center items-center text-slate-500 text-xs">
        <span class="flex items-center gap-1.5">&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. Thiết kế bởi <span class="hover:text-primary transition-colors">Admin TechBlog</span></span>
    </div>
</footer>

<?php wp_footer(); ?>

<script>
    // Reading progress bar micro-interaction
    window.addEventListener('scroll', () => {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        const progressBar = document.getElementById('progress-bar');
        if (progressBar) {
            progressBar.style.width = scrolled + "%";
        }
    });

    // Mobile Dynamic Drawer controls
    function toggleMobileCategoryDrawer() {
        const drawer = document.getElementById('mobile-category-drawer');
        const content = document.getElementById('drawer-content');
        if (drawer.classList.contains('hidden')) {
            drawer.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('translate-y-full');
            }, 10);
        } else {
            content.classList.add('translate-y-full');
            setTimeout(() => {
                drawer.classList.add('hidden');
            }, 300);
        }
    }

    // Close drawer when clicking outside content area
    document.getElementById('mobile-category-drawer').addEventListener('click', (e) => {
        if (e.target === document.getElementById('mobile-category-drawer')) {
            toggleMobileCategoryDrawer();
        }
    });

    // Elegant hover effect for article cards
    const cards = document.querySelectorAll('article');
    cards.forEach(card => {
        card.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
    });
</script>

<!-- Back to Top Button (Floating Zero Border-Radius Red Theme with Font Awesome SVG) -->
<button class="back-to-top fixed bottom-20 lg:bottom-8 right-6 z-50 bg-primary text-white w-10 h-10 flex items-center justify-center rounded-none shadow-md hover:bg-primary/80 transition-all cursor-pointer duration-300 active:scale-95" id="backToTop" style="display: none;" aria-label="Quay lại đầu trang">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-white" viewBox="0 0 640 640" aria-hidden="true">
        <!--!Font Awesome Free v7.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.-->
        <path d="M342.6 73.4C330.1 60.9 309.8 60.9 297.3 73.4L137.3 233.4C124.8 245.9 124.8 266.2 137.3 278.7C149.8 291.2 170.1 291.2 182.6 278.7L288 173.3L288 544C288 561.7 302.3 576 320 576C337.7 576 352 561.7 352 544L352 173.3L457.4 278.7C469.9 291.2 490.2 291.2 502.7 278.7C515.2 266.2 515.2 245.9 502.7 233.4L342.7 73.4z"/>
    </svg>
</button>

<script>
    document.addEventListener("DOMContentLoaded", function () {
      const btn = document.getElementById("backToTop");
      if (btn) {
        window.addEventListener("scroll", function () {
          if (window.scrollY > 300) {
            btn.style.display = "flex";
          } else {
            btn.style.display = "none";
          }
        });

        btn.addEventListener("click", function () {
          const duration = 800; // 800ms for a premium, gentle slide-up
          const startPosition = window.pageYOffset || document.documentElement.scrollTop;
          const startTime = performance.now();

          function easeOutQuad(t) {
            return t * (2 - t);
          }

          function scrollStep(timestamp) {
            const timeElapsed = timestamp - startTime;
            const progress = Math.min(timeElapsed / duration, 1);
            const ease = easeOutQuad(progress);

            window.scrollTo(0, startPosition * (1 - ease));

            if (progress < 1) {
              requestAnimationFrame(scrollStep);
            }
          }

          requestAnimationFrame(scrollStep);
        });
      }
    });
    </script>
</body>
</html>
