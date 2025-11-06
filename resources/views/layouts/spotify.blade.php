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
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        var gaLoaded = false;
        var gaLoadTimeout = null;
        
        (function() {
            var script = document.createElement('script');
            script.async = true;
            script.src = 'https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.id') }}';
            
            gaLoadTimeout = setTimeout(function() {
                if (!gaLoaded) {
                    console.warn('Google Analytics script load timeout');
                    window.gtag = function() { return; };
                }
            }, 5000);
            
            script.onerror = function() {
                console.warn('Google Analytics script failed to load');
                clearTimeout(gaLoadTimeout);
                window.gtag = function() { return; };
            };
            
            script.onload = function() {
                gaLoaded = true;
                clearTimeout(gaLoadTimeout);
                try {
                    gtag('config', '{{ config('services.google_analytics.id') }}', {
                        'send_page_view': true,
                        'transport_type': 'beacon',
                        'anonymize_ip': true,
                        'page_path': window.location.pathname + window.location.search
                    });
                } catch (e) {
                    console.warn('Google Analytics configuration failed:', e);
                    window.gtag = function() { return; };
                }
            };
            
            document.head.appendChild(script);
        })();
        
        (function() {
            var originalGtag = window.gtag;
            window.gtag = function() {
                try {
                    if (typeof originalGtag === 'function') {
                        originalGtag.apply(this, arguments);
                    }
                } catch (e) {
                    console.warn('Google Analytics error (ignored):', e);
                }
            };
        })();
    </script>
    @endif
    
    @if(app()->environment('production'))
        @php
            $manifestPath = public_path('build/manifest.json');
            $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : null;
        @endphp
        @if($manifest)
            @if(isset($manifest['resources/css/app.css']['file']))
                <link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/css/app.css']['file']) }}">
            @endif
            @if(isset($manifest['resources/js/app.js']['file']))
                <script type="module" src="{{ asset('build/' . $manifest['resources/js/app.js']['file']) }}"></script>
            @endif
        @else
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-black text-white min-h-screen">
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
        function showErrorNotification(message) {
            const notification = document.getElementById('error-notification');
            const messageEl = document.getElementById('error-notification-message');
            if (notification && messageEl) {
                messageEl.textContent = message;
                notification.classList.remove('hidden');
                notification.style.display = 'block';
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
        
        window.showErrorNotification = showErrorNotification;
        window.hideErrorNotification = hideErrorNotification;
    </script>

    <div class="flex h-screen overflow-hidden">
        <!-- Left Sidebar -->
        <aside class="w-64 bg-black border-r border-zinc-800 flex-shrink-0 hidden md:flex flex-col">
            <!-- Logo -->
            <div class="p-6">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#1db954] flex items-center justify-center text-white font-bold text-lg">
                        K
                    </div>
                    <span class="text-white font-bold text-xl">Karahanyuze</span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="px-3 pb-6">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('home') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('home') ? 'bg-zinc-800 text-white' : 'text-zinc-400 hover:text-white hover:bg-zinc-900' }}">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.69l9 8.5V20h-2v-7.5l-7-6.61-7 6.61V20H3v-8.81l9-8.5z"/>
                            </svg>
                            <span class="font-medium">Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('indirimbo.search') }}" class="flex items-center gap-4 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('indirimbo.*') ? 'bg-zinc-800 text-white' : 'text-zinc-400 hover:text-white hover:bg-zinc-900' }}">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                            </svg>
                            <span class="font-medium">Search</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Your Library Section -->
            <div class="px-3 pb-6">
                <div class="bg-zinc-900/50 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-white font-bold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"/>
                            </svg>
                            <span>Your Library</span>
                        </h3>
                        <button class="text-zinc-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-3">
                        @auth
                        @if(Auth::user()->isAdmin())
                        <div class="bg-zinc-800/50 rounded-lg p-4 hover:bg-zinc-800 transition-colors">
                            <h4 class="text-white font-semibold mb-1">Create playlist</h4>
                            <p class="text-zinc-400 text-sm mb-3">Create a new playlist</p>
                            <a href="{{ route('admin.playlists.create') }}" class="inline-block px-4 py-2 bg-white text-black rounded-full text-sm font-bold hover:scale-105 transition-transform">Create playlist</a>
                        </div>
                        @endif
                        <div class="bg-zinc-800/50 rounded-lg p-4 hover:bg-zinc-800 transition-colors">
                            <h4 class="text-white font-semibold mb-1">Browse playlists</h4>
                            <p class="text-zinc-400 text-sm mb-3">Discover curated playlists</p>
                            <a href="{{ route('playlists.index') }}" class="inline-block px-4 py-2 bg-white text-black rounded-full text-sm font-bold hover:scale-105 transition-transform">Browse playlists</a>
                        </div>
                        @else
                        <div class="bg-zinc-800/50 rounded-lg p-4 hover:bg-zinc-800 transition-colors">
                            <h4 class="text-white font-semibold mb-1">Browse playlists</h4>
                            <p class="text-zinc-400 text-sm mb-3">Discover curated playlists</p>
                            <a href="{{ route('playlists.index') }}" class="inline-block px-4 py-2 bg-white text-black rounded-full text-sm font-bold hover:scale-105 transition-transform">Browse playlists</a>
                        </div>
                        @endauth
                        <div class="bg-zinc-800/50 rounded-lg p-4 hover:bg-zinc-800 transition-colors">
                            <h4 class="text-white font-semibold mb-1">Let's find some music</h4>
                            <p class="text-zinc-400 text-sm mb-3">We'll keep you updated on new songs</p>
                            <a href="{{ route('artists.index') }}" class="inline-block px-4 py-2 bg-white text-black rounded-full text-sm font-bold hover:scale-105 transition-transform">Browse artists</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Section -->
            @auth
            <div class="mt-auto px-3 pb-6">
                <div class="bg-zinc-900 rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-[#1db954] flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr(Auth::user()->FirstName ?? Auth::user()->UserName ?? 'U', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white font-medium text-sm truncate">{{ Auth::user()->FirstName ?? Auth::user()->UserName }}</p>
                            @if(Auth::user()->isAdmin())
                            <p class="text-zinc-400 text-xs">Administrator</p>
                            @else
                            <p class="text-zinc-400 text-xs">User</p>
                            @endif
                        </div>
                    </div>
                    <div class="space-y-1">
                        @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block w-full px-3 py-2 text-sm text-zinc-400 hover:text-white hover:bg-zinc-800 rounded transition-colors">Admin Panel</a>
                        @endif
                        <a href="{{ route('user.dashboard') }}" class="block w-full px-3 py-2 text-sm text-zinc-400 hover:text-white hover:bg-zinc-800 rounded transition-colors">Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST" class="inline w-full">
                            @csrf
                            <button type="submit" class="block w-full text-left px-3 py-2 text-sm text-zinc-400 hover:text-white hover:bg-zinc-800 rounded transition-colors">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
            @else
            <div class="mt-auto px-3 pb-6">
                <div class="space-y-2">
                    <a href="{{ route('register') }}" class="block w-full px-4 py-3 bg-white text-black font-bold rounded-full hover:bg-zinc-200 transition-colors text-center">Sign Up</a>
                    <a href="{{ route('login') }}" class="block w-full px-4 py-3 bg-transparent border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-black transition-colors text-center">Log In</a>
                </div>
            </div>
            @endauth
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation Bar -->
            <header class="bg-zinc-900/50 backdrop-blur-sm border-b border-zinc-800 px-6 py-4 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <!-- Navigation Arrows -->
                    <div class="flex items-center gap-2">
                        <button onclick="window.history.back()" class="w-8 h-8 rounded-full bg-black flex items-center justify-center hover:bg-zinc-800 transition-colors">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                            </svg>
                        </button>
                        <button onclick="window.history.forward()" class="w-8 h-8 rounded-full bg-black flex items-center justify-center hover:bg-zinc-800 transition-colors">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Search Bar (Desktop) -->
                    <div class="hidden md:flex flex-1 max-w-md mx-8">
                        <form action="{{ route('indirimbo.search') }}" method="GET" class="relative w-full">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-zinc-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                            </svg>
                            <input
                                type="text"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="What do you want to listen to?"
                                class="w-full pl-10 pr-4 py-2 bg-white rounded-full text-sm text-black placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-white"
                            />
                        </form>
                    </div>

                    <!-- User Menu -->
                    <div class="flex items-center gap-4">
                        @auth
                        <div class="flex items-center gap-3">
                            @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 rounded-full text-sm font-medium transition-colors">Admin</a>
                            @endif
                            <div class="relative group">
                                <button class="flex items-center gap-2 bg-black hover:bg-zinc-800 rounded-full px-3 py-2 transition-colors">
                                    <div class="w-8 h-8 rounded-full bg-[#1db954] flex items-center justify-center text-white font-bold text-sm">
                                        {{ strtoupper(substr(Auth::user()->FirstName ?? Auth::user()->UserName ?? 'U', 0, 1)) }}
                                    </div>
                                    <span class="text-white font-medium text-sm hidden md:block">{{ Auth::user()->FirstName ?? Auth::user()->UserName }}</span>
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M7 10l5 5 5-5z"/>
                                    </svg>
                                </button>
                                <div class="absolute right-0 mt-2 w-48 bg-zinc-800 rounded-lg shadow-xl py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                                    <a href="{{ route('user.dashboard') }}" class="block px-4 py-2 text-sm text-white hover:bg-zinc-700">Dashboard</a>
                                    <a href="{{ route('contact') }}" class="block px-4 py-2 text-sm text-white hover:bg-zinc-700">Contact</a>
                                    <div class="border-t border-zinc-700 my-1"></div>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-white hover:bg-zinc-700">Logout</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @else
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-bold hover:underline">Sign up</a>
                        <a href="{{ route('login') }}" class="px-6 py-2 bg-white text-black rounded-full text-sm font-bold hover:bg-zinc-200 transition-colors">Log in</a>
                        @endauth
                    </div>
                </div>
            </header>

            <!-- Main Content (Scrollable) -->
            <main class="flex-1 overflow-y-auto bg-gradient-to-b from-zinc-900 via-black to-black">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bottom Music Player -->
    @include('components.music-player')
</body>
</html>

