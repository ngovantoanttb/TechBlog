<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo tạm dừng hoạt động - <?php bloginfo( 'name' ); ?></title>
    
    <!-- Prefetch and load fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <!-- TailWind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Be Vietnam Pro"', 'sans-serif'],
                    },
                    colors: {
                        primary: '#2563eb', // Royal Blue
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center bg-white font-sans antialiased">

<main class="w-full max-w-4xl px-6 py-12 flex flex-col items-center justify-center text-center">
    <!-- Lock Illustration -->
    <div class="flex justify-center items-center mb-8 h-24">
        <svg class="w-16 h-16 md:w-20 md:h-20 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
        </svg>
    </div>

    <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 mb-4 tracking-tight">
        Thông báo tạm dừng hoạt động
    </h1>

    <div class="">
        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs md:text-sm font-semibold bg-slate-100 text-slate-800">
            <span class="w-2 h-2 mr-2 bg-slate-500 rounded-full"></span>
            Tạm đóng liên kết
        </span>
    </div>

    <p class="pt-4 text-slate-600 text-base md:text-lg leading-relaxed max-w-xl mx-auto mb-8">
        <?php echo esc_html( get_option( 'techblog_suspended_message', 'Trang web này hiện đã tạm thời dừng hoạt động theo yêu cầu của ban quản trị.' ) ); ?>
    </p>


    <p class="text-slate-400 text-xs mt-16 uppercase tracking-wider font-bold">
        &copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>
    </p>
</main>

</body>
</html>
