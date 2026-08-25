<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống đang bảo trì - <?php bloginfo( 'name' ); ?></title>
    
    <!-- Prefetch and load fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <!-- Static Theme CSS -->
    <link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() . '/css/main.min.css' ); ?>">
    
    <style>
        @keyframes spin-slow {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes spin-reverse-slow {
            0% { transform: rotate(360deg); }
            100% { transform: rotate(0deg); }
        }
        .animate-spin-slow {
            animation: spin-slow 8s linear infinite;
        }
        .animate-spin-reverse-slow {
            animation: spin-reverse-slow 6s linear infinite;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-white font-sans antialiased">

<main class="w-full max-w-4xl px-6 py-12 flex flex-col items-center justify-center text-center">
    <!-- Animated Gears Illustration -->
    <div class="flex justify-center items-center gap-2 mb-8 h-24">
        <!-- Large Gear -->
        <svg class="w-16 h-16 md:w-20 md:h-20 text-slate-400 animate-spin-slow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <!-- Small Gear -->
        <svg class="w-10 h-10 md:w-12 md:h-12 text-yellow-500 animate-spin-reverse-slow -mt-6 -ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
    </div>

    <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 mb-4 tracking-tight">
        Hệ thống đang bảo trì
    </h1>
    
    <div class="">
        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs md:text-sm font-semibold bg-yellow-100 text-yellow-800">
            <span class="w-2 h-2 mr-2 bg-yellow-500 rounded-full animate-pulse"></span>
            Đang nâng cấp hệ thống
        </span>
    </div>

    <p class="pt-4 text-slate-600 text-base md:text-lg leading-relaxed max-w-xl mx-auto mb-8">
        <?php echo esc_html( get_option( 'techblog_maintenance_message', 'Hệ thống đang được nâng cấp để mang lại trải nghiệm tốt nhất cho bạn. Chúng tôi sẽ quay trở lại sớm!' ) ); ?>
    </p>


    <p class="text-slate-400 text-xs mt-16 uppercase tracking-wider font-bold">
        &copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>
    </p>
</main>

</body>
</html>
