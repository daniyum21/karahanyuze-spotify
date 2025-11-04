@extends('layouts.app')

@section('title', 'My Dashboard - Karahanyuze')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black">
    <div class="container mx-auto px-4 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">My Dashboard</h1>
            <p class="text-zinc-400">Welcome back, {{ Auth::user()->FirstName ?? Auth::user()->UserName }}!</p>
            <p class="text-sm text-zinc-500 mt-2">Your favorites</p>
        </div>

        <!-- Tabs for different favorite types -->
        <div class="mb-6 flex flex-wrap gap-4 border-b border-zinc-800">
            <button onclick="showFavorites('songs')" class="tab-button px-4 py-2 text-white border-b-2 border-green-500 font-medium" data-tab="songs">
                Songs ({{ $favoriteSongs->total() }})
            </button>
            <button onclick="showFavorites('artists')" class="tab-button px-4 py-2 text-zinc-400 hover:text-white border-b-2 border-transparent font-medium" data-tab="artists">
                Artists ({{ $favoriteArtists->count() }})
            </button>
            <button onclick="showFavorites('orchestras')" class="tab-button px-4 py-2 text-zinc-400 hover:text-white border-b-2 border-transparent font-medium" data-tab="orchestras">
                Orchestras ({{ $favoriteOrchestras->count() }})
            </button>
            <button onclick="showFavorites('itoreros')" class="tab-button px-4 py-2 text-zinc-400 hover:text-white border-b-2 border-transparent font-medium" data-tab="itoreros">
                Itoreros ({{ $favoriteItoreros->count() }})
            </button>
            <button onclick="showFavorites('playlists')" class="tab-button px-4 py-2 text-zinc-400 hover:text-white border-b-2 border-transparent font-medium" data-tab="playlists">
                Playlists ({{ $favoritePlaylists->count() }})
            </button>
        </div>

        <!-- Favorite Songs -->
        <div id="favorites-songs" class="favorites-section">
            @if($favoriteSongs->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($favoriteSongs as $song)
                <a href="{{ route('indirimbo.show', [$song->slug, $song->UUID]) }}" class="group">
                    <div class="bg-zinc-900 rounded-lg overflow-hidden hover:bg-zinc-800 transition-colors">
                        <div class="aspect-square relative">
                            @if($song->ProfilePicture)
                            <img 
                                src="{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}" 
                                alt="{{ $song->IndirimboName }}"
                                class="w-full h-full object-cover"
                            >
                            @else
                            <div class="w-full h-full bg-zinc-800 flex items-center justify-center">
                                <svg class="w-16 h-16 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                </svg>
                            </div>
                            @endif
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                <svg class="w-12 h-12 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="text-white font-semibold mb-1 line-clamp-2 group-hover:text-green-400 transition-colors">{{ $song->IndirimboName }}</h3>
                            <p class="text-zinc-400 text-sm">
                                @if($song->artist)
                                    {{ $song->artist->StageName }}
                                @elseif($song->orchestra)
                                    {{ $song->orchestra->OrchestreName }}
                                @elseif($song->itorero)
                                    {{ $song->itorero->ItoreroName }}
                                @endif
                            </p>
                            <p class="text-xs text-zinc-500 mt-2">Added {{ isset($song->favorited_at) ? \Carbon\Carbon::parse($song->favorited_at)->diffForHumans() : 'Recently' }}</p>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="mt-8">
                {{ $favoriteSongs->links() }}
            </div>
            @else
            <div class="text-center py-12 bg-zinc-900 rounded-lg">
                <svg class="w-16 h-16 text-zinc-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <p class="text-zinc-400 text-lg mb-4">No favorite songs yet</p>
                <p class="text-zinc-500 text-sm mb-6">Start liking songs to see them here!</p>
                <a href="{{ route('home') }}" class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    Browse Songs
                </a>
            </div>
            @endif
        </div>

        <!-- Favorite Artists -->
        <div id="favorites-artists" class="favorites-section hidden">
            @if($favoriteArtists->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($favoriteArtists as $artist)
                <a href="{{ route('artists.show', $artist->slug) }}" class="group">
                    <div class="bg-zinc-900 rounded-lg overflow-hidden hover:bg-zinc-800 transition-colors">
                        <div class="aspect-square relative">
                            <img 
                                src="{{ \App\Helpers\ImageHelper::getImageUrl($artist->ProfilePicture) }}" 
                                alt="{{ $artist->StageName }}"
                                class="w-full h-full object-cover"
                            >
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors"></div>
                        </div>
                        <div class="p-4">
                            <h3 class="text-white font-semibold mb-1 group-hover:text-green-400 transition-colors">{{ $artist->StageName }}</h3>
                            <p class="text-zinc-400 text-sm">{{ $artist->songs->count() }} songs</p>
                            <p class="text-xs text-zinc-500 mt-2">Added {{ isset($artist->favorited_at) ? \Carbon\Carbon::parse($artist->favorited_at)->diffForHumans() : 'Recently' }}</p>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="text-center py-12 bg-zinc-900 rounded-lg">
                <p class="text-zinc-400 text-lg mb-4">No favorite artists yet</p>
                <a href="{{ route('artists.index') }}" class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    Browse Artists
                </a>
            </div>
            @endif
        </div>

        <!-- Favorite Orchestras -->
        <div id="favorites-orchestras" class="favorites-section hidden">
            @if($favoriteOrchestras->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($favoriteOrchestras as $orchestra)
                <a href="{{ route('orchestre.show', $orchestra->slug) }}" class="group">
                    <div class="bg-zinc-900 rounded-lg overflow-hidden hover:bg-zinc-800 transition-colors">
                        <div class="aspect-square relative">
                            <img 
                                src="{{ \App\Helpers\ImageHelper::getImageUrl($orchestra->ProfilePicture) }}" 
                                alt="{{ $orchestra->OrchestreName }}"
                                class="w-full h-full object-cover"
                            >
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors"></div>
                        </div>
                        <div class="p-4">
                            <h3 class="text-white font-semibold mb-1 group-hover:text-green-400 transition-colors">{{ $orchestra->OrchestreName }}</h3>
                            <p class="text-zinc-400 text-sm">{{ $orchestra->songs->count() }} songs</p>
                            <p class="text-xs text-zinc-500 mt-2">Added {{ isset($orchestra->favorited_at) ? \Carbon\Carbon::parse($orchestra->favorited_at)->diffForHumans() : 'Recently' }}</p>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="text-center py-12 bg-zinc-900 rounded-lg">
                <p class="text-zinc-400 text-lg mb-4">No favorite orchestras yet</p>
                <a href="{{ route('orchestre.index') }}" class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    Browse Orchestras
                </a>
            </div>
            @endif
        </div>

        <!-- Favorite Itoreros -->
        <div id="favorites-itoreros" class="favorites-section hidden">
            @if($favoriteItoreros->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($favoriteItoreros as $itorero)
                <a href="{{ route('itorero.show', $itorero->slug) }}" class="group">
                    <div class="bg-zinc-900 rounded-lg overflow-hidden hover:bg-zinc-800 transition-colors">
                        <div class="aspect-square relative">
                            <img 
                                src="{{ \App\Helpers\ImageHelper::getImageUrl($itorero->ProfilePicture) }}" 
                                alt="{{ $itorero->ItoreroName }}"
                                class="w-full h-full object-cover"
                            >
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors"></div>
                        </div>
                        <div class="p-4">
                            <h3 class="text-white font-semibold mb-1 group-hover:text-green-400 transition-colors">{{ $itorero->ItoreroName }}</h3>
                            <p class="text-zinc-400 text-sm">{{ $itorero->songs->count() }} songs</p>
                            <p class="text-xs text-zinc-500 mt-2">Added {{ isset($itorero->favorited_at) ? \Carbon\Carbon::parse($itorero->favorited_at)->diffForHumans() : 'Recently' }}</p>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="text-center py-12 bg-zinc-900 rounded-lg">
                <p class="text-zinc-400 text-lg mb-4">No favorite itoreros yet</p>
                <a href="{{ route('itorero.index') }}" class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    Browse Itoreros
                </a>
            </div>
            @endif
        </div>

        <!-- Favorite Playlists -->
        <div id="favorites-playlists" class="favorites-section hidden">
            @if($favoritePlaylists->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($favoritePlaylists as $playlist)
                <a href="{{ route('playlists.show', $playlist->slug) }}" class="group">
                    <div class="bg-zinc-900 rounded-lg overflow-hidden hover:bg-zinc-800 transition-colors">
                        <div class="aspect-square relative">
                            <img 
                                src="{{ \App\Helpers\ImageHelper::getImageUrl($playlist->ProfilePicture) }}" 
                                alt="{{ $playlist->PlaylistName }}"
                                class="w-full h-full object-cover"
                            >
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors"></div>
                        </div>
                        <div class="p-4">
                            <h3 class="text-white font-semibold mb-1 group-hover:text-green-400 transition-colors">{{ $playlist->PlaylistName }}</h3>
                            <p class="text-zinc-400 text-sm">{{ $playlist->songs->count() }} songs</p>
                            <p class="text-xs text-zinc-500 mt-2">Added {{ isset($playlist->favorited_at) ? \Carbon\Carbon::parse($playlist->favorited_at)->diffForHumans() : 'Recently' }}</p>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="text-center py-12 bg-zinc-900 rounded-lg">
                <p class="text-zinc-400 text-lg mb-4">No favorite playlists yet</p>
                <a href="{{ route('playlists.index') }}" class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    Browse Playlists
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function showFavorites(type) {
    // Hide all sections
    document.querySelectorAll('.favorites-section').forEach(section => {
        section.classList.add('hidden');
    });
    
    // Show selected section
    const section = document.getElementById('favorites-' + type);
    if (section) {
        section.classList.remove('hidden');
    }
    
    // Update tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        const tab = button.getAttribute('data-tab');
        if (tab === type) {
            button.classList.remove('text-zinc-400', 'border-transparent');
            button.classList.add('text-white', 'border-green-500');
        } else {
            button.classList.remove('text-white', 'border-green-500');
            button.classList.add('text-zinc-400', 'border-transparent');
        }
    });
}
</script>
@endsection
