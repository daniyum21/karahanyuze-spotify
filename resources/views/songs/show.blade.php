@extends('layouts.app')

@section('title', $song->IndirimboName . ' - Karahanyuze')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black pb-24">
    <div class="container mx-auto px-4 py-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ url()->previous() !== request()->url() ? url()->previous() : route('home') }}" class="inline-flex items-center gap-2 text-white hover:text-green-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Back</span>
            </a>
        </div>

        <!-- Song Header -->
        <div class="flex flex-col md:flex-row gap-8 mb-12">
            <div class="flex-shrink-0">
                <img 
                    src="{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}" 
                    alt="{{ $song->IndirimboName }}"
                    class="w-64 h-64 md:w-80 md:h-80 rounded-lg object-cover shadow-2xl"
                />
            </div>
            <div class="flex-1">
                <!-- Category Tag -->
                <div class="mb-3">
                    <span class="text-blue-400 text-sm font-medium uppercase tracking-wide">
                        @if($song->orchestra)
                            Orchestre
                        @elseif($song->itorero)
                            Itorero
                        @elseif($song->playlists && $song->playlists->count() > 0)
                            Playlist
                        @else
                            Indirimbo
                        @endif
                    </span>
                </div>

                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">{{ $song->IndirimboName }}</h1>
                
                @if($song->owner())
                <div class="mb-6">
                    <p class="text-white text-lg">
                        by 
                        @if($song->artist)
                            <a href="{{ route('artists.show', $song->artist->slug) }}" class="text-green-500 hover:text-green-400 font-semibold transition-colors">
                                {{ $song->artist->StageName }}
                            </a>
                        @elseif($song->orchestra)
                            <a href="{{ route('orchestre.show', $song->orchestra->slug) }}" class="text-green-500 hover:text-green-400 font-semibold transition-colors">
                                {{ $song->orchestra->OrchestreName }}
                            </a>
                        @elseif($song->itorero)
                            <a href="{{ route('itorero.show', $song->itorero->slug) }}" class="text-green-500 hover:text-green-400 font-semibold transition-colors">
                                {{ $song->itorero->ItoreroName }}
                            </a>
                        @endif
                    </p>
                </div>
                @endif

                <!-- Metadata -->
                <div class="flex flex-wrap gap-8 mb-6 text-white">
                    <div>
                        <p class="text-xs text-zinc-400 mb-1">Year</p>
                        <p class="font-semibold">{{ $song->created_at ? $song->created_at->format('Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-400 mb-1">Duration</p>
                        <p class="font-semibold" id="song-duration">--:--</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-400 mb-1">Plays</p>
                        <p class="font-semibold">{{ number_format($song->PlayCount ?? 0) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-400 mb-1">Downloads</p>
                        <p class="font-semibold">{{ number_format($song->DownloadCount ?? 0) }}</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4 mb-8">
                    @php
                        $isFavorited = false;
                        if (Auth::check()) {
                            // Check if this specific song is favorited
                            // Must have FavoriteType = 'Song' or FavoriteType is NULL (legacy)
                            // And (FavoriteID = song ID OR IndirimboID = song ID for legacy compatibility)
                            // This ensures we only match actual song favorites, not orchestra/playlist favorites
                            $isFavorited = \App\Models\Favorite::where('UserID', Auth::id())
                                ->where(function($query) use ($song) {
                                    $query->where(function($q) use ($song) {
                                        // New polymorphic format: FavoriteType = 'Song' and FavoriteID = song ID
                                        $q->where('FavoriteType', 'Song')
                                          ->where('FavoriteID', $song->IndirimboID);
                                    })->orWhere(function($q) use ($song) {
                                        // Legacy format: FavoriteType is NULL and IndirimboID = song ID
                                        $q->whereNull('FavoriteType')
                                          ->where('IndirimboID', $song->IndirimboID);
                                    });
                                })
                                ->exists();
                        }
                    @endphp
                    <x-like-button 
                        entity-type="song" 
                        entity-id="{{ $song->IndirimboID }}" 
                        :is-liked="$isFavorited"
                    />
                    <x-download-button song-id="{{ $song->IndirimboID }}" />
                    <x-share-button :title="$song->IndirimboName" />
                </div>

            </div>
        </div>

        <!-- Hidden Audio Player for duration detection -->
        @if($song->IndirimboUrl)
        <audio 
            id="song-audio-player" 
            preload="metadata"
            style="display: none;"
        >
            <source src="{{ route('indirimbo.audio', $song->IndirimboID) }}" type="audio/mpeg">
        </audio>
        @endif

        <!-- About This Song Section -->
        <div class="bg-zinc-900 rounded-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-white mb-6">About This Song</h2>
            @if(!empty($song->Description))
            <div class="prose prose-invert max-w-none">
                <div class="text-zinc-300 leading-relaxed text-lg">
                    {!! $song->Description !!}
                </div>
            </div>
            @else
            <div class="prose prose-invert max-w-none">
                <p class="text-zinc-400 leading-relaxed text-lg italic">
                    No description available for this song.
                </p>
            </div>
            @endif
        </div>

        <!-- Lyrics Section -->
        <div class="bg-zinc-900 rounded-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-white mb-6">Lyrics</h2>
            @if($song->Lyrics)
            <div class="max-h-96 overflow-y-auto pr-4">
                <div class="text-zinc-300 leading-relaxed text-lg">
                    {!! strip_tags($song->Lyrics, '<br><p><div><span><strong><em><b><i><u>') !!}
                </div>
            </div>
            @else
            <div class="text-center py-12">
                <p class="text-zinc-400 text-lg">Iyi ndirimbo ntamagambo igite.</p>
                <p class="text-zinc-500 text-sm mt-2">This song has no lyrics available.</p>
            </div>
            @endif
        </div>

        <!-- Related Playlists -->
        @if($song->playlists && $song->playlists->count() > 0)
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-white mb-6">In Playlists</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($song->playlists as $playlist)
                <a href="{{ route('playlists.show', $playlist->slug) }}" class="bg-zinc-800 rounded-lg p-4 hover:bg-zinc-700 transition-colors block">
                    <div class="flex items-center gap-4">
                        <img 
                            src="{{ \App\Helpers\ImageHelper::getImageUrl($playlist->ProfilePicture) }}" 
                            alt="{{ $playlist->PlaylistName }}"
                            class="w-16 h-16 rounded object-cover"
                        />
                        <div class="flex-1">
                            <h3 class="font-semibold text-white mb-1">{{ $playlist->PlaylistName }}</h3>
                            <p class="text-sm text-zinc-400">{{ $playlist->songs->count() }} songs</p>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    let isLiked = {{ $isFavorited ? 'true' : 'false' }};
    let isPlaying = false;

    // Initialize bottom player and auto-play when page loads
    document.addEventListener('DOMContentLoaded', function() {
        const audioPlayer = document.getElementById('song-audio-player');
        const audioUrl = '{{ $song->IndirimboUrl ? route("indirimbo.audio", $song->IndirimboID) : "" }}';
        const musicPlayer = document.getElementById('music-player');
        
        // Show the music player immediately
        if (musicPlayer) {
            musicPlayer.classList.remove('hidden');
        }
        
        // Update bottom player with song info
        updateBottomPlayer();
        
        // Wait a bit for the player to be initialized, then auto-play
        setTimeout(function() {
            const player = document.getElementById('bottom-audio-player');
            
            if (player && audioUrl) {
                // Re-initialize player events to ensure they're attached
                if (typeof window.initializePlayerEvents === 'function') {
                    window.initializePlayerEvents();
                }
                
                // Get fresh reference after re-initialization
                const freshPlayer = document.getElementById('bottom-audio-player');
                if (!freshPlayer) return;
                
                // Ensure audio source is set
                if (freshPlayer.src !== audioUrl) {
                    freshPlayer.src = audioUrl;
                }
                
                // Add error handler
                freshPlayer.addEventListener('error', function(e) {
                    console.error('Audio load error:', e);
                    console.error('Audio URL:', audioUrl);
                    console.error('Player src:', freshPlayer.src);
                }, { once: true });
                
                // Load the audio
                freshPlayer.load();
                
                // Wait for audio to be ready, then play
                const playAudio = function() {
                    if (freshPlayer.readyState >= 2) {
                        freshPlayer.play().then(function() {
                            console.log('Auto-play started successfully');
                            // Update play/pause button state
                            if (typeof window.isPlaying !== 'undefined') {
                                window.isPlaying = true;
                                const playPauseBtn = document.getElementById('play-pause-btn');
                                const playIcon = document.getElementById('play-icon');
                                const pauseIcon = document.getElementById('pause-icon');
                                if (playIcon) playIcon.classList.add('hidden');
                                if (pauseIcon) pauseIcon.classList.remove('hidden');
                            }
                        }).catch(function(error) {
                            console.log('Auto-play prevented (browser policy):', error);
                            // User will need to click play manually
                        });
                    }
                };
                
                // Try to play immediately if ready, otherwise wait for canplay/loadeddata
                if (freshPlayer.readyState >= 2) {
                    playAudio();
                } else {
                    freshPlayer.addEventListener('canplay', playAudio, { once: true });
                    freshPlayer.addEventListener('loadeddata', playAudio, { once: true });
                }
            }
        }, 500); // Increased delay to ensure player is fully initialized
        
        // Get duration from hidden player if available
        if (audioPlayer) {
            audioPlayer.addEventListener('loadedmetadata', function() {
                const duration = audioPlayer.duration;
                if (duration && !isNaN(duration)) {
                    const minutes = Math.floor(duration / 60);
                    const seconds = Math.floor(duration % 60);
                    const durationText = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
                    const durationEl = document.getElementById('song-duration');
                    if (durationEl) {
                        durationEl.textContent = durationText;
                    }
                }
            });
        }
    });


    function toggleLike() {
        @guest
        // Redirect to login if not authenticated
        window.location.href = '{{ route("login") }}';
        return;
        @endguest

        const likeBtn = document.getElementById('like-btn');
        const svg = likeBtn.querySelector('svg');
        const songId = {{ $song->IndirimboID }};
        
        // Send AJAX request to toggle favorite
        fetch(`/favorites/${songId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin',
            redirect: 'manual' // Don't follow redirects automatically
        })
        .then(async response => {
            // Handle redirects (302, 301, etc.)
            if (response.type === 'opaqueredirect' || response.status === 302 || response.status === 301) {
                if (typeof showErrorNotification === 'function') {
                    showErrorNotification('Please log in to favorite items.');
                }
                setTimeout(() => {
                    window.location.href = '{{ route("login") }}';
                }, 1500);
                return null;
            }
            
            // Handle authentication errors
            if (response.status === 401) {
                let message = 'Please log in to favorite items.';
                try {
                    const errorData = await response.json();
                    if (errorData.message) {
                        message = errorData.message;
                    }
                } catch (e) {
                    // Ignore JSON parse errors
                }
                if (typeof showErrorNotification === 'function') {
                    showErrorNotification(message);
                }
                setTimeout(() => {
                    window.location.href = '{{ route("login") }}';
                }, 1500);
                return null;
            }
            
            // Handle email verification errors
            if (response.status === 403) {
                let message = 'Please verify your email address.';
                let redirectUrl = '{{ route("verification.notice") }}';
                try {
                    const errorData = await response.json();
                    if (errorData.message) {
                        message = errorData.message;
                    }
                    if (errorData.redirect) {
                        redirectUrl = errorData.redirect;
                    }
                } catch (e) {
                    // Ignore JSON parse errors
                }
                if (typeof showErrorNotification === 'function') {
                    showErrorNotification(message);
                }
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 1500);
                return null;
            }
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                // If not JSON, likely a redirect or HTML error page
                if (typeof showErrorNotification === 'function') {
                    showErrorNotification('Please log in to favorite items.');
                }
                setTimeout(() => {
                    window.location.href = '{{ route("login") }}';
                }, 1500);
                return null;
            }
            
            if (!response.ok) {
                // Try to get error message from response
                try {
                    const errorData = await response.json();
                    throw new Error(errorData.message || errorData.error || 'An error occurred');
                } catch (e) {
                    if (e.message && e.message !== 'Unexpected token < in JSON at position 0') {
                        throw e;
                    }
                    throw new Error('An error occurred. Please try again.');
                }
            }
            
            return response.json();
        })
        .then(data => {
            if (!data) return; // Already handled redirect
            
            if (data.success) {
                isLiked = data.isFavorited;
                
                if (isLiked) {
                    likeBtn.classList.add('bg-pink-500', 'hover:bg-pink-600');
                    likeBtn.classList.remove('bg-zinc-800', 'hover:bg-zinc-700');
                    svg.setAttribute('fill', 'currentColor');
                } else {
                    likeBtn.classList.remove('bg-pink-500', 'hover:bg-pink-600');
                    likeBtn.classList.add('bg-zinc-800', 'hover:bg-zinc-700');
                    svg.setAttribute('fill', 'none');
                }
            } else if (data.error) {
                if (typeof showErrorNotification === 'function') {
                    showErrorNotification(data.message || data.error || 'An error occurred. Please try again.');
                }
            }
        })
        .catch(error => {
            console.error('Error toggling favorite:', error);
            // Show user-friendly error message
            const message = error.message || 'An error occurred. Please try again.';
            if (typeof showErrorNotification === 'function') {
                showErrorNotification(message);
            }
            if (message.includes('log in') || message.includes('Unauthorized') || message.includes('login')) {
                setTimeout(() => {
                    window.location.href = '{{ route("login") }}';
                }, 1500);
            }
        });
    }

    // downloadSong function is now in download-button component
    function downloadSong() {
        // Use the download route which tracks downloads
        const downloadUrl = '{{ route("indirimbo.download", $song->IndirimboID) }}';
        window.location.href = downloadUrl;
    }


    function updateBottomPlayer() {
        // Update the bottom music player with current song info
        const songTitle = '{{ $song->IndirimboName }}';
        const artistName = @if($song->artist)
                            '{{ $song->artist->StageName }}'
                        @elseif($song->orchestra)
                            '{{ $song->orchestra->OrchestreName }}'
                        @elseif($song->itorero)
                            '{{ $song->itorero->ItoreroName }}'
                        @else
                            'Unknown Artist'
                        @endif;
        const songImage = '{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}';

        // Update bottom player elements if they exist
        const playerImage = document.getElementById('player-image');
        const playerTitle = document.getElementById('player-title');
        const playerArtist = document.getElementById('player-artist');

        if (playerImage) playerImage.src = songImage;
        if (playerTitle) playerTitle.textContent = songTitle;
        if (playerArtist) playerArtist.textContent = artistName;
    }
</script>
@endsection

