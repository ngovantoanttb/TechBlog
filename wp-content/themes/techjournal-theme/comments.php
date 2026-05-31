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

    <!-- Comment Form -->
    <div class="bg-white p-6 border border-slate-200 shadow-sm mt-8">
        <?php 
        // Display custom comment form configured in functions.php
        comment_form(); 
        ?>
    </div>

</div>
