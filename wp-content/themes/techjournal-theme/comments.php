<?php
/**
 * The template for displaying comments
 *
 * @package TechJournal
 * @since 1.0.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php if ( have_comments() ) : ?>
        <h3 class="font-headline-md text-headline-md text-on-surface mb-8 font-bold">
            <?php
            $comment_count = get_comments_number();
            if ( 1 === $comment_count ) {
                printf(
                    esc_html__( '1 Comment', 'techjournal' )
                );
            } else {
                printf(
                    esc_html( _n( '%s Comment', '%s Comments', $comment_count, 'techjournal' ) ),
                    number_format_i18n( $comment_count )
                );
            }
            ?>
        </h3>

        <!-- Comments List -->
        <ul class="space-y-6 mb-12">
            <?php
            wp_list_comments( array(
                'style'       => 'ul',
                'short_ping'  => true,
                'avatar_size' => 40,
                'callback'    => 'techjournal_custom_comment_callback'
            ) );
            ?>
        </ul>

        <?php
        // Comment navigation if pages exist
        if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
            ?>
            <nav class="navigation comment-navigation mb-8" role="navigation">
                <div class="nav-links flex gap-4 font-label-md text-label-md">
                    <div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'techjournal' ) ); ?></div>
                    <div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'techjournal' ) ); ?></div>
                </div>
            </nav>
            <?php
        endif;
        ?>

    <?php endif; // Check for have_comments(). ?>

    <?php
    // If comments are closed and there are comments, let's leave a little note.
    if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
        ?>
        <p class="no-comments font-label-md text-label-md text-on-surface-variant italic mb-6"><?php esc_html_e( 'Comments are closed.', 'techjournal' ); ?></p>
        <?php
    endif;
    ?>

    <!-- Comment Form (Aligned with TechBlog premium Flat UI style) -->
    <div class="mt-10 pt-8 border-t border-slate-100">
        <?php
        $commenter = wp_get_current_commenter();
        $req       = get_option( 'require_name_email' );
        $aria_req  = ( $req ? " aria-required='true'" : '' );

        $fields = array(
            'author' => '<div class="comment-form-author mb-4">
                <label for="author" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">' . esc_html__( 'Tên *', 'techjournal' ) . '</label>
                <input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' class="w-full bg-slate-50 border border-slate-200/80 px-4 py-2.5 text-[12.5px] text-slate-700 focus:outline-none focus:border-primary/80 focus:bg-white transition-all duration-300" />
            </div>',
            'email'  => '<div class="comment-form-email mb-4">
                <label for="email" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">' . esc_html__( 'Email *', 'techjournal' ) . '</label>
                <input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' class="w-full bg-slate-50 border border-slate-200/80 px-4 py-2.5 text-[12.5px] text-slate-700 focus:outline-none focus:border-primary/80 focus:bg-white transition-all duration-300" />
            </div>',
        );

        $comments_args = array(
            'fields'               => $fields,
            'comment_field'        => '<div class="comment-form-comment mb-5">
                <label for="comment" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">' . esc_html__( 'Bình luận *', 'techjournal' ) . '</label>
                <textarea id="comment" name="comment" cols="45" rows="5" aria-required="true" class="w-full bg-slate-50 border border-slate-200/80 px-4 py-3 text-[12.5px] text-slate-700 focus:outline-none focus:border-primary/80 focus:bg-white transition-all duration-300 resize-none h-32"></textarea>
            </div>',
            'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="bg-primary hover:bg-primary/95 text-white font-bold px-8 py-3 text-[11px] uppercase tracking-wider transition-all duration-300 shadow-md hover:shadow-lg active:scale-95 cursor-pointer inline-flex items-center gap-2">%4$s</button>',
            'submit_field'         => '<div class="form-submit mt-6">%1$s %2$s</div>',
            'title_reply'          => esc_html__( 'Gửi bình luận', 'techjournal' ),
            'title_reply_to'       => esc_html__( 'Trả lời %s', 'techjournal' ),
            'cancel_reply_link'    => esc_html__( 'Hủy phản hồi', 'techjournal' ),
            'title_reply_class'    => 'font-display text-sm font-black text-slate-800 uppercase tracking-tight pb-3 mb-5 relative anony-section-title',
            'comment_notes_before' => '<p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-6">' . esc_html__( 'Email của bạn sẽ không được hiển thị công khai. Các trường bắt buộc được đánh dấu *', 'techjournal' ) . '</p>',
        );

        comment_form( $comments_args );
        ?>
    </div>

</div>
