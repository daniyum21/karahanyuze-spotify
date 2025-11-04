<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Karahanyuze - Rwandan Music Heritage')</title>
    
    @if(config('services.google_analytics.id'))
    <!-- Google Analytics -->
    <script>
        // Initialize dataLayer before loading script
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        
        // Load GA script with error handling
        (function() {
            var script = document.createElement('script');
            script.async = true;
            script.src = 'https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.id') }}';
            script.onerror = function() {
                console.warn('Google Analytics script failed to load');
            };
            script.onload = function() {
                gtag('config', '{{ config('services.google_analytics.id') }}', {
                    'send_page_view': true,
                    'transport_type': 'beacon',
                    'anonymize_ip': true
                });
            };
            document.head.appendChild(script);
        })();
    </script>
    @endif
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-900 text-white min-h-screen">
    @include('components.header')
    
    <main>
        @yield('content')
    </main>

    @include('components.music-player')
</body>
</html>

