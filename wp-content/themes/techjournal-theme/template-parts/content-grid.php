<?php
/**
 * Template part for displaying posts in a grid layout (Homepage style)
 *
 * @package TechJournal
 * @since 1.0.0
 */

$post_image = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
if ( ! $post_image ) {
    $post_image = techblog_get_placeholder_img();
}

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
?>
<article class="group flex flex-col cursor-pointer transition-all duration-300">
    <!-- Image container with absolute Category Badge overlay -->
    <a href="<?php the_permalink(); ?>" class="aspect-[16/10] overflow-hidden block relative bg-slate-950 shrink-0">
        <img class="w-full h-full object-cover transition-transform duration-750 group-hover:scale-102 opacity-95 group-hover:opacity-100" src="<?php echo esc_url($post_image); ?>" alt="<?php the_title_attribute(); ?>" />
        <?php if ($category_to_show) : ?>
            <span class="absolute bottom-3 left-3 bg-red-600 text-white text-[10px] sm:text-[9px] font-black uppercase px-2.5 py-1 tracking-widest shadow-sm">
                <?php echo esc_html($category_to_show->name); ?>
            </span>
        <?php endif; ?>
    </a>
    <div class="pt-4 flex flex-col flex-grow">
        <h3 class="font-display text-base text-slate-805 group-hover:text-primary transition-colors font-bold leading-snug mb-3.5 break-words">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        
        <!-- Date and Clock at the bottom -->
        <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium mt-auto">
            <?php echo techjournal_get_svg( 'clock', 'w-4 h-4 text-slate-500 fill-current' ); ?>
            <span><?php echo get_the_date('d/m/Y'); ?></span>
        </div>
    </div>
</article>
