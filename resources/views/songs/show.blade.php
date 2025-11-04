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
                        Traditional Karahanyuze
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
                        <p class="text-xs text-zinc-400 mb-1">Downloads</p>
                        <p class="font-semibold">0</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4 mb-8">
                    @php
                        $isFavorited = false;
                        if (Auth::check()) {
                            $isFavorited = Auth::user()->favoriteSongs()->where('Favorites.IndirimboID', $song->IndirimboID)->exists();
                        }
                    @endphp
                    <button 
                        id="like-btn"
                        class="px-6 py-3 {{ $isFavorited ? 'bg-red-500 hover:bg-red-600' : 'bg-zinc-800 hover:bg-zinc-700' }} text-white font-semibold rounded-lg transition-colors flex items-center gap-2"
                        onclick="toggleLike()"
                    >
                        <svg class="w-5 h-5" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        {{ $isFavorited ? 'Liked' : 'Like' }}
                    </button>
                    <button 
                        class="px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold rounded-lg transition-colors flex items-center gap-2"
                        onclick="downloadSong()"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download
                    </button>
                    <div class="relative">
                        <button 
                            id="share-btn"
                            class="px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold rounded-lg transition-colors flex items-center gap-2"
                            onclick="toggleShareMenu()"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                            Share
                        </button>
                        <!-- Share Menu Dropdown -->
                        <div id="share-menu" class="hidden absolute right-0 mt-2 w-48 bg-zinc-800 rounded-lg shadow-xl border border-zinc-700 z-50">
                            <div class="py-2">
                                <a href="#" onclick="shareToFacebook(); return false;" class="flex items-center gap-3 px-4 py-3 text-white hover:bg-zinc-700 transition-colors">
                                    <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    <span>Share on Facebook</span>
                                </a>
                                <a href="#" onclick="shareToTwitter(); return false;" class="flex items-center gap-3 px-4 py-3 text-white hover:bg-zinc-700 transition-colors">
                                    <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                    </svg>
                                    <span>Share on X</span>
                                </a>
                                <a href="#" onclick="copyLink(); return false;" class="flex items-center gap-3 px-4 py-3 text-white hover:bg-zinc-700 transition-colors border-t border-zinc-700">
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    <span>Copy Link</span>
                                </a>
                            </div>
                        </div>
                    </div>
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
                <p class="text-zinc-300 leading-relaxed text-lg">
                    {{ $song->Description }}
                </p>
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
                <div class="text-zinc-300 whitespace-pre-wrap leading-relaxed text-lg">
                    {!! nl2br(e($song->Lyrics)) !!}
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
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                isLiked = data.isFavorited;
                
                if (isLiked) {
                    likeBtn.classList.add('bg-red-500', 'hover:bg-red-600');
                    likeBtn.classList.remove('bg-zinc-800', 'hover:bg-zinc-700');
                    svg.setAttribute('fill', 'currentColor');
                } else {
                    likeBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
                    likeBtn.classList.add('bg-zinc-800', 'hover:bg-zinc-700');
                    svg.setAttribute('fill', 'none');
                }
            }
        })
        .catch(error => {
            console.error('Error toggling favorite:', error);
            alert('An error occurred. Please try again.');
        });
    }

    function downloadSong() {
        const audioUrl = '{{ route("indirimbo.audio", $song->IndirimboID) }}';
        const songTitle = '{{ $song->IndirimboName }}';
        const filename = songTitle.replace(/\s+/g, '-') + '.mp3';
        const a = document.createElement('a');
        a.href = audioUrl;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    function toggleShareMenu() {
        const shareMenu = document.getElementById('share-menu');
        if (shareMenu) {
            shareMenu.classList.toggle('hidden');
        }
    }

    function shareToFacebook() {
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent('{{ $song->IndirimboName }}');
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${title}`, '_blank', 'width=600,height=400');
        document.getElementById('share-menu').classList.add('hidden');
    }

    function shareToTwitter() {
        const url = encodeURIComponent(window.location.href);
        const text = encodeURIComponent('{{ $song->IndirimboName }} - Karahanyuze');
        window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400');
        document.getElementById('share-menu').classList.add('hidden');
    }

    function copyLink() {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(function() {
            // Show feedback
            const shareBtn = document.getElementById('share-btn');
            const originalText = shareBtn.innerHTML;
            shareBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Copied!';
            shareBtn.classList.add('bg-green-500');
            setTimeout(function() {
                shareBtn.innerHTML = originalText;
                shareBtn.classList.remove('bg-green-500');
            }, 2000);
            document.getElementById('share-menu').classList.add('hidden');
        }).catch(function() {
            alert('Failed to copy link. Please copy manually: ' + url);
            document.getElementById('share-menu').classList.add('hidden');
        });
    }

    // Close share menu when clicking outside
    document.addEventListener('click', function(event) {
        const shareBtn = document.getElementById('share-btn');
        const shareMenu = document.getElementById('share-menu');
        if (shareMenu && shareBtn && !shareMenu.contains(event.target) && !shareBtn.contains(event.target)) {
            shareMenu.classList.add('hidden');
        }
    });


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

