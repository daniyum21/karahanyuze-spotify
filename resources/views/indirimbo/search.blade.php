@extends('layouts.spotify')

@section('title', 'Search Results - Karahanyuze')

@php
    // Helper function to highlight matching text
    function highlightText($text, $query) {
        if (empty($query) || empty($text)) {
            return $text;
        }
        // Escape HTML special characters in the query
        $escapedQuery = htmlspecialchars($query, ENT_QUOTES, 'UTF-8');
        // Split query into words for multi-word highlighting
        $words = preg_split('/\s+/', $escapedQuery);
        $highlighted = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        
        foreach ($words as $word) {
            if (strlen(trim($word)) > 0) {
                // Use case-insensitive highlighting
                $pattern = '/(' . preg_quote($word, '/') . ')/i';
                $highlighted = preg_replace($pattern, '<mark class="bg-green-500/30 text-green-300 rounded px-1">$1</mark>', $highlighted);
            }
        }
        
        return $highlighted;
    }
@endphp

@section('content')
<div class="px-6 py-8 pb-24">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Search Results</h1>
            <p class="text-zinc-400">Searching for: <span class="text-green-500 font-semibold">"{{ $query }}"</span></p>
            <p class="text-sm text-zinc-500 mt-2">Found {{ $totalResults }} result(s)</p>
        </div>

        @if($totalResults === 0)
        <div class="text-center py-12 bg-zinc-900 rounded-lg">
            <svg class="w-16 h-16 text-zinc-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <p class="text-zinc-400 text-lg mb-4">No results found for "{{ $query }}"</p>
            <p class="text-zinc-500 text-sm">Try searching with different keywords</p>
        </div>
        @else
        <div class="space-y-12">
            <!-- Songs Results -->
            @if($songs->count() > 0)
            <div>
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                    </svg>
                    Songs ({{ $songs->count() }})
                </h2>
                <div class="overflow-x-auto scrollbar-hide -mx-6 px-6">
                    <div class="flex gap-4" style="width: max-content;">
                        @foreach($songs as $song)
                        <div class="group flex-shrink-0 w-[180px]">
                            <a href="{{ route('indirimbo.show', [$song->slug, $song->UUID]) }}" class="block p-4 bg-zinc-900/50 hover:bg-zinc-800 rounded-lg transition-all duration-200 cursor-pointer">
                                <div class="relative mb-4">
                                    <img 
                                        src="{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}" 
                                        alt="{{ $song->IndirimboName }}"
                                        class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                    />
                                    <button 
                                        onclick="event.preventDefault(); playSong('{{ $song->UUID }}', '{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}', '{{ addslashes($song->IndirimboName) }}', '{{ addslashes($song->artist ? $song->artist->StageName : ($song->orchestra ? $song->orchestra->OrchestreName : ($song->itorero ? $song->itorero->ItoreroName : 'Unknown'))) }}', '{{ route('indirimbo.audio', $song->IndirimboID) }}', {{ $song->IndirimboID }});"
                                        class="absolute bottom-2 right-2 w-12 h-12 bg-[#1db954] rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-lg hover:scale-110 z-10"
                                    >
                                        <svg class="w-6 h-6 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                <h3 class="font-semibold text-white mb-1 truncate text-sm">{!! highlightText($song->IndirimboName, $query) !!}</h3>
                                <p class="text-xs text-zinc-400 truncate">
                                    @if($song->artist)
                                        {!! highlightText($song->artist->StageName, $query) !!}
                                    @elseif($song->orchestra)
                                        {!! highlightText($song->orchestra->OrchestreName, $query) !!}
                                    @elseif($song->itorero)
                                        {!! highlightText($song->itorero->ItoreroName, $query) !!}
                                    @endif
                                </p>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Artists Results -->
            @if($artists->count() > 0)
            <div>
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Artists ({{ $artists->count() }})
                </h2>
                <div class="overflow-x-auto scrollbar-hide -mx-6 px-6">
                    <div class="flex gap-4" style="width: max-content;">
                        @foreach($artists as $artist)
                        <div class="group flex-shrink-0 w-[180px]">
                            <a href="{{ route('artists.show', $artist->slug) }}" class="block p-4 bg-zinc-900/50 hover:bg-zinc-800 rounded-lg transition-all duration-200 cursor-pointer">
                                <div class="relative mb-4">
                                    <img 
                                        src="{{ \App\Helpers\ImageHelper::getImageUrl($artist->ProfilePicture) }}" 
                                        alt="{{ $artist->StageName }}"
                                        class="w-full aspect-square object-cover rounded-full shadow-lg"
                                    />
                                    <button 
                                        onclick="event.preventDefault(); window.location.href='{{ route('artists.show', $artist->slug) }}';"
                                        class="absolute bottom-2 right-2 w-12 h-12 bg-[#1db954] rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-lg hover:scale-110 z-10"
                                    >
                                        <svg class="w-6 h-6 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                <h3 class="font-semibold text-white mb-1 truncate text-sm group-hover:underline">{!! highlightText($artist->StageName, $query) !!}</h3>
                                <p class="text-xs text-zinc-400">Artist</p>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Orchestras Results -->
            @if($orchestras->count() > 0)
            <div>
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                    </svg>
                    Orchestras ({{ $orchestras->count() }})
                </h2>
                <div class="overflow-x-auto scrollbar-hide -mx-6 px-6">
                    <div class="flex gap-4" style="width: max-content;">
                        @foreach($orchestras as $orchestra)
                        <div class="group flex-shrink-0 w-[180px]">
                            <a href="{{ route('orchestre.show', $orchestra->slug) }}" class="block p-4 bg-zinc-900/50 hover:bg-zinc-800 rounded-lg transition-all duration-200 cursor-pointer">
                                <div class="relative mb-4">
                                    <img 
                                        src="{{ \App\Helpers\ImageHelper::getImageUrl($orchestra->ProfilePicture) }}" 
                                        alt="{{ $orchestra->OrchestreName }}"
                                        class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                    />
                                    <button 
                                        onclick="event.preventDefault(); window.location.href='{{ route('orchestre.show', $orchestra->slug) }}';"
                                        class="absolute bottom-2 right-2 w-12 h-12 bg-[#1db954] rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-lg hover:scale-110 z-10"
                                    >
                                        <svg class="w-6 h-6 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                <h3 class="font-semibold text-white mb-1 truncate text-sm group-hover:underline">{!! highlightText($orchestra->OrchestreName, $query) !!}</h3>
                                <p class="text-xs text-zinc-400">Orchestra</p>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Itoreros Results -->
            @if($itoreros->count() > 0)
            <div>
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Itoreros ({{ $itoreros->count() }})
                </h2>
                <div class="overflow-x-auto scrollbar-hide -mx-6 px-6">
                    <div class="flex gap-4" style="width: max-content;">
                        @foreach($itoreros as $itorero)
                        <div class="group flex-shrink-0 w-[180px]">
                            <a href="{{ route('itorero.show', $itorero->slug) }}" class="block p-4 bg-zinc-900/50 hover:bg-zinc-800 rounded-lg transition-all duration-200 cursor-pointer">
                                <div class="relative mb-4">
                                    <img 
                                        src="{{ \App\Helpers\ImageHelper::getImageUrl($itorero->ProfilePicture) }}" 
                                        alt="{{ $itorero->ItoreroName }}"
                                        class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                    />
                                    <button 
                                        onclick="event.preventDefault(); window.location.href='{{ route('itorero.show', $itorero->slug) }}';"
                                        class="absolute bottom-2 right-2 w-12 h-12 bg-[#1db954] rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-lg hover:scale-110 z-10"
                                    >
                                        <svg class="w-6 h-6 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                <h3 class="font-semibold text-white mb-1 truncate text-sm group-hover:underline">{!! highlightText($itorero->ItoreroName, $query) !!}</h3>
                                <p class="text-xs text-zinc-400">Itorero</p>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Playlists Results -->
            @if($playlists->count() > 0)
            <div>
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                    </svg>
                    Playlists ({{ $playlists->count() }})
                </h2>
                <div class="overflow-x-auto scrollbar-hide -mx-6 px-6">
                    <div class="flex gap-4" style="width: max-content;">
                        @foreach($playlists as $playlist)
                        <div class="group flex-shrink-0 w-[180px]">
                            <a href="{{ route('playlists.show', $playlist->slug) }}" class="block p-4 bg-zinc-900/50 hover:bg-zinc-800 rounded-lg transition-all duration-200 cursor-pointer">
                                <div class="relative mb-4">
                                    <img 
                                        src="{{ \App\Helpers\ImageHelper::getImageUrl($playlist->ProfilePicture) }}" 
                                        alt="{{ $playlist->PlaylistName }}"
                                        class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                    />
                                    <button 
                                        onclick="event.preventDefault(); window.location.href='{{ route('playlists.show', $playlist->slug) }}';"
                                        class="absolute bottom-2 right-2 w-12 h-12 bg-[#1db954] rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-lg hover:scale-110 z-10"
                                    >
                                        <svg class="w-6 h-6 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                <h3 class="font-semibold text-white mb-1 truncate text-sm group-hover:underline">{!! highlightText($playlist->PlaylistName, $query) !!}</h3>
                                <p class="text-xs text-zinc-400">{{ $playlist->songs->count() }} songs</p>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif
</div>

<style>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>

<script>
function playSong(uuid, imageUrl, title, artist, audioUrl, songId = null) {
    if (songId) {
        window.currentSongId = songId;
    }
    
    if (typeof window.playSongFromPlayer === 'function') {
        window.playSongFromPlayer(uuid, imageUrl, title, artist, audioUrl);
    } else {
        const player = document.getElementById('bottom-audio-player');
        const playerImage = document.getElementById('player-image');
        const playerTitle = document.getElementById('player-title');
        const playerArtist = document.getElementById('player-artist');
        const musicPlayer = document.getElementById('music-player');
        
        if (player && audioUrl) {
            player.src = audioUrl;
            if (playerImage) playerImage.src = imageUrl || '/placeholder.svg';
            if (playerTitle) playerTitle.textContent = title;
            if (playerArtist) playerArtist.textContent = artist;
            if (musicPlayer) musicPlayer.classList.remove('hidden');
            player.play().catch(e => console.error('Play error:', e));
        }
    }
}
</script>
@endsection

