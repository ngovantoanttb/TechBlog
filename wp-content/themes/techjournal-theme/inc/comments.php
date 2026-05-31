<?php
/**
 * TechJournal Comments Layout and Custom Filtering
 *
 * @package TechJournal
 * @since 1.0.0
 */

// 1. Custom Premium Comment Formatting Callback
if ( ! function_exists( 'techjournal_custom_comment_callback' ) ) {
    function techjournal_custom_comment_callback( $comment, $args, $depth ) {
        $GLOBALS['comment'] = $comment;
        ?>
        <li <?php comment_class( 'border-b border-slate-100 last:border-0 pb-6 mb-6 list-none' ); ?> id="li-comment-<?php comment_ID(); ?>">
            <div id="comment-<?php comment_ID(); ?>" class="flex gap-4">
                <div class="shrink-0">
                    <?php
                    $is_admin_commenter = false;
                    $commenter_email = $comment->comment_author_email;
                    
                    if ( $comment->user_id ) {
                        $user = get_userdata( $comment->user_id );
                        if ( $user && in_array( 'administrator', (array) $user->roles ) ) {
                            $is_admin_commenter = true;
                            $commenter_email = $user->user_email;
                        }
                    } else if ( ! empty( $commenter_email ) ) {
                        $admin_email = get_option( 'admin_email' );
                        if ( strtolower( trim( $commenter_email ) ) === strtolower( trim( $admin_email ) ) ) {
                            $is_admin_commenter = true;
                        }
                    }
                    
                    if ( $is_admin_commenter ) {
                        if ( techjournal_has_gravatar( $commenter_email ) ) {
                            echo get_avatar( $comment, 40, '', '', array( 'class' => 'rounded-none w-10 h-10 object-cover' ) );
                        } else {
                            $logo_url = get_template_directory_uri() . '/assets/images/Logo-TechBlog.png';
                            ?>
                            <img src="<?php echo esc_url( $logo_url ); ?>" class="rounded-none w-10 h-10 object-contain block shrink-0 bg-slate-100 p-1" alt="Ảnh đại diện TechBlog" />
                            <?php
                        }
                    } else {
                        ?>
                        <img src="<?php echo get_template_directory_uri() ?>/assets/images/anony.jpg" class="rounded-none w-10 h-10 object-cover block shrink-0" alt="<?php echo esc_attr( get_comment_author() ); ?>" />
                        <?php
                    }
                    ?>
                </div>
                <div class="flex-grow">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="font-bold text-[12.5px] text-slate-805"><?php comment_author_link(); ?></span>
                        <span class="text-[10.5px] text-slate-400 font-bold">
                            <?php 
                            $comment_timestamp = get_comment_time( 'U', true );
                            $current_timestamp = time();
                            $time_diff = $current_timestamp - $comment_timestamp;
                            
                            if ( $time_diff >= -60 && $time_diff < 86400 ) {
                                if ( $time_diff < 60 ) {
                                    echo esc_html__( 'vừa xong', 'techjournal' );
                                } else {
                                    $relative = human_time_diff( $comment_timestamp, $current_timestamp );
                                    printf( esc_html__( '%s trước', 'techjournal' ), $relative );
                                }
                            } else {
                                echo get_comment_date('d/m/Y') . ' lúc ' . get_comment_time('H:i');
                            }
                            ?>
                        </span>
                    </div>
                    <div class="text-[13px] leading-relaxed text-slate-650 prose max-w-none">
                        <?php comment_text(); ?>
                    </div>
                    <div class="mt-3 flex items-center gap-4 text-[10px] font-bold uppercase tracking-wider">
                        <?php
                        comment_reply_link( array_merge( $args, array(
                            'reply_text' => esc_html__( 'Phản hồi', 'techjournal' ),
                            'depth'      => $depth,
                            'max_depth'  => $args['max_depth'],
                            'class'      => 'text-primary hover:underline'
                        ) ) );
                        ?>
                        <?php edit_comment_link( esc_html__( 'Sửa', 'techjournal' ), '<span class="text-slate-400">|</span> ', '' ); ?>
                    </div>
                </div>
            </div>
        </li>
        <?php
    }
}

// 2. Reorder and Filter Comment Form Fields (Tên -> Email -> Bình luận)
if ( ! function_exists( 'techjournal_reorder_comment_fields' ) ) {
    function techjournal_reorder_comment_fields( $fields ) {
        $new_fields = array();
        
        // Exact order required: Tên (author) -> Email (email) -> Bình luận (comment)
        // Completely omits url (Trang web) and cookies (consent checkbox)
        if ( isset( $fields['author'] ) ) {
            $new_fields['author'] = $fields['author'];
        }
        if ( isset( $fields['email'] ) ) {
            $new_fields['email'] = $fields['email'];
        }
        if ( isset( $fields['comment'] ) ) {
            $new_fields['comment'] = $fields['comment'];
        }
        
        return $new_fields;
    }
}
add_filter( 'comment_form_fields', 'techjournal_reorder_comment_fields' );
