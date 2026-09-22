<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>EngixCare Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9ff; }
        .glass-nav { backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        .soft-card-shadow { shadow: 0px 4px 20px rgba(31, 41, 55, 0.05); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #bbcabf; border-radius: 10px; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>
<body class="text-on-surface">
    @yield('content')

    @stack('scripts')

    <script>
        document.querySelectorAll('.group').forEach(el => {
            el.addEventListener('mouseenter', () => {
                el.style.transform = 'translateY(-2px)';
            });
            el.addEventListener('mouseleave', () => {
                el.style.transform = 'translateY(0)';
            });
        });

        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (header && window.scrollY > 10) {
                header.classList.add('shadow-md');
                header.style.backgroundColor = 'rgba(248, 249, 255, 0.95)';
            } else if (header) {
                header.classList.remove('shadow-md');
                header.style.backgroundColor = 'rgba(248, 249, 255, 0.8)';
            }
        });
    </script>
</body>
</html>
