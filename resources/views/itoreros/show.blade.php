@extends('layouts.app')

@section('title', $itorero->ItoreroName . ' - Karahanyuze')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black pb-24">
    <div class="container mx-auto px-4 py-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('itorero.index') }}" class="inline-flex items-center gap-2 text-white hover:text-green-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Back to Itorero</span>
            </a>
        </div>

        <!-- Itorero Header -->
        <div class="flex flex-col md:flex-row gap-8 mb-12">
            <div class="flex-shrink-0">
                <img 
                    src="{{ \App\Helpers\ImageHelper::getImageUrl($itorero->ProfilePicture) }}" 
                    alt="{{ $itorero->ItoreroName }}"
                    class="w-64 h-64 md:w-80 md:h-80 rounded-lg object-cover shadow-2xl"
                />
            </div>
            <div class="flex-1">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">{{ $itorero->ItoreroName }}</h1>

                @if($itorero->Description)
                <p class="text-zinc-300 mb-6 leading-relaxed">{{ strip_tags($itorero->Description) }}</p>
                @endif

                <div class="flex flex-wrap gap-6 text-sm text-zinc-400 mb-6">
                    <div>
                        <span class="font-semibold text-zinc-500">Songs:</span> 
                        <span class="text-white">{{ $itorero->songs->count() }}</span>
                    </div>
                </div>

                <!-- Favorite Button -->
                @auth
                @php
                    $isFavorited = false;
                    if (Auth::check()) {
                        $isFavorited = Auth::user()->favorites()
                            ->where('FavoriteType', 'Itorero')
                            ->where('FavoriteID', $itorero->ItoreroID)
                            ->exists();
                    }
                @endphp
                <x-like-button 
                    entity-type="itorero" 
                    entity-id="{{ $itorero->ItoreroID }}" 
                    :is-liked="$isFavorited"
                />
                @endauth
            </div>
        </div>

        <!-- Songs Section -->
        @if($itorero->songs && $itorero->songs->count() > 0)
        @php
            $songCount = $itorero->songs->count();
            // Estimate duration (assuming average 4 minutes per song if not available)
            $estimatedDuration = $songCount * 4; // in minutes
        @endphp
        <div class="mb-8">
            <!-- Summary and Play All Button -->
            <div class="mb-6">
                <p class="text-white text-lg mb-4">{{ $songCount }} songs • Duration: {{ $estimatedDuration }} min</p>
                <div class="flex gap-3">
                    <button onclick="playAllSongs()" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg py-3 px-6 flex items-center gap-2 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                        Play All
                    </button>
                    <button class="bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg p-3 transition-colors border border-zinc-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Songs List -->
            <div class="space-y-2">
                @foreach($itorero->songs as $index => $song)
                    <x-song-list-item :song="$song" :index="$index" />
                @endforeach
            </div>
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-zinc-400 text-lg">No songs available for this itorero.</p>
        </div>
        @endif
    </div>
</div>

@if($itorero->songs && $itorero->songs->count() > 0)
@php
    $songList = $itorero->songs->map(function($song) {
        return [
            'id' => $song->IndirimboID,
            'name' => $song->IndirimboName,
            'image' => \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture),
            'artist' => $song->artist ? $song->artist->StageName : ($song->orchestra ? $song->orchestra->OrchestreName : ($song->itorero ? $song->itorero->ItoreroName : 'Unknown')),
            'audioUrl' => route('indirimbo.audio', $song->IndirimboID)
        ];
    })->toArray();
