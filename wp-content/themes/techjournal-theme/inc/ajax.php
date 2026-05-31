<?php
/**
 * TechJournal AJAX Handlers and Client-Side Script Injections
 *
 * @package TechJournal
 * @since 1.0.0
 */

// 1. Server-Side AJAX Load More Posts handler (Supports search, archive, category, and standard list layout grids)
function techblog_load_more_posts() {
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    $cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 3, // strictly load 3 posts each click
        'offset'         => $offset,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => 'publish'
    );
    
    if ( $cat_id > 0 ) {
        $args['cat'] = $cat_id;
    }
    
    if ( ! empty( $search ) ) {
        $args['s'] = $search;
    }
    
    $query = new WP_Query( $args );
    
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            
            // Format output markup based on query context (Search/Category/Archive = Flex Row, Homepage = Grid Card)
            if ( ! empty( $search ) || $cat_id > 0 ) {
                get_template_part( 'template-parts/content-card' );
            } else {
                get_template_part( 'template-parts/content-grid' );
            }
        }
        wp_reset_postdata();
    }
    wp_die();
}
add_action( 'wp_ajax_techblog_load_more', 'techblog_load_more_posts' );
add_action( 'wp_ajax_nopriv_techblog_load_more', 'techblog_load_more_posts' );

// 2. Premium Client-side Javascript AJAX orchestrator script with Skeleton Loader and Fade-in Image loading
function techblog_load_more_js_footer() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('techblog-load-more-btn');
            if (btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const spinner = document.getElementById('load-more-spinner');
                    const page = parseInt(btn.getAttribute('data-page'), 10);
                    const catId = parseInt(btn.getAttribute('data-cat-id'), 10);
                    const search = btn.getAttribute('data-search') || '';
                    
                    btn.classList.add('opacity-75', 'pointer-events-none');
                    if (spinner) spinner.classList.remove('hidden');
                    
                    const grid = document.getElementById('techblog-post-grid');
                    if (!grid) return;
                    
                    // Create high-fidelity Skeleton cards based on layout context
                    const isGrid = !(search.trim() !== '' || catId > 0);
                    let skeletonsHTML = '';
                    
                    for (let i = 0; i < 3; i++) {
                        if (isGrid) {
                            skeletonsHTML += `
                                <div class="techblog-skeleton-card animate-pulse bg-white border border-slate-100 p-4 flex flex-col gap-3" style="border-radius: 0;">
                                    <div class="aspect-[16/10] bg-slate-200 w-full" style="border-radius: 0;"></div>
                                    <div class="h-3 bg-slate-200 w-1/4" style="border-radius: 0;"></div>
                                    <div class="h-5 bg-slate-200 w-5/6" style="border-radius: 0;"></div>
                                    <div class="h-3 bg-slate-200 w-1/2" style="border-radius: 0;"></div>
                                </div>
                            `;
                        } else {
                            skeletonsHTML += `
                                <div class="techblog-skeleton-card animate-pulse flex flex-col sm:flex-row gap-5 p-5 bg-white border border-slate-100/80 shadow-sm" style="border-radius: 0;">
                                    <div class="w-full sm:w-[220px] h-[140px] bg-slate-200 shrink-0" style="border-radius: 0;"></div>
                                    <div class="flex-grow space-y-3.5 py-1">
                                        <div class="h-3 bg-slate-200 w-1/6" style="border-radius: 0;"></div>
                                        <div class="h-5 bg-slate-200 w-4/5" style="border-radius: 0;"></div>
                                        <div class="h-3 bg-slate-200 w-1/3" style="border-radius: 0;"></div>
                                        <div class="h-4 bg-slate-200 w-full" style="border-radius: 0;"></div>
                                    </div>
                                </div>
                            `;
                        }
                    }
                    
                    // Inject skeletons with subtle transition
                    const skeletonsWrapper = document.createElement('div');
                    skeletonsWrapper.id = 'techblog-skeletons-wrapper';
                    skeletonsWrapper.className = isGrid ? 'grid grid-cols-1 sm:grid-cols-3 gap-6 w-full col-span-full' : 'flex flex-col gap-6 w-full';
                    skeletonsWrapper.innerHTML = skeletonsHTML;
                    skeletonsWrapper.style.opacity = '0';
                    skeletonsWrapper.style.transition = 'opacity 300ms ease';
                    grid.appendChild(skeletonsWrapper);
                    
                    // Force reflow and show skeletons
                    setTimeout(() => {
                        skeletonsWrapper.style.opacity = '1';
                    }, 20);
                    
                    // Initial load was 9 posts, subsequently load 3 posts each click
                    const offset = 9 + (page - 1) * 3;
                    
                    const formData = new FormData();
                    formData.append('action', 'techblog_load_more');
                    formData.append('offset', offset);
                    formData.append('cat_id', catId);
                    formData.append('search', search);
                    
                    // Synthetic premium delay to show off beautiful skeletons
                    const startTime = Date.now();
                    
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(html => {
                        const elapsed = Date.now() - startTime;
                        const remainingDelay = Math.max(0, 450 - elapsed);
                        
                        setTimeout(() => {
                            // Fade out skeletons
                            skeletonsWrapper.style.opacity = '0';
                            
                            setTimeout(() => {
                                skeletonsWrapper.remove();
                                
                                if (spinner) spinner.classList.add('hidden');
                                btn.classList.remove('opacity-75', 'pointer-events-none');
                                
                                if (html.trim() !== '') {
                                    // Create temp wrapper element to convert text to HTML nodes
                                    const tempDiv = document.createElement('div');
                                    tempDiv.innerHTML = html;
                                    const newCards = Array.from(tempDiv.children);
                                    
                                    newCards.forEach(card => {
                                        card.style.opacity = '0';
                                        card.style.transform = 'translateY(15px)';
                                        card.style.transition = 'all 450ms cubic-bezier(0.25, 1, 0.5, 1)';
                                        grid.appendChild(card);
                                        
                                        // Add fade-in for lazy-loaded images in cards
                                        const cardImgs = card.querySelectorAll('img');
                                        cardImgs.forEach(img => {
                                            img.style.opacity = '0';
                                            img.style.transition = 'opacity 500ms ease';
                                            img.addEventListener('load', function() {
                                                img.style.opacity = '1';
                                            });
                                            // Handle cached images
                                            if (img.complete) {
                                                img.style.opacity = '1';
                                            }
                                        });
                                        
                                        // Trigger layout reflow & trigger CSS entry animation
                                        setTimeout(() => {
                                            card.style.opacity = '1';
                                            card.style.transform = 'translateY(0)';
                                        }, 50);
                                    });
                                    
                                    // Advance to next paginated load offset
                                    btn.setAttribute('data-page', page + 1);
                                } else {
                                    // No more posts found
                                    btn.textContent = 'HẾT BÀI VIẾT';
                                    btn.classList.add('bg-slate-400', 'hover:bg-slate-400', 'cursor-not-allowed');
                                    setTimeout(() => {
                                        btn.style.display = 'none';
                                    }, 1500);
                                }
                            }, 200);
                        }, remainingDelay);
                    })
                    .catch(error => {
                        console.error('TechBlog AJAX Load More failed:', error);
                        skeletonsWrapper.remove();
                        if (spinner) spinner.classList.add('hidden');
                        btn.classList.remove('opacity-75', 'pointer-events-none');
                    });
                });
            }
        });
    </script>
    <?php
}
add_action( 'wp_footer', 'techblog_load_more_js_footer', 100 );

