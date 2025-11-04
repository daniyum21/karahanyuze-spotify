<header class="sticky top-0 z-40 border-b border-zinc-800/50 bg-black shadow-sm">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 hover:opacity-80 transition-opacity cursor-pointer">
                <div class="w-10 h-10 rounded-lg bg-green-500 flex items-center justify-center text-yellow-400 font-bold text-lg">
                    K
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-green-500">Karahanyuze</h1>
                    <p class="text-xs text-white">INDIRIMBO ZO HAMBERE</p>
                </div>
            </a>

            <!-- Navigation Links in Header -->
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('artists.index') }}" class="transition-colors font-medium {{ request()->routeIs('artists.*') ? 'text-green-500' : 'text-white hover:text-green-500' }}">Abahanzi</a>
                <a href="{{ route('itorero.index') }}" class="transition-colors font-medium {{ request()->routeIs('itorero.*') ? 'text-green-500' : 'text-white hover:text-green-500' }}">Itorero</a>
                <a href="{{ route('orchestre.index') }}" class="transition-colors font-medium {{ request()->routeIs('orchestre.*') ? 'text-green-500' : 'text-white hover:text-green-500' }}">Orchestre</a>
                <a href="{{ route('playlists.index') }}" class="transition-colors font-medium {{ request()->routeIs('playlists.*') ? 'text-green-500' : 'text-white hover:text-green-500' }}">Playlists</a>
                <a href="{{ route('contact') }}" class="transition-colors font-medium {{ request()->routeIs('contact') ? 'text-green-500' : 'text-white hover:text-green-500' }}">Contact Us</a>
            </div>

            <!-- Search Bar in Header -->
            <div class="flex-1 max-w-md">
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

            <div class="flex items-center gap-2">
                <a href="#" class="p-2 rounded-lg hover:bg-zinc-800 transition-colors">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </a>
                <a href="#" class="p-2 rounded-lg hover:bg-zinc-800 transition-colors">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                    </svg>
                </a>
                @auth
                @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 rounded-lg text-white font-medium transition-colors">
                    Admin Dashboard
                </a>
                @else
                <a href="{{ route('user.dashboard') }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 rounded-lg text-white font-medium transition-colors">
                    Dashboard
                </a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 rounded-lg text-white font-medium transition-colors">
                        Gusohoka
                    </button>
                </form>
                @else
                <a href="{{ route('register') }}" class="px-4 py-2 border border-green-500 text-green-500 hover:bg-green-500 hover:text-white rounded-lg font-medium transition-colors">
                    Iyandikishe
                </a>
                <a href="{{ route('login') }}" class="px-4 py-2 bg-green-500 hover:bg-green-600 rounded-lg text-white font-medium transition-colors">
                    Injira
                </a>
                @endauth
            </div>
        </div>
    </div>
</header>

