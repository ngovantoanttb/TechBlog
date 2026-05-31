<?php
/**
 * The template for displaying all pages - TechBlog Premium Style
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
                    <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                    <span class="text-slate-600 truncate"><?php the_title(); ?></span>
                </nav>
                
                <!-- Page Title -->
                <h1 class="font-display text-2xl sm:text-3xl md:text-4xl text-slate-900 font-extrabold tracking-tight leading-tight mb-6 pb-4 border-b border-slate-100">
                    <?php the_title(); ?>
                </h1>
                
                <!-- Page Featured Image - Flattened -->
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="mb-8 border border-slate-100/50 aspect-[21/9] overflow-hidden">
                        <img alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover opacity-95" src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'large' ) ); ?>" />
                    </div>
                <?php endif; ?>
                
                <!-- Page Content Body -->
                <div class="prose max-w-none text-slate-600 text-[14.5px] leading-relaxed space-y-6 wp-entry-content">
                    <?php the_content(); ?>
                </div>
                
                <?php
            endwhile;
        endif;
        ?>
        
    </div>
</main>

<?php get_footer(); ?>
