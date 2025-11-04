<header class="sticky top-0 z-50 border-b border-zinc-800/50 bg-black shadow-sm">
    <div class="container mx-auto px-4 py-4">
        <!-- Top Row: Logo, Search, Hamburger -->
        <div class="flex items-center justify-between gap-4">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 md:gap-3 hover:opacity-80 transition-opacity cursor-pointer flex-shrink-0">
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg bg-green-500 flex items-center justify-center text-yellow-400 font-bold text-base md:text-lg">
                    K
                </div>
                <div class="hidden sm:block">
                    <h1 class="text-lg md:text-2xl font-bold text-green-500">Karahanyuze</h1>
                    <p class="text-xs text-white hidden md:block">INDIRIMBO ZO HAMBERE</p>
                </div>
            </a>

            <!-- Search Bar - Hidden on mobile, shown on tablet+ -->
            <div class="hidden md:flex flex-1 max-w-md mx-4">
                <form action="{{ route('indirimbo.search') }}" method="GET" class="relative w-full">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        type="text"
                        name="q"
                        placeholder="Shakira indirimbo..."
                        class="w-full pl-10 pr-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-sm text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    />
                </form>
            </div>

            <!-- Desktop Auth Buttons - Hidden on mobile -->
            <div class="hidden md:flex items-center gap-2 flex-shrink-0">
                @auth
                @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 rounded-lg text-white font-medium transition-colors text-sm">
                    Admin
                </a>
                @else
                <a href="{{ route('user.dashboard') }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 rounded-lg text-white font-medium transition-colors text-sm">
                    Dashboard
                </a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 rounded-lg text-white font-medium transition-colors text-sm">
                        Gusohoka
                    </button>
                </form>
                @else
                <a href="{{ route('register') }}" class="px-3 py-2 border border-green-500 text-green-500 hover:bg-green-500 hover:text-white rounded-lg font-medium transition-colors text-sm">
                    Iyandikishe
                </a>
                <a href="{{ route('login') }}" class="px-3 py-2 bg-green-500 hover:bg-green-600 rounded-lg text-white font-medium transition-colors text-sm">
                    Injira
                </a>
                @endauth
            </div>

            <!-- Hamburger Menu Button - Mobile only -->
            <button id="mobile-menu-toggle" class="md:hidden p-2 rounded-lg hover:bg-zinc-800 transition-colors text-white" aria-label="Toggle menu">
                <svg id="menu-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg id="close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile Menu - Hidden by default -->
        <div id="mobile-menu" class="hidden md:hidden mt-4 pb-4 border-t border-zinc-800/50">
            <!-- Mobile Search -->
            <div class="mt-4 mb-4">
                <form action="{{ route('indirimbo.search') }}" method="GET" class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        type="text"
                        name="q"
                        placeholder="Shakira indirimbo..."
                        class="w-full pl-10 pr-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-sm text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    />
                </form>
            </div>

            <!-- Mobile Navigation Links -->
            <nav class="flex flex-col gap-2">
                <a href="{{ route('artists.index') }}" class="px-4 py-3 rounded-lg transition-colors font-medium {{ request()->routeIs('artists.*') ? 'bg-green-500/20 text-green-500' : 'text-white hover:bg-zinc-800' }}">Abahanzi</a>
                <a href="{{ route('itorero.index') }}" class="px-4 py-3 rounded-lg transition-colors font-medium {{ request()->routeIs('itorero.*') ? 'bg-green-500/20 text-green-500' : 'text-white hover:bg-zinc-800' }}">Itorero</a>
                <a href="{{ route('orchestre.index') }}" class="px-4 py-3 rounded-lg transition-colors font-medium {{ request()->routeIs('orchestre.*') ? 'bg-green-500/20 text-green-500' : 'text-white hover:bg-zinc-800' }}">Orchestre</a>
                <a href="{{ route('playlists.index') }}" class="px-4 py-3 rounded-lg transition-colors font-medium {{ request()->routeIs('playlists.*') ? 'bg-green-500/20 text-green-500' : 'text-white hover:bg-zinc-800' }}">Playlists</a>
                <a href="{{ route('contact') }}" class="px-4 py-3 rounded-lg transition-colors font-medium {{ request()->routeIs('contact') ? 'bg-green-500/20 text-green-500' : 'text-white hover:bg-zinc-800' }}">Contact Us</a>
            </nav>

            <!-- Mobile Auth Buttons -->
            <div class="mt-4 pt-4 border-t border-zinc-800/50 flex flex-col gap-2">
                @auth
                @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-3 bg-blue-500 hover:bg-blue-600 rounded-lg text-white font-medium transition-colors text-center">
                    Admin Dashboard
                </a>
                @else
                <a href="{{ route('user.dashboard') }}" class="px-4 py-3 bg-blue-500 hover:bg-blue-600 rounded-lg text-white font-medium transition-colors text-center">
                    Dashboard
                </a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 bg-red-500 hover:bg-red-600 rounded-lg text-white font-medium transition-colors">
                        Gusohoka
                    </button>
                </form>
                @else
                <a href="{{ route('register') }}" class="px-4 py-3 border border-green-500 text-green-500 hover:bg-green-500 hover:text-white rounded-lg font-medium transition-colors text-center">
                    Iyandikishe
                </a>
                <a href="{{ route('login') }}" class="px-4 py-3 bg-green-500 hover:bg-green-600 rounded-lg text-white font-medium transition-colors text-center">
                    Injira
                </a>
                @endauth
            </div>
        </div>

        <!-- Desktop Navigation Links - Hidden on mobile -->
        <div class="hidden md:flex items-center gap-6 mt-4">
            <a href="{{ route('artists.index') }}" class="transition-colors font-medium {{ request()->routeIs('artists.*') ? 'text-green-500' : 'text-white hover:text-green-500' }}">Abahanzi</a>
            <a href="{{ route('itorero.index') }}" class="transition-colors font-medium {{ request()->routeIs('itorero.*') ? 'text-green-500' : 'text-white hover:text-green-500' }}">Itorero</a>
            <a href="{{ route('orchestre.index') }}" class="transition-colors font-medium {{ request()->routeIs('orchestre.*') ? 'text-green-500' : 'text-white hover:text-green-500' }}">Orchestre</a>
            <a href="{{ route('playlists.index') }}" class="transition-colors font-medium {{ request()->routeIs('playlists.*') ? 'text-green-500' : 'text-white hover:text-green-500' }}">Playlists</a>
            <a href="{{ route('contact') }}" class="transition-colors font-medium {{ request()->routeIs('contact') ? 'text-green-500' : 'text-white hover:text-green-500' }}">Contact Us</a>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('menu-icon');
    const closeIcon = document.getElementById('close-icon');

    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
            menuIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileMenu.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
                if (!mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                    menuIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                }
            }
        });

        // Close menu when clicking a link
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.add('hidden');
                menuIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            });
        });
    }
});
</script>

