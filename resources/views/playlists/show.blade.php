@extends('layouts.spotify')

@section('title', $playlist->PlaylistName . ' - Karahanyuze')

@section('content')
<div class="relative">
    <!-- Gradient Header -->
    <div class="absolute top-0 left-0 right-0 h-[400px] bg-gradient-to-b from-[#1db954] via-[#1db954]/80 to-black z-0"></div>
    
    <div class="relative z-10 px-6 pt-8 pb-24">
        <!-- Header Section -->
        <div class="flex items-end gap-6 mb-8">
            <img 
                src="{{ \App\Helpers\ImageHelper::getImageUrl($playlist->ProfilePicture) }}" 
                alt="{{ $playlist->PlaylistName }}"
                class="w-[232px] h-[232px] rounded-lg object-cover shadow-2xl flex-shrink-0"
            />
            <div class="flex-1 pb-4">
                <p class="text-sm text-white mb-2">Playlist</p>
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-4">{{ $playlist->PlaylistName }}</h1>
                
                <div class="flex items-center gap-2 text-sm text-zinc-300">
                    <span>{{ $playlist->songs->count() }} songs</span>
                </div>
            </div>
        </div>

        <!-- Play Button and Actions -->
        <div class="flex items-center gap-4 mb-8">
            <button 
                onclick="playAllSongs()"
                class="w-14 h-14 bg-[#1db954] hover:bg-[#1ed760] rounded-full flex items-center justify-center shadow-lg hover:scale-105 transition-all"
            >
                <svg class="w-7 h-7 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
            </button>
            
            @auth
            @php
                $isFavorited = false;
                if (Auth::check()) {
                    $isFavorited = Auth::user()->favorites()
                        ->where('FavoriteType', 'Playlist')
                        ->where('FavoriteID', $playlist->PlaylistID)
                        ->exists();
                }
            @endphp
            <x-like-button 
                entity-type="playlist" 
                entity-id="{{ $playlist->PlaylistID }}" 
                :is-liked="$isFavorited"
            />
            @endauth
        </div>

        <!-- Songs List -->
        @if($playlist->songs && $playlist->songs->count() > 0)
        <div class="mb-8">
            <div class="mb-4 border-b border-zinc-800 pb-2 flex items-center gap-4 text-sm text-zinc-400">
                <div class="w-6 text-center">#</div>
                <div class="flex-1">Title</div>
                <div class="w-32 text-right hidden md:block">Plays</div>
                <div class="w-20 text-right">
                    <svg class="w-5 h-5 inline" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                    </svg>
                </div>
            </div>

            <div class="space-y-1">
                @foreach($playlist->songs as $index => $song)
                <div 
                    id="song-item-{{ $song->IndirimboID }}"
                    onclick="playSongFromList({{ $song->IndirimboID }}, {{ $index }})"
                    class="group flex items-center gap-4 p-2 rounded hover:bg-zinc-800/50 transition-colors cursor-pointer"
                >
                    <div class="w-6 text-center text-zinc-400 group-hover:text-white">{{ $index + 1 }}</div>
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <img 
                            src="{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}" 
                            alt="{{ $song->IndirimboName }}"
                            class="w-10 h-10 rounded object-cover hidden md:block"
                        />
                        <div class="min-w-0 flex-1">
                            <p class="text-white font-medium truncate">{{ $song->IndirimboName }}</p>
                            <p class="text-zinc-400 text-sm truncate">
                                @if($song->artist)
                                    {{ $song->artist->StageName }}
                                @elseif($song->orchestra)
                                    {{ $song->orchestra->OrchestreName }}
                                @elseif($song->itorero)
                                    {{ $song->itorero->ItoreroName }}
                                @else
                                    Unknown
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="w-32 text-right text-zinc-400 hidden md:block">{{ number_format($song->PlayCount ?? 0) }}</div>
                    <div class="w-20 text-right">
                        <button 
                            onclick="event.stopPropagation(); playSongFromList({{ $song->IndirimboID }}, {{ $index }})"
                            class="opacity-0 group-hover:opacity-100 transition-opacity p-2 hover:scale-110"
                        >
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-zinc-400 text-lg">No songs available in this playlist.</p>
        </div>
        @endif

        @if($playlist->Description)
        <div class="bg-zinc-900/50 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-white mb-4">About</h2>
            <div class="text-zinc-300 whitespace-pre-wrap">{{ strip_tags($playlist->Description) }}</div>
        </div>
        @endif
    </div>
</div>

@if($playlist->songs && $playlist->songs->count() > 0)
@php
    $songList = $playlist->songs->map(function($song) {
        return [
            'id' => $song->IndirimboID,
            'uuid' => $song->UUID,
            'name' => $song->IndirimboName,
            'image' => \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture),
            'artist' => $song->artist ? $song->artist->StageName : ($song->orchestra ? $song->orchestra->OrchestreName : ($song->itorero ? $song->itorero->ItoreroName : 'Unknown')),
            'audioUrl' => route('indirimbo.audio', $song->IndirimboID)
        ];
    })->toArray();
@endphp
<script>
    window.artistSongList = @json($songList);
    window.currentSongIndex = -1;

    window.playSongFromList = function(songId, index) {
        const song = window.artistSongList[index];
        if (!song) return;

        document.querySelectorAll('[id^="song-item-"]').forEach(function(item) {
            item.classList.remove('bg-zinc-800');
        });

        const currentSongItem = document.getElementById('song-item-' + songId);
        if (currentSongItem) {
            currentSongItem.classList.add('bg-zinc-800');
        }

        window.currentSongIndex = index;

        const musicPlayer = document.getElementById('music-player');
        if (musicPlayer) {
            musicPlayer.classList.remove('hidden');
        }

        const playerImage = document.getElementById('player-image');
        const playerTitle = document.getElementById('player-title');
        const playerArtist = document.getElementById('player-artist');
        const playerAudio = document.getElementById('bottom-audio-player');

        if (playerImage) playerImage.src = song.image || '/placeholder.svg';
        if (playerTitle) playerTitle.textContent = song.name;
        if (playerArtist) playerArtist.textContent = song.artist;
        if (playerAudio && song.audioUrl) {
            window.currentSongId = song.id;
            playerAudio.src = song.audioUrl;
            playerAudio.currentTime = 0;
            playerAudio.load();
            playerAudio.play().catch(e => console.error('Play error:', e));
        }
    };

    function playAllSongs() {
        if (window.artistSongList && window.artistSongList.length > 0) {
            window.playSongFromList(window.artistSongList[0].id, 0);
        }
    }
</script>
@endif
@endsection