@endphp
<script>
    // Store the song list for this itorero page
    window.artistSongList = @json($songList);
    
    window.currentSongIndex = -1;

    window.playSongFromList = function(songId, index) {
        const song = window.artistSongList[index];
        if (!song) return;

        // Remove highlight from all songs
        document.querySelectorAll('[id^="song-item-"]').forEach(function(item) {
            item.classList.remove('bg-blue-500/20', 'border-blue-500');
            item.classList.add('bg-zinc-800');
        });

        // Highlight the current song
        const currentSongItem = document.getElementById('song-item-' + songId);
        if (currentSongItem) {
            currentSongItem.classList.remove('bg-zinc-800');
            currentSongItem.classList.add('bg-blue-500/20', 'border', 'border-blue-500');
        }

        window.currentSongIndex = index;

        // Show the music player
        const musicPlayer = document.getElementById('music-player');
        if (musicPlayer) {
            musicPlayer.classList.remove('hidden');
        }

        // Update player info
        const playerImage = document.getElementById('player-image');
        const playerTitle = document.getElementById('player-title');
        const playerArtist = document.getElementById('player-artist');
        const playerAudio = document.getElementById('bottom-audio-player');

        if (playerImage) playerImage.src = song.image;
        if (playerTitle) playerTitle.textContent = song.name;
        if (playerArtist) playerArtist.textContent = song.artist;
        if (playerAudio) {
            // Re-initialize player events to ensure ended event is attached
            if (typeof window.initializePlayerEvents === 'function') {
                window.initializePlayerEvents();
                // Get fresh reference after re-initialization
                const freshPlayer = document.getElementById('bottom-audio-player');
                if (freshPlayer) {
                    freshPlayer.src = song.audioUrl;
                    freshPlayer.currentTime = 0; // Reset to beginning
                    freshPlayer.load();
                    
                    // Wait for audio to be ready before playing
                    freshPlayer.addEventListener('canplay', function playWhenReady() {
                        freshPlayer.play().catch(function(error) {
                            console.log('Auto-play prevented (browser policy):', error);
                        });
                        freshPlayer.removeEventListener('canplay', playWhenReady);
                    }, { once: true });
                }
            } else {
                playerAudio.src = song.audioUrl;
                playerAudio.currentTime = 0; // Reset to beginning
                playerAudio.load();
                
                // Wait for audio to be ready before playing
                playerAudio.addEventListener('canplay', function playWhenReady() {
                    playerAudio.play().catch(function(error) {
                        console.log('Auto-play prevented (browser policy):', error);
                    });
                    playerAudio.removeEventListener('canplay', playWhenReady);
                }, { once: true });
            }
        }
    };

    function playAllSongs() {
        if (window.artistSongList && window.artistSongList.length > 0) {
            window.playSongFromList(window.artistSongList[0].id, 0);
        }
    }

    // downloadSong function for itorero song lists
    function downloadSong(songId) {
        // Use the download route which tracks downloads
        const downloadUrl = `/download/${songId}`;
        window.location.href = downloadUrl;
    }

    // Auto-play first song when itorero page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Wait for player to initialize
        setTimeout(function() {
            if (window.artistSongList && window.artistSongList.length > 0) {
                // Initialize player events first
                if (typeof window.initializePlayerEvents === 'function') {
                    window.initializePlayerEvents();
                }
                
                // Show the music player first
                const musicPlayer = document.getElementById('music-player');
                if (musicPlayer) {
                    musicPlayer.classList.remove('hidden');
                }
                
                // Auto-play first song
                const firstSong = window.artistSongList[0];
                const playerImage = document.getElementById('player-image');
                const playerTitle = document.getElementById('player-title');
                const playerArtist = document.getElementById('player-artist');
                const playerAudio = document.getElementById('bottom-audio-player');
                
                // Update player info
                if (playerImage) playerImage.src = firstSong.image;
                if (playerTitle) playerTitle.textContent = firstSong.name;
                if (playerArtist) playerArtist.textContent = firstSong.artist;
                
                // Highlight first song
                const firstSongItem = document.getElementById('song-item-' + firstSong.id);
                if (firstSongItem) {
                    firstSongItem.classList.remove('bg-zinc-800');
                    firstSongItem.classList.add('bg-blue-500/20', 'border', 'border-blue-500');
                }
                
                window.currentSongIndex = 0;
                
                if (playerAudio) {
                    playerAudio.src = firstSong.audioUrl;
                    playerAudio.load();
                    
                    // Wait for audio to be ready before playing
                    const playWhenReady = function() {
                        playerAudio.play().catch(function(error) {
                            console.log('Auto-play prevented (browser policy):', error);
                            // If autoplay is blocked, at least the player is ready
                        });
                    };
                    
                    if (playerAudio.readyState >= 2) {
                        // Audio is already loaded
                        playWhenReady();
                    } else {
                        // Wait for audio to load
                        playerAudio.addEventListener('canplay', playWhenReady, { once: true });
                        playerAudio.addEventListener('loadeddata', playWhenReady, { once: true });
                    }
                }
            }
        }, 200);
    });

</script>
@endif
@endsection

