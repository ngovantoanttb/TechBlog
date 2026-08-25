<?php
/**
 * The template for displaying standalone 404 pages (Not Found) - TechBlog Style
 *
 * @package TechJournal
 * @since 1.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php esc_html_e( 'Ủa? Đi lạc rồi bạn ơi! - 404 TechBlog', 'techjournal' ); ?></title>
    
    <!-- Google Fonts (Premium Be Vietnam Pro Font) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <?php wp_head(); ?>
</head>
<body <?php body_class( 'min-h-screen flex items-center justify-center bg-white' ); ?>>
<?php wp_body_open(); ?>

<!-- Main Standalone Content (Custom Layout with translations & humor) -->
<main class="w-full flex justify-center">
    <div class="lg:px-24 lg:py-24 md:py-20 md:px-44 px-4 py-24 items-center flex justify-center flex-col-reverse lg:flex-row md:gap-28 gap-16">
        <div class="xl:pt-24 w-full lg:w-1/2 relative pb-12 lg:pb-0">
            <div class="relative">
                <div class="absolute z-10">
                    <div class="max-w-md">
                        <h1 class="my-2 text-slate-800 font-extrabold text-2xl leading-snug">
                            Ối giời ơi! Bạn vừa lạc vào vùng đất "Không Có Gì" của vũ trụ rồi!
                        </h1>
                        <p class="my-3 text-slate-600 text-[13px] leading-relaxed">
                            Đường link này đã bốc hơi nhanh hơn cả tiền lương của bạn! Đừng hoang mang, hố đen này tuy sâu nhưng không hút mất người yêu cũ của bạn đâu. Nhấp nút thần kỳ dưới đây để lên phi thuyền về lại bờ an toàn nhé!
                        </p>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-block sm:w-full lg:w-auto my-3 border rounded-none py-4 px-8 text-center bg-primary text-white font-bold text-[11px] uppercase tracking-wider hover:bg-blue-700 transition-colors shadow-lg">
                            Lên phi thuyền về Trái Đất gấp!
                        </a>
                    </div>
                </div>
                <!-- Faint background layout lines -->
                <div class="pt-32 opacity-20 sm:opacity-100">
                    <img src="https://i.ibb.co/G9DC8S0/404-2.png" alt="Decoration Grid" />
                </div>
            </div>
        </div>
        <div class="w-full lg:w-1/2 flex justify-center">
            <img src="https://i.ibb.co/ck1SGFJ/Group.png" alt="404 Illustration" class="max-w-full h-auto" />
        </div>
    </div>
</main>

<?php wp_footer(); ?>
</body>
</html>
