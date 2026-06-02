<?php
/**
 * Template Name: Contact Page (Trang Liên Hệ)
 *
 * The template for displaying the contact form and contact details - TechBlog Premium.
 *
 * @package TechJournal
 * @since 1.0.0
 */

get_header(); ?>

<main class="pt-6 sm:pt-8 pb-section-gap bg-background min-h-screen">
    <div class="max-w-3xl mx-auto bg-white p-6 sm:p-12 border border-slate-100/80 premium-shadow">
        
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) : the_post();
                ?>
                <!-- Page Breadcrumbs -->
                <nav class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-5" aria-label="Breadcrumb">
                    <a class="hover:text-primary transition-all" href="<?php echo esc_url( home_url( '/' ) ); ?>">Trang Chủ</a>
                    <?php echo techjournal_get_svg( 'chevron-right', 'w-3.5 h-3.5 text-slate-400 fill-current' ); ?>
                    <span class="text-slate-600 truncate"><?php the_title(); ?></span>
                </nav>
                
                <!-- Page Title -->
                <h1 class="font-display text-2xl sm:text-3xl md:text-4xl text-slate-900 font-extrabold tracking-tight leading-tight mb-6 pb-4 border-b border-slate-100">
                    <?php the_title(); ?>
                </h1>
                
                <!-- Page Content Body -->
                <div class="prose max-w-none text-slate-600 text-[14.5px] leading-relaxed mb-8 wp-entry-content">
                    <?php the_content(); ?>
                </div>

                <!-- Contact Form Wrapper -->
                <div id="techblog-contact-wrapper" class="border border-slate-100 p-6 sm:p-8 bg-slate-50/30">
                    
                    <form id="techblog-contact-form" class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Full Name -->
                            <div>
                                <label for="c_name" class="block font-display text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2">Họ và Tên <span class="text-red-500">*</span></label>
                                <input id="c_name" name="c_name" type="text" required class="w-full bg-white border border-slate-200 px-4 py-3 text-xs text-slate-800 focus:outline-none focus:border-primary transition-colors rounded-none placeholder-slate-400" placeholder="Nguyễn Văn A" />
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label for="c_email" class="block font-display text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2">Địa chỉ Email <span class="text-red-500">*</span></label>
                                <input id="c_email" name="c_email" type="email" required class="w-full bg-white border border-slate-200 px-4 py-3 text-xs text-slate-800 focus:outline-none focus:border-primary transition-colors rounded-none placeholder-slate-400" placeholder="name@example.com" />
                            </div>
                        </div>

                        <!-- Subject -->
                        <div>
                            <label for="c_subject" class="block font-display text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2">Tiêu đề liên hệ</label>
                            <input id="c_subject" name="c_subject" type="text" class="w-full bg-white border border-slate-200 px-4 py-3 text-xs text-slate-800 focus:outline-none focus:border-primary transition-colors rounded-none placeholder-slate-400" placeholder="Tôi muốn hợp tác quảng cáo..." />
                        </div>

                        <!-- Message -->
                        <div>
                            <label for="c_message" class="block font-display text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2">Nội dung liên hệ <span class="text-red-500">*</span></label>
                            <textarea id="c_message" name="c_message" rows="5" required class="w-full bg-white border border-slate-200 px-4 py-3 text-xs text-slate-800 focus:outline-none focus:border-primary transition-colors rounded-none placeholder-slate-400" placeholder="Nhập nội dung liên hệ của bạn tại đây..."></textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" id="c_submit_btn" class="bg-primary hover:bg-blue-500 text-white font-bold text-xs py-3 px-8 transition-all active:scale-[0.98] cursor-pointer rounded-none uppercase tracking-wider flex items-center gap-2">
                                <span>Gửi Liên Hệ</span>
                                <span id="c_submit_spinner" class="animate-spin hidden">
                                    <?php echo techjournal_get_svg( 'sync', 'w-4 h-4 fill-current' ); ?>
                                </span>
                            </button>
                        </div>
                    </form>

                    <!-- Success Message (Initially Hidden) -->
                    <div id="techblog-contact-success" class="hidden text-center py-10 px-4">
                        <div class="w-16 h-16 bg-green-50 text-green-500 border border-green-200 flex items-center justify-center mx-auto mb-5 rounded-none">
                            <?php echo techjournal_get_svg( 'done', 'w-8 h-8 text-green-500 fill-current' ); ?>
                        </div>
                        <h3 class="font-display text-lg font-black text-slate-850 uppercase tracking-tight mb-2">Gửi Liên Hệ Thành Công!</h3>
                        <p class="text-slate-500 text-xs sm:text-[13px] leading-relaxed max-w-md mx-auto">
                            Cảm ơn bạn đã liên hệ với **TechBlog**. Chúng tôi đã nhận được thông tin và sẽ phản hồi lại bạn qua email trong thời gian sớm nhất.
                        </p>
                    </div>

                    <!-- Error Message (Initially Hidden) -->
                    <div id="techblog-contact-error" class="hidden bg-red-50 border border-red-100 text-red-700 text-xs py-3 px-4 mb-4 text-center">
                        Đã có lỗi xảy ra. Vui lòng thử lại sau giây lát.
                    </div>

                </div>

                <!-- Contact Form AJAX Script -->
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.getElementById('techblog-contact-form');
                    const wrapper = document.getElementById('techblog-contact-wrapper');
                    const successDiv = document.getElementById('techblog-contact-success');
                    const errorDiv = document.getElementById('techblog-contact-error');
                    const submitBtn = document.getElementById('c_submit_btn');
                    const spinner = document.getElementById('c_submit_spinner');

                    if (!form) return;

                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        // Show loading state
                        submitBtn.setAttribute('disabled', 'true');
                        spinner.classList.remove('hidden');
                        errorDiv.classList.add('hidden');

                        const formData = new FormData(form);
                        formData.append('action', 'techblog_submit_contact');
                        formData.append('nonce', '<?php echo wp_create_nonce("techblog_contact_nonce"); ?>');

                        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Hide loading state
                            submitBtn.removeAttribute('disabled');
                            spinner.classList.add('hidden');

                            if (data.success) {
                                // Transition smoothly
                                form.style.display = 'none';
                                successDiv.classList.remove('hidden');
                            } else {
                                errorDiv.classList.remove('hidden');
                            }
                        })
                        .catch(err => {
                            submitBtn.removeAttribute('disabled');
                            spinner.classList.add('hidden');
                            errorDiv.classList.remove('hidden');
                        });
                    });
                });
                </script>
                
                <?php
            endwhile;
        endif;
        ?>
        
    </div>
</main>

<?php get_footer(); ?>