// 3. AJAX Handler for Contact Form Submission with Custom Database Logging
function techblog_handle_contact_submission() {
    // 1. Verify Nonce
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'techblog_contact_nonce' ) ) {
        wp_send_json_error( array( 'message' => esc_html__( 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang.', 'techjournal' ) ) );
    }
    
    // 2. Validate Inputs
    $name    = isset( $_POST['c_name'] ) ? sanitize_text_field( $_POST['c_name'] ) : '';
    $email   = isset( $_POST['c_email'] ) ? sanitize_email( $_POST['c_email'] ) : '';
    $subject = isset( $_POST['c_subject'] ) ? sanitize_text_field( $_POST['c_subject'] ) : '';
    $message = isset( $_POST['c_message'] ) ? sanitize_textarea_field( $_POST['c_message'] ) : '';
    
    if ( empty( $name ) || empty( $email ) || empty( $message ) ) {
        wp_send_json_error( array( 'message' => esc_html__( 'Vui lòng điền đầy đủ các trường bắt buộc.', 'techjournal' ) ) );
    }
    
    if ( ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => esc_html__( 'Địa chỉ email không hợp lệ.', 'techjournal' ) ) );
    }
    
    // 3. Log Submission into Isolated Custom Database Table 'wp_techblog_contacts'
    global $wpdb;
    $table_name = $wpdb->prefix . 'techblog_contacts';
    $db_inserted = $wpdb->insert(
        $table_name,
        array(
            'name'       => $name,
            'email'      => $email,
            'subject'    => $subject,
            'message'    => $message,
            'status'     => 'unread',
            'created_at' => current_time( 'mysql' ),
        )
    );
    
    // 4. Construct Email Notification
    $to = get_option( 'admin_email' );
    $email_subject = 'Liên hệ mới từ ' . $name;
    if ( ! empty( $subject ) ) {
        $email_subject .= ' - ' . $subject;
    }
    
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $name . ' <' . $to . '>', 
        'Reply-To: ' . $name . ' <' . $email . '>'
    );
    
    $body  = '<h2>Bạn nhận được một liên hệ mới từ website TechBlog:</h2>';
    $body .= '<p><strong>Họ tên:</strong> ' . esc_html( $name ) . '</p>';
    $body .= '<p><strong>Email:</strong> ' . esc_html( $email ) . '</p>';
    $body .= '<p><strong>Tiêu đề:</strong> ' . esc_html( $subject ) . '</p>';
    $body .= '<p><strong>Nội dung:</strong></p>';
    $body .= '<div style="background:#f4f4f5; padding:15px; border-left:4px solid #2563eb; margin:15px 0; font-style:italic; font-size:14px; line-height:1.6;">' . nl2br( esc_html( $message ) ) . '</div>';
    
    // 5. Send Email
    $sent = wp_mail( $to, $email_subject, $body, $headers );
    
    if ( $db_inserted ) {
        wp_send_json_success( array( 'message' => esc_html__( 'Liên hệ đã được gửi thành công.', 'techjournal' ) ) );
    } else {
        wp_send_json_error( array( 'message' => esc_html__( 'Không thể gửi liên hệ. Vui lòng thử lại.', 'techjournal' ) ) );
    }
}
add_action( 'wp_ajax_techblog_submit_contact', 'techblog_handle_contact_submission' );
add_action( 'wp_ajax_nopriv_techblog_submit_contact', 'techblog_handle_contact_submission' );
