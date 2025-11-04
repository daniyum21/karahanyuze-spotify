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
    <!-- Global Error Message Notification -->
    <div id="error-notification" class="fixed top-4 right-4 z-50 max-w-md hidden" style="display: none;">
        <div class="bg-red-600 border border-red-700 rounded-lg shadow-lg p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-white flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <div class="flex-1">
                <p id="error-notification-message" class="text-white text-sm font-medium"></p>
            </div>
            <button onclick="hideErrorNotification()" class="text-white hover:text-red-200 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>

    <script>
        // Global error notification functions
        function showErrorNotification(message) {
            const notification = document.getElementById('error-notification');
            const messageEl = document.getElementById('error-notification-message');
            if (notification && messageEl) {
                messageEl.textContent = message;
                notification.classList.remove('hidden');
                notification.style.display = 'block';
                
                // Auto-hide after 5 seconds
                setTimeout(function() {
                    hideErrorNotification();
                }, 5000);
            }
        }
        
        function hideErrorNotification() {
            const notification = document.getElementById('error-notification');
            if (notification) {
                notification.classList.add('hidden');
                notification.style.display = 'none';
            }
        }
        
        // Make it globally available
        window.showErrorNotification = showErrorNotification;
        window.hideErrorNotification = hideErrorNotification;
    </script>

    @include('components.header')
    
    <main>
        @yield('content')
    </main>

    @include('components.music-player')
</body>
</html>

