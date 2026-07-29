<?php
$posts_page_id = get_option( 'page_for_posts' );
$posts_page_url = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/' );
?>

<!-- Mobile Dynamic Drawer overlay - Replaces category drawer and bottom bar with full site menu -->
<div id="mobile-category-drawer" class="fixed inset-0 bg-black/40 z-50 hidden transition-all duration-300" role="dialog" aria-modal="true" aria-label="Menu Navigation Drawer">
    <div class="absolute top-0 left-0 w-full bg-white max-h-[90vh] overflow-y-auto p-6 shadow-xl transition-transform duration-300 -translate-y-full" id="drawer-content">
        <!-- Drawer Header -->
        <div class="flex justify-between items-center mb-6 pb-2 border-b border-slate-100">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center active:scale-95 transition-all shrink-0">
                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/Logo-TechBlog-header.png' ); ?>" alt="<?php bloginfo( 'name' ); ?> Logo" class="h-6 w-auto block" />
            </a>
            <button onclick="toggleMobileCategoryDrawer()" aria-label="Close menu drawer" class="text-slate-500 hover:text-primary w-12 h-12 bg-slate-50 cursor-pointer flex items-center justify-center">
                <?php echo techjournal_get_svg( 'close', 'w-5 h-5 fill-current' ); ?>
            </button>
        </div>

        <!-- Search Bar for Mobile -->
        <div class="mb-6">
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="relative flex items-center bg-slate-50 border border-slate-200 focus-within:border-primary/80 transition-all pl-3.5 pr-0.5 h-12">
                <input type="search" placeholder="TÌM KIẾM..." name="s" value="<?php echo get_search_query(); ?>" class="w-full h-full text-sm text-slate-700 placeholder-slate-400 placeholder:text-xs placeholder:font-bold placeholder:tracking-wider focus:outline-none bg-transparent" />
                <button type="submit" aria-label="Tìm kiếm" class="w-12 h-12 text-slate-500 hover:text-primary transition-colors cursor-pointer flex items-center justify-center shrink-0">
                    <?php echo techjournal_get_svg( 'search', 'w-5 h-5' ); ?>
                </button>
            </form>
        </div>

        <!-- Hierarchical Site Navigation for Mobile -->
        <div class="space-y-4 mb-8">
            <span class="font-display text-xs font-bold uppercase tracking-wider text-slate-400 block border-b border-slate-100 pb-1.5">Danh mục</span>
            <nav class="space-y-1.5" aria-label="Mobile Site Navigation">
                <!-- Home -->
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-3 py-2.5 px-3 font-bold text-sm sm:text-[11px] uppercase tracking-wider text-slate-700 bg-slate-50 border border-slate-100/60 hover:bg-primary hover:text-white group transition-all">
                    <?php echo techjournal_get_svg( 'home', 'w-5 h-5 text-primary group-hover:text-white fill-current' ); ?>
                    Trang chủ
                </a>
                
                <!-- Parent: Articles (with toggleable child category list) -->
                <div class="relative">
                    <button onclick="toggleMobileSubmenu()" class="w-full flex items-center justify-between py-2.5 px-3 font-bold text-sm sm:text-[11px] uppercase tracking-wider text-slate-700 bg-slate-50 border border-slate-100/60 hover:bg-primary hover:text-white group transition-all text-left cursor-pointer">
                        <span class="flex items-center gap-3">
                            <?php echo techjournal_get_svg( 'article', 'w-5 h-5 text-primary group-hover:text-white fill-current' ); ?>
                            Bài viết
                        </span>
                        <span class="transition-transform duration-200" id="submenu-arrow">
                            <?php echo techjournal_get_svg( 'chevron-down', 'w-5 h-5 text-slate-400 group-hover:text-white fill-current' ); ?>
                        </span>
                    </button>
                    
                    <!-- Child Categories List (Accordion Container) -->
                    <div id="mobile-submenu-cats" class="hidden pl-4 bg-slate-50/50 border-x border-b border-slate-100 py-1.5 transition-all duration-300">
                        <!-- Link to All Articles -->
                        <a href="<?php echo esc_url( $posts_page_url ); ?>" class="flex items-center gap-2 py-2 px-3 text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-primary transition-all">
                            <span class="w-1.5 h-1.5 bg-slate-400"></span>
                            Tất cả bài viết
                        </a>
                        <?php
                        $exclude_ids = array();
                        $default_cat_id = (int) get_option( 'default_category' );
                        if ( $default_cat_id ) {
                            $default_cat = get_category( $default_cat_id );
                            if ( $default_cat && in_array( $default_cat->slug, array( 'chua-phan-loai', 'uncategorized' ) ) ) {
                                $exclude_ids[] = $default_cat_id;
                            }
                        }

                        $drawer_cats = get_categories( array(
                            'orderby'    => 'count',
                            'order'      => 'DESC',
                            'parent'     => 0,
                            'hide_empty' => false,
                            'exclude'    => $exclude_ids
                        ) );
                        if ( ! empty( $drawer_cats ) ) {
                            foreach ( $drawer_cats as $cat ) {
                                $sub_cats = get_categories( array(
                                    'orderby'    => 'count',
                                    'order'      => 'DESC',
                                    'parent'     => $cat->term_id,
                                    'hide_empty' => false
                                ) );

                                if ( ! empty( $sub_cats ) ) {
                                    ?>
                                    <div class="relative">
                                        <div class="flex items-center justify-between w-full pr-3 hover:bg-slate-50 group/item transition-all">
                                            <a class="flex items-center gap-2 py-2 px-3 text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-primary transition-all flex-grow" href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>">
                                                <span class="w-1.5 h-1.5 bg-primary/60"></span>
                                                <?php echo esc_html( $cat->name ); ?>
                                            </a>
                                            <button onclick="event.stopPropagation(); toggleMobileSubCatSubmenu(<?php echo $cat->term_id; ?>);" class="p-2 text-slate-400 hover:text-primary transition-transform duration-200 cursor-pointer" id="subcat-arrow-btn-<?php echo $cat->term_id; ?>" aria-label="Toggle subcategories">
                                                <?php echo techjournal_get_svg( 'chevron-down', 'w-4 h-4 fill-current' ); ?>
                                            </button>
                                        </div>
                                        <!-- Child Categories List -->
                                        <div id="mobile-subcat-cats-<?php echo $cat->term_id; ?>" class="hidden pl-6 bg-slate-50/20 border-l border-slate-200 py-1 transition-all duration-300">
                                            <?php foreach ( $sub_cats as $sub_cat ) : ?>
                                                <a href="<?php echo esc_url( get_category_link( $sub_cat->term_id ) ); ?>" class="flex items-center gap-2 py-2 px-3 text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-primary transition-all">
                                                    <span class="w-1 h-1 bg-slate-400"></span>
                                                    <?php echo esc_html( $sub_cat->name ); ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <a class="flex items-center gap-2 py-2 px-3 text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-primary transition-all" href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>">
                                        <span class="w-1.5 h-1.5 bg-primary/60"></span>
                                        <?php echo esc_html( $cat->name ); ?>
                                    </a>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
                
                <!-- About Us -->
                <?php
                $about_page = get_page_by_path('gioi-thieu');
                if ( $about_page ) :
                ?>
                    <a href="<?php echo esc_url( get_permalink( $about_page->ID ) ); ?>" class="flex items-center gap-3 py-2.5 px-3 font-bold text-sm sm:text-[11px] uppercase tracking-wider text-slate-700 bg-slate-50 border border-slate-100/60 hover:bg-primary hover:text-white group transition-all">
                        <?php echo techjournal_get_svg( 'info', 'w-5 h-5 text-primary group-hover:text-white fill-current' ); ?>
                        Giới thiệu
                    </a>
                <?php endif; ?>
                
                <!-- Contact -->
                <?php
                $contact_page = get_page_by_path('lien-he');
                if ( $contact_page ) :
                ?>
                    <a href="<?php echo esc_url( get_permalink( $contact_page->ID ) ); ?>" class="flex items-center gap-3 py-2.5 px-3 font-bold text-sm sm:text-[11px] uppercase tracking-wider text-slate-700 bg-slate-50 border border-slate-100/60 hover:bg-primary hover:text-white group transition-all">
                        <?php echo techjournal_get_svg( 'mail', 'w-5 h-5 text-primary group-hover:text-white fill-current' ); ?>
                        Liên hệ
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="w-full mt-section-gap bg-slate-900 border-t border-slate-800 text-slate-300" role="contentinfo">
    <div class="max-w-container-max mx-auto px-6 py-16 grid grid-cols-1 md:grid-cols-12 gap-12">
        <!-- Brand Info (Chuẩn SEO) -->
        <div class="col-span-full md:col-span-4 lg:col-span-5 space-y-5">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="block hover:opacity-90 active:scale-95 transition-all">
                <!-- <span class="text-2xl font-display font-black text-white tracking-tighter hover:text-primary">
                    Tech<span class="text-primary">Blog</span>
                </span>
                <span class="sr-only"><?php bloginfo( 'name' ); ?></span> -->
                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/Logo-TechBlog-footer.png' ); ?>" alt="<?php bloginfo( 'name' ); ?> Logo" class="h-12 w-auto block" style="clip-path: inset(1px);" />
            </a>
            <p class="text-slate-300 text-sm leading-relaxed max-w-sm">
                Nơi mà bạn có thể tìm thấy những kiến thức, thủ thuật hữu ích về công nghệ mà nhà trường không giảng dạy cho bạn biết.
                <!-- <?php 
                $site_desc = get_bloginfo( 'description' );
                if ( empty( $site_desc ) || $site_desc === 'Một trang web sử dụng WordPress' || $site_desc === 'Just another WordPress site' ) {
                    $site_desc = 'Kênh thông tin công nghệ hàng đầu, chuyên cập nhật các tin tức công nghệ mới nhất, đánh giá chi tiết thiết bị, kiến thức lập trình và giải pháp phần mềm hiện đại.';
                }
                echo esc_html( $site_desc ); 
                ?> -->
            </p>
            <!-- Social profiles - Flattened -->
            <div class="flex gap-3 pt-2">
                <a href="https://www.facebook.com/TechBlog.contact/" target="_blank" rel="noopener noreferrer" class="w-9 h-9 bg-slate-800 border border-slate-700/60 hover:bg-primary hover:border-primary text-slate-300 hover:text-white flex items-center justify-center transition-all duration-300 active:scale-95" title="Facebook">
                    <?php echo techjournal_get_svg( 'facebook', 'w-[15px] h-[15px]' ); ?>
                </a>
                <a href="https://t.me/ngovantoanttb" target="_blank" rel="noopener noreferrer" class="w-9 h-9 bg-slate-800 border border-slate-700/60 hover:bg-primary hover:border-primary text-slate-300 hover:text-white flex items-center justify-center transition-all duration-300 active:scale-95" title="Telegram">
                    <?php echo techjournal_get_svg( 'telegram', 'w-[16px] h-[16px]' ); ?>
                </a>
                <a href="mailto:<?php echo antispambot( get_option( 'admin_email' ) ); ?>" class="w-9 h-9 bg-slate-800 border border-slate-700/60 hover:bg-primary hover:border-primary text-slate-300 hover:text-white flex items-center justify-center transition-all duration-300 active:scale-95" title="Email liên hệ">
                    <?php echo techjournal_get_svg( 'email', 'w-[17px] h-[17px]' ); ?>
                </a>
            </div>
        </div>
        
        <!-- Top categories - Flattened -->
        <div class="col-span-full md:col-span-4 lg:col-span-4 space-y-4">
            <span class="font-display text-sm sm:text-xs font-black uppercase tracking-wider text-slate-200 block border-b border-slate-800 pb-2.5">Chủ đề chính</span>
            <nav class="grid grid-cols-2 gap-x-4 gap-y-2.5 text-sm sm:text-[13px]" aria-label="Footer Categories Links">
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
                    'parent'  => 0, // chỉ lấy danh mục cha
                    'orderby' => 'count',
                    'order'   => 'DESC',
                    'exclude' => $exclude_ids,
                    'hide_empty' => false // Hiển thị cả chuyên mục chưa có bài viết để lấy ra toàn bộ chủ đề
                ) );
                if ( ! empty( $footer_cats ) ) {
                    foreach ( $footer_cats as $cat ) {
                        echo '<a class="text-slate-300 hover:text-primary transition-colors flex items-center gap-1.5" href="' . esc_url( get_category_link( $cat->term_id ) ) . '">
                            <span class="w-1.5 h-1.5 bg-slate-850"></span>' . esc_html( $cat->name ) . '
                        </a>';
                    }
                }
                ?>
            </nav>
        </div>

        <!-- Resources / Quick Links -->
        <div class="col-span-full md:col-span-4 lg:col-span-3 space-y-4">
            <span class="font-display text-sm sm:text-xs font-black uppercase tracking-wider text-slate-200 block border-b border-slate-800 pb-2.5">Các trang khác</span>
            <nav class="flex flex-col gap-2.5 text-sm sm:text-[13px]" aria-label="Footer Company Links">
                <?php
                $about_page = get_page_by_path('gioi-thieu');
                $contact_page = get_page_by_path('lien-he');
                $privacy_page = get_page_by_path('chinh-sach-bao-mat');
                
                if ( $about_page ) :  
                ?>
                    <a class="text-slate-300 hover:text-primary transition-colors flex items-center gap-1.5" href="<?php echo esc_url( get_permalink( $about_page->ID ) ); ?>">
                        <span class="w-1.5 h-1.5 bg-slate-850"></span>Giới thiệu
                    </a>
                <?php endif; ?>
                
                <?php if ( $contact_page ) : ?>
                    <a class="text-slate-300 hover:text-primary transition-colors flex items-center gap-1.5" href="<?php echo esc_url( get_permalink( $contact_page->ID ) ); ?>">
                        <span class="w-1.5 h-1.5 bg-slate-850"></span>Liên hệ
                    </a>
                <?php endif; ?>
                
                <?php 
                $privacy_url = get_privacy_policy_url();
                if ( ! empty( $privacy_url ) ) : 
                ?>
                    <a class="text-slate-300 hover:text-primary transition-colors flex items-center gap-1.5" href="<?php echo esc_url( $privacy_url ); ?>">
                        <span class="w-1.5 h-1.5 bg-slate-850"></span>Chính sách bảo mật
                    </a>
                <?php elseif ( $privacy_page ) : ?>
                    <a class="text-slate-300 hover:text-primary transition-colors flex items-center gap-1.5" href="<?php echo esc_url( get_permalink( $privacy_page->ID ) ); ?>">
                        <span class="w-1.5 h-1.5 bg-slate-850"></span>Chính sách bảo mật
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
    
    <!-- Bottom line -->
    <div class="max-w-container-max mx-auto px-6 py-8 border-t border-slate-800/60 flex justify-center items-center text-slate-300 text-xs sm:text-[11px]">
        <span class="flex items-center gap-1.5">&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?> by <span class="hover:text-primary transition-colors">ngovantoanttb</span></span>
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
                content.classList.remove('-translate-y-full');
            }, 10);
        } else {
            content.classList.add('-translate-y-full');
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

    // Toggle Mobile Accordion Submenu
    function toggleMobileSubmenu() {
        const submenu = document.getElementById('mobile-submenu-cats');
        const arrow = document.getElementById('submenu-arrow');
        if (submenu.classList.contains('hidden')) {
            submenu.classList.remove('hidden');
            arrow.classList.add('rotate-180');
        } else {
            submenu.classList.add('hidden');
            arrow.classList.remove('rotate-180');
        }
    }

    // Toggle Mobile Sub-category Submenu
    function toggleMobileSubCatSubmenu(catId) {
        const submenu = document.getElementById('mobile-subcat-cats-' + catId);
        const btn = document.getElementById('subcat-arrow-btn-' + catId);
        if (submenu && btn) {
            if (submenu.classList.contains('hidden')) {
                submenu.classList.remove('hidden');
                btn.classList.add('rotate-180');
            } else {
                submenu.classList.add('hidden');
                btn.classList.remove('rotate-180');
            }
        }
    }

    // Elegant hover effect for article cards
    const cards = document.querySelectorAll('article');
    cards.forEach(card => {
        card.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
    });
</script>

<!-- Back to Top Button (Floating Zero Border-Radius Red Theme with Font Awesome SVG) -->
<button class="back-to-top fixed bottom-10 lg:bottom-4 right-6 z-50 bg-primary text-white w-12 h-12 flex items-center justify-center rounded-none shadow-md hover:bg-primary/80 transition-all cursor-pointer duration-300 active:scale-95" id="backToTop" style="display: none;" aria-label="Quay lại đầu trang">
    <?php echo techjournal_get_svg( 'back-to-top', 'w-5 h-5 fill-white' ); ?>
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
        }, { passive: true });

        btn.addEventListener("click", function (e) {
          e.preventDefault();
          window.scrollTo({
            top: 0,
            behavior: "smooth"
          });
        });
      }
    });
</script>
</body>
</html>
