@extends('layouts.app')

@section('title', 'Karahanyuze - Rwandan Music Heritage')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black">
    <!-- Hero Section -->
    <section class="flex flex-col items-center justify-center min-h-[80vh] text-center px-4 py-20 relative">
        <!-- Background gradient effect -->
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-blue-950/30 to-transparent"></div>
        
        <div class="relative z-10 max-w-4xl mx-auto">
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-white mb-6">
                Discover Rwandan Music Heritage
            </h1>
            <p class="text-xl md:text-2xl text-white/90 mb-12 max-w-2xl mx-auto leading-relaxed">
                Search our collection of karahanyuze classics, modern interpretations, and traditional treasures.
            </p>
            
            <!-- Large Search Bar -->
                    <form action="{{ route('indirimbo.search') }}" method="GET" class="w-full max-w-2xl mx-auto">
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        type="text"
                        name="q"
                        placeholder="Search songs, artists, playlists..."
                        class="w-full pl-14 pr-4 py-4 bg-zinc-900/80 border-2 border-blue-500 rounded-lg text-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-lg backdrop-blur-sm"
                    />
                </div>
            </form>
        </div>
    </section>

    
    <!-- Recent Songs -->
    @if($recentSongs->count() > 0)
    <section class="container mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">Recently Added</h2>
            <a href="#" class="text-green-600 hover:text-green-500 text-sm font-medium">View All</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($recentSongs as $song)
                    <a href="{{ route('indirimbo.show', [$song->slug, $song->UUID]) }}" class="bg-zinc-800 rounded-lg p-4 hover:bg-zinc-700 transition-colors cursor-pointer group block">
                <div class="relative mb-4">
                    <img 
                        src="{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}" 
                        alt="{{ $song->IndirimboName }}"
                        class="w-full aspect-square object-cover rounded-lg"
                    />
                    <button class="absolute bottom-2 right-2 w-10 h-10 bg-green-600 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </button>
                </div>
                <h3 class="font-semibold text-white mb-1 truncate">{{ $song->IndirimboName }}</h3>
                <p class="text-sm text-zinc-400 truncate">
                    @if($song->artist)
                        {{ $song->artist->StageName }}
                    @elseif($song->orchestra)
                        {{ $song->orchestra->OrchestreName }}
                    @elseif($song->itorero)
                        {{ $song->itorero->ItoreroName }}
                    @else
                        Unknown Artist
                    @endif
                </p>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Featured Artists -->
    @if($featuredArtists->count() > 0)
    <section class="container mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">Featured Artists</h2>
            <a href="#" class="text-green-600 hover:text-green-500 text-sm font-medium">View All</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredArtists as $artist)
            <div class="bg-zinc-800 rounded-lg p-4 hover:bg-zinc-700 transition-colors cursor-pointer">
                <div class="mb-4">
                    <img 
                        src="{{ \App\Helpers\ImageHelper::getImageUrl($artist->ProfilePicture) }}" 
                        alt="{{ $artist->StageName }}"
                        class="w-full aspect-square object-cover rounded-lg"
                    />
                </div>
                <h3 class="font-semibold text-white mb-1">{{ $artist->StageName }}</h3>
                <p class="text-sm text-zinc-400">{{ $artist->songs->count() }} songs</p>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Featured Playlists -->
    @if($featuredPlaylists->count() > 0)
    <section class="container mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">Featured Playlists</h2>
            <a href="{{ route('playlists.index') }}" class="text-green-600 hover:text-green-500 text-sm font-medium">View All</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredPlaylists as $playlist)
                    <a href="{{ route('playlists.show', $playlist->slug) }}" class="bg-zinc-800 rounded-lg p-4 hover:bg-zinc-700 transition-colors block">
                <div class="mb-4">
                    <img 
                        src="{{ \App\Helpers\ImageHelper::getImageUrl($playlist->ProfilePicture) }}" 
                        alt="{{ $playlist->PlaylistName }}"
                        class="w-full aspect-square object-cover rounded-lg"
                    />
                </div>
                <h3 class="font-semibold text-white mb-1">{{ $playlist->PlaylistName }}</h3>
                <p class="text-sm text-zinc-400">{{ $playlist->songs->count() }} songs</p>
            </a>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection

