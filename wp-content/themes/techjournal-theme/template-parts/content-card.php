<?php
/**
 * Template part for displaying posts in lists (Standard TechBlog card style)
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
<article class="group bg-white flex flex-col sm:flex-row gap-6 cursor-pointer transition-all duration-300 border-b border-slate-100/80 last:border-0 last:mb-0">
    <!-- Image container on Left -->
    <a href="<?php the_permalink(); ?>" class="w-full sm:w-[280px] md:w-[320px] aspect-[16/10] sm:aspect-[16/9] md:aspect-[1.5/1] overflow-hidden block relative bg-slate-950 shrink-0 shadow-sm">
        <img class="w-full h-full object-cover transition-transform duration-750 group-hover:scale-102 opacity-95 group-hover:opacity-100" src="<?php echo esc_url($post_image); ?>" alt="<?php the_title_attribute(); ?>" />
        <?php if ($category_to_show) : ?>
            <span class="absolute top-3 left-3 bg-red-600 text-white text-[9px] font-black uppercase px-2.5 py-1 tracking-widest shadow-sm">
                <?php echo esc_html($category_to_show->name); ?>
            </span>
        <?php endif; ?>
    </a>
    
    <!-- Text Content on Right -->
    <div class="flex flex-col flex-grow min-w-0 pt-1 sm:pt-2 pr-0 sm:pr-4 md:pr-6 pb-5 sm:pb-6">
        <h3 class="font-display text-base sm:text-lg md:text-[20px] text-slate-805 hover:text-primary transition-colors font-bold leading-snug mb-3 break-words">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        
        <!-- Meta Info Line -->
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5 text-[10px] sm:text-[11px] text-slate-500 font-bold uppercase tracking-wider mb-4">
            <span>BY <span class="text-primary font-black"><?php echo (strcasecmp(get_the_author(), 'admin') === 0) ? 'Admin TechBlog' : get_the_author(); ?></span></span>
            <span>•</span>
            <span class="flex items-center gap-1">
                <?php echo techjournal_get_svg( 'clock', 'w-4 h-4 text-slate-500 fill-current' ); ?>
                <?php echo get_the_date('d/m/Y'); ?>
            </span>
            <span>•</span>
            <span class="flex items-center gap-1">
                <?php echo techjournal_get_svg( 'comment', 'w-4 h-4 text-slate-500 fill-current' ); ?>
                <?php echo get_comments_number(); ?>
            </span>
        </div>
        
        <!-- Excerpt -->
        <p class="text-slate-600 text-xs sm:text-sm leading-relaxed mb-5">
            <?php echo wp_trim_words( get_the_excerpt(), 25, '...' ); ?>
        </p>
        
        <!-- READ MORE Button -->
        <a href="<?php the_permalink(); ?>" class="border border-slate-200 bg-white hover:bg-red-600 hover:text-white hover:border-red-600 text-[10px] font-bold uppercase tracking-wider text-slate-700 px-6 py-3 w-fit active:scale-95 transition-all mt-1 shadow-sm inline-flex items-center justify-center min-h-[48px]">
            READ MORE
        </a>
    </div>
</article>
