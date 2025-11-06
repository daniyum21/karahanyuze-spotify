@extends('layouts.spotify')

@section('title', 'My Dashboard - Karahanyuze')

@section('content')
<div class="px-6 py-8 pb-24">
        @if(session('success'))
        <div class="mb-6 bg-green-500/20 border border-green-500/50 rounded-lg p-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <p class="text-green-400 font-medium">{{ session('success') }}</p>
        </div>
        @endif
        
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">My Dashboard</h1>
            <p class="text-zinc-400">Welcome back, {{ Auth::user()->FirstName ?? Auth::user()->UserName }}!</p>
        </div>

        <!-- Create Content Section -->
        <div class="mb-8 bg-zinc-900 rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-2">Create Content</h2>
                    <p class="text-zinc-400 text-sm">Share your music with the community. All content will be reviewed and approved by an administrator before being published.</p>
                </div>
                <a href="{{ route('submissions.index') }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-colors text-sm">
                    View My Submissions →
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('user.artists.create') }}" class="bg-zinc-800 hover:bg-zinc-700 rounded-lg p-4 transition-colors group">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:bg-purple-500/30 transition-colors">
                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="text-white font-semibold group-hover:text-purple-400 transition-colors">Create Artist</h3>
                    </div>
                    <p class="text-zinc-400 text-xs">Add a new artist</p>
                </a>
                
                <a href="{{ route('user.orchestras.create') }}" class="bg-zinc-800 hover:bg-zinc-700 rounded-lg p-4 transition-colors group">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center group-hover:bg-blue-500/30 transition-colors">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                            </svg>
                        </div>
                        <h3 class="text-white font-semibold group-hover:text-blue-400 transition-colors">Create Orchestra</h3>
                    </div>
                    <p class="text-zinc-400 text-xs">Add a new orchestra</p>
                </a>
                
                <a href="{{ route('user.itoreros.create') }}" class="bg-zinc-800 hover:bg-zinc-700 rounded-lg p-4 transition-colors group">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center group-hover:bg-yellow-500/30 transition-colors">
                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-white font-semibold group-hover:text-yellow-400 transition-colors">Create Itorero</h3>
                    </div>
                    <p class="text-zinc-400 text-xs">Add a new itorero</p>
                </a>
                
                <a href="{{ route('user.songs.create') }}" class="bg-zinc-800 hover:bg-zinc-700 rounded-lg p-4 transition-colors group">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center group-hover:bg-green-500/30 transition-colors">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                            </svg>
                        </div>
                        <h3 class="text-white font-semibold group-hover:text-green-400 transition-colors">Upload Song</h3>
                    </div>
                    <p class="text-zinc-400 text-xs">Upload a new song</p>
                </a>
            </div>
        </div>

        <!-- My Content Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-white">My Content</h2>
                <a href="{{ route('submissions.index') }}" class="text-sm text-zinc-400 hover:text-green-400 transition-colors">
                    View All →
                </a>
            </div>

            <!-- Tabs for different content types -->
            <div class="mb-6 flex flex-wrap gap-4 border-b border-zinc-800">
                <button onclick="showMyContent('songs')" class="my-content-tab px-4 py-2 text-white border-b-2 border-green-500 font-medium" data-tab="songs">
                    My Music ({{ $mySongs->count() }})
                </button>
                <button onclick="showMyContent('artists')" class="my-content-tab px-4 py-2 text-zinc-400 hover:text-white border-b-2 border-transparent font-medium" data-tab="artists">
                    My Artists ({{ $myArtists->count() }})
                </button>
                <button onclick="showMyContent('orchestras')" class="my-content-tab px-4 py-2 text-zinc-400 hover:text-white border-b-2 border-transparent font-medium" data-tab="orchestras">
                    My Orchestras ({{ $myOrchestras->count() }})
                </button>
                <button onclick="showMyContent('itoreros')" class="my-content-tab px-4 py-2 text-zinc-400 hover:text-white border-b-2 border-transparent font-medium" data-tab="itoreros">
                    My Itoreros ({{ $myItoreros->count() }})
                </button>
            </div>

            <!-- My Songs -->
            <div id="my-content-songs" class="my-content-section">
                @if($mySongs->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($mySongs as $song)
                    <div class="bg-zinc-900 rounded-lg overflow-hidden hover:bg-zinc-800 transition-colors">
                        <a href="{{ $song->isApproved() ? route('indirimbo.show', [$song->slug, $song->UUID]) : route('user.songs.edit', $song->UUID) }}" class="block">
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
                                <div class="absolute top-2 right-2">
                                    @if($song->isApproved())
                                    <span class="px-2 py-1 bg-green-500/80 text-white text-xs font-semibold rounded">Approved</span>
                                    @elseif($song->isDeclined())
                                    <span class="px-2 py-1 bg-red-500/80 text-white text-xs font-semibold rounded">Declined</span>
                                    @else
                                    <span class="px-2 py-1 bg-yellow-500/80 text-white text-xs font-semibold rounded">Pending</span>
                                    @endif
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="text-white font-semibold mb-1 line-clamp-2">{{ $song->IndirimboName }}</h3>
                                <p class="text-zinc-400 text-sm">
                                    @if($song->artist)
                                        {{ $song->artist->StageName }}
                                    @elseif($song->orchestra)
                                        {{ $song->orchestra->OrchestreName }}
                                    @elseif($song->itorero)
                                        {{ $song->itorero->ItoreroName }}
                                    @endif
                                </p>
                                <p class="text-xs text-zinc-500 mt-2">Submitted {{ $song->created_at ? $song->created_at->diffForHumans() : 'Recently' }}</p>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12 bg-zinc-900 rounded-lg">
                    <svg class="w-16 h-16 text-zinc-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                    </svg>
                    <p class="text-zinc-400 text-lg mb-4">No songs submitted yet</p>
                    <a href="{{ route('user.songs.create') }}" class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                        Upload Your First Song
                    </a>
                </div>
                @endif
            </div>

            <!-- My Artists -->
            <div id="my-content-artists" class="my-content-section hidden">
                @if($myArtists->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($myArtists as $artist)
                    <div class="bg-zinc-900 rounded-lg overflow-hidden hover:bg-zinc-800 transition-colors">
                        <a href="{{ route('artists.show', $artist->slug) }}" class="block">
                            <div class="aspect-square relative">
                                <img 
                                    src="{{ \App\Helpers\ImageHelper::getImageUrl($artist->ProfilePicture) }}" 
                                    alt="{{ $artist->StageName }}"
                                    class="w-full h-full object-cover"
                                >
                            </div>
                            <div class="p-4">
                                <h3 class="text-white font-semibold mb-1">{{ $artist->StageName }}</h3>
                                <p class="text-zinc-400 text-sm">{{ $artist->songs->count() }} songs</p>
                                <p class="text-xs text-zinc-500 mt-2">Created {{ $artist->created_at ? $artist->created_at->diffForHumans() : 'Recently' }}</p>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12 bg-zinc-900 rounded-lg">
                    <p class="text-zinc-400 text-lg mb-4">No artists created yet</p>
                    <a href="{{ route('user.artists.create') }}" class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                        Create Your First Artist
                    </a>
                </div>
                @endif
            </div>

            <!-- My Orchestras -->
            <div id="my-content-orchestras" class="my-content-section hidden">
                @if($myOrchestras->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($myOrchestras as $orchestra)
                    <div class="bg-zinc-900 rounded-lg overflow-hidden hover:bg-zinc-800 transition-colors">
                        <a href="{{ route('orchestre.show', $orchestra->slug) }}" class="block">
                            <div class="aspect-square relative">
                                <img 
                                    src="{{ \App\Helpers\ImageHelper::getImageUrl($orchestra->ProfilePicture) }}" 
                                    alt="{{ $orchestra->OrchestreName }}"
                                    class="w-full h-full object-cover"
                                >
                            </div>
                            <div class="p-4">
                                <h3 class="text-white font-semibold mb-1">{{ $orchestra->OrchestreName }}</h3>
                                <p class="text-zinc-400 text-sm">{{ $orchestra->songs->count() }} songs</p>
                                <p class="text-xs text-zinc-500 mt-2">Created {{ $orchestra->created_at ? $orchestra->created_at->diffForHumans() : 'Recently' }}</p>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12 bg-zinc-900 rounded-lg">
                    <p class="text-zinc-400 text-lg mb-4">No orchestras created yet</p>
                    <a href="{{ route('user.orchestras.create') }}" class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                        Create Your First Orchestra
                    </a>
                </div>
                @endif
            </div>

            <!-- My Itoreros -->
            <div id="my-content-itoreros" class="my-content-section hidden">
                @if($myItoreros->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($myItoreros as $itorero)
                    <div class="bg-zinc-900 rounded-lg overflow-hidden hover:bg-zinc-800 transition-colors">
                        <a href="{{ route('itorero.show', $itorero->slug) }}" class="block">
                            <div class="aspect-square relative">
                                <img 
                                    src="{{ \App\Helpers\ImageHelper::getImageUrl($itorero->ProfilePicture) }}" 
                                    alt="{{ $itorero->ItoreroName }}"
                                    class="w-full h-full object-cover"
                                >
                            </div>
                            <div class="p-4">
                                <h3 class="text-white font-semibold mb-1">{{ $itorero->ItoreroName }}</h3>
                                <p class="text-zinc-400 text-sm">{{ $itorero->songs->count() }} songs</p>
                                <p class="text-xs text-zinc-500 mt-2">Created {{ $itorero->created_at ? $itorero->created_at->diffForHumans() : 'Recently' }}</p>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12 bg-zinc-900 rounded-lg">
                    <p class="text-zinc-400 text-lg mb-4">No itoreros created yet</p>
                    <a href="{{ route('user.itoreros.create') }}" class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                        Create Your First Itorero
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Favorites Section -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-white mb-4">Your Favorites</h2>
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
function showMyContent(type) {
    // Hide all sections
    document.querySelectorAll('.my-content-section').forEach(section => {
        section.classList.add('hidden');
    });
    
    // Show selected section
    const section = document.getElementById('my-content-' + type);
    if (section) {
        section.classList.remove('hidden');
    }
    
    // Update tab buttons
    document.querySelectorAll('.my-content-tab').forEach(button => {
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
