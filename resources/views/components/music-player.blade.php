<div id="music-player" class="fixed bottom-0 left-0 right-0 border-t border-zinc-800 bg-zinc-900 shadow-2xl z-50 hidden" style="height: 90px;">

    <div class="h-full flex items-center justify-between gap-4 px-4">
        <!-- Song Info (Left) -->
        <div class="flex items-center gap-3 min-w-0 flex-1" style="min-width: 180px;">
            <img id="player-image" src="/placeholder.svg" alt="Song" class="w-14 h-14 rounded bg-zinc-800 flex-shrink-0 object-cover" />
            <div class="min-w-0">
                <h4 id="player-title" class="font-medium text-sm truncate text-white hover:underline cursor-pointer">No song selected</h4>
                <p id="player-artist" class="text-xs text-zinc-400 truncate hover:underline cursor-pointer">Select a song to play</p>
            </div>
            <button class="text-zinc-400 hover:text-white transition-colors hidden lg:block">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            </button>
        </div>

        <!-- Playback Controls (Center) -->
        <div class="flex flex-col items-center gap-2 flex-1 max-w-[722px]">
            <div class="flex items-center gap-2">
                <button id="shuffle-btn" class="p-2 hover:text-white transition-colors text-zinc-400" onclick="toggleShuffle()">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M10.59 9.17L5.41 4 4 5.41l5.17 5.17 1.42-1.41zM14.5 4l2.04 2.04L4 18.59 5.41 20 17.96 7.46 20 9.5V4h-5.5zm.33 9.41l-1.41 1.41 3.13 3.13L14.5 20H20v-5.5l-2.04 2.04-3.13-3.13z"/>
                    </svg>
                </button>
                <button id="prev-btn" class="p-2 hover:text-white transition-colors text-zinc-400" onclick="previousTrack()">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6 6h2v12H6zm3.5 6l8.5-6V6l-8.5 6z"/>
                    </svg>
                </button>
                <button id="play-pause-btn" class="w-10 h-10 bg-white hover:scale-105 rounded-full transition-all flex items-center justify-center shadow-lg" onclick="togglePlayPause()">
                    <svg id="play-icon" class="w-6 h-6 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                    <svg id="pause-icon" class="w-6 h-6 text-black hidden" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/>
                    </svg>
                </button>
                <button id="next-btn" class="p-2 hover:text-white transition-colors text-zinc-400" onclick="nextTrack()">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/>
                    </svg>
                </button>
                <button id="repeat-btn" class="p-2 hover:text-white transition-colors text-zinc-400" onclick="toggleRepeat()">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7.5 21H2V9h5.5c3.04 0 5.5 2.46 5.5 5.5S10.54 20 7.5 20zm-1.5-2h1.5c1.93 0 3.5-1.57 3.5-3.5S9.43 11 7.5 11H6v8zM22 9h-5.5C13.46 9 11 11.46 11 14.5S13.46 20 16.5 20H22v-2h-5.5c-1.93 0-3.5-1.57-3.5-3.5s1.57-3.5 3.5-3.5H22V9z"/>
                    </svg>
                </button>
            </div>
            <div class="flex items-center gap-2 w-full">
                <span id="current-time" class="text-xs text-zinc-400" style="min-width: 40px;">0:00</span>
                <div class="flex-1 h-1 bg-zinc-700 rounded-full cursor-pointer hover:bg-[#1db954] transition-colors relative" id="progress-bar" 
                     onclick="seekTo(event)" 
                     onmousedown="handleProgressMouseDown(event)"
                     onmousemove="handleProgressMouseMove(event)"
                     onmouseup="handleProgressMouseUp(event)"
                     onmouseleave="handleProgressMouseUp(event)"
                     ontouchstart="handleProgressTouchStart(event)"
                     ontouchmove="handleProgressTouchMove(event)"
                     ontouchend="handleProgressTouchEnd(event)">
                    <div class="h-full bg-white rounded-full transition-all" id="progress-fill" style="width: 0%"></div>
                    <div id="progress-indicator" class="absolute top-1/2 w-3 h-3 bg-white rounded-full border-2 border-zinc-900 shadow-lg transition-all pointer-events-none -translate-y-1/2 opacity-0 hover:opacity-100" style="left: 0%"></div>
                </div>
                <span id="total-time" class="text-xs text-zinc-400" style="min-width: 40px;">0:00</span>
            </div>
        </div>

        <!-- Volume Control (Right) -->
        <div class="flex items-center gap-2 flex-1 justify-end" style="min-width: 180px;">
            <button id="volume-btn" class="p-2 hover:text-white transition-colors text-zinc-400" onclick="toggleMute()">
                <svg id="volume-icon" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                </svg>
                <svg id="mute-icon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
                </svg>
            </button>
            <div class="hidden lg:flex items-center gap-2 w-32">
                <div class="flex-1 h-1 bg-zinc-700 rounded-full cursor-pointer hover:bg-[#1db954] transition-colors relative" id="volume-bar" onclick="setVolume(event)">
                    <div class="h-full bg-white rounded-full transition-all" id="volume-fill" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Audio Player -->
    <audio id="bottom-audio-player" preload="metadata"></audio>
</div>

<script>
    // Make functions globally available
    window.isPlaying = false;
    window.isMuted = false;
    window.currentVolume = 1.0;
    window.isShuffled = false;
    window.isRepeating = false;
    window.isDragging = false;

    // Get player elements - use function to ensure we get fresh references
    window.getPlayerElements = function() {
        return {
            audioPlayer: document.getElementById('bottom-audio-player'),
            playPauseBtn: document.getElementById('play-pause-btn'),
            playIcon: document.getElementById('play-icon'),
            pauseIcon: document.getElementById('pause-icon'),
            currentTimeEl: document.getElementById('current-time'),
            totalTimeEl: document.getElementById('total-time'),
            progressFill: document.getElementById('progress-fill')
        };
    };

    // Initialize event listeners on the audio player
    window.initializePlayerEvents = function() {
        const player = document.getElementById('bottom-audio-player');
        if (!player) return;
        
        // Store the current src and state before cloning
        const currentSrc = player.src;
        const currentTime = player.currentTime;
        const wasPlaying = !player.paused;
        
        // Remove old listeners by cloning (to reset event listeners)
        const newPlayer = player.cloneNode(true);
        player.parentNode.replaceChild(newPlayer, player);
        const audioPlayer = document.getElementById('bottom-audio-player');
        
        // Restore the src and state if they existed
        if (currentSrc) {
            audioPlayer.src = currentSrc;
            audioPlayer.load();
            if (wasPlaying && !isNaN(currentTime)) {
                audioPlayer.currentTime = currentTime;
            }
        }
        
        if (audioPlayer) {
            audioPlayer.addEventListener('loadedmetadata', function() {
                const elements = window.getPlayerElements();
                if (elements.totalTimeEl && audioPlayer.duration && !isNaN(audioPlayer.duration)) {
                    const duration = audioPlayer.duration;
                    const minutes = Math.floor(duration / 60);
                    const seconds = Math.floor(duration % 60);
                    elements.totalTimeEl.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
                }
            });

            audioPlayer.addEventListener('timeupdate', function() {
                const elements = window.getPlayerElements();
                if (audioPlayer.duration && !isNaN(audioPlayer.duration) && elements.progressFill && elements.currentTimeEl) {
                    const progress = (audioPlayer.currentTime / audioPlayer.duration) * 100;
                    elements.progressFill.style.width = progress + '%';
                    
                    // Update progress indicator position
                    const progressIndicator = document.getElementById('progress-indicator');
                    if (progressIndicator) {
                        progressIndicator.style.left = progress + '%';
                    }
                    
                    const minutes = Math.floor(audioPlayer.currentTime / 60);
                    const seconds = Math.floor(audioPlayer.currentTime % 60);
                    elements.currentTimeEl.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
                }
            });

            audioPlayer.addEventListener('play', function() {
                const elements = window.getPlayerElements();
                window.isPlaying = true;
                if (elements.playIcon) elements.playIcon.classList.add('hidden');
                if (elements.pauseIcon) elements.pauseIcon.classList.remove('hidden');
                
                // Track listening history
                trackListeningHistory();
            });

            audioPlayer.addEventListener('pause', function() {
                const elements = window.getPlayerElements();
                window.isPlaying = false;
                if (elements.playIcon) elements.playIcon.classList.remove('hidden');
                if (elements.pauseIcon) elements.pauseIcon.classList.add('hidden');
            });

            audioPlayer.addEventListener('ended', function() {
                const elements = window.getPlayerElements();
                window.isPlaying = false;
                if (elements.playIcon) elements.playIcon.classList.remove('hidden');
                if (elements.pauseIcon) elements.pauseIcon.classList.add('hidden');
                
                // Auto-play next song if we're on an artist page with a song list
                if (window.artistSongList && window.currentSongIndex >= 0) {
                    if (window.currentSongIndex < window.artistSongList.length - 1) {
                        // Play next song
                        const nextIndex = window.currentSongIndex + 1;
                        const nextSong = window.artistSongList[nextIndex];
                        if (window.playSongFromList) {
                            window.playSongFromList(nextSong.id, nextIndex);
                        }
                    } else if (window.isRepeating) {
                        // Loop back to first song if repeating
                        const firstSong = window.artistSongList[0];
                        if (window.playSongFromList) {
                            window.playSongFromList(firstSong.id, 0);
                        }
                    }
                } else if (window.isRepeating) {
                    // Original repeat behavior
                    audioPlayer.play();
                }
            });
        }
    };

    // Make control functions globally available
    window.togglePlayPause = function() {
        const player = document.getElementById('bottom-audio-player');
        if (!player) {
            console.error('Audio player not found');
            return;
        }
        
        // Ensure audio source is loaded
        if (!player.src || player.src === '') {
            console.log('No audio source available');
            return;
        }
        
        // Show player if it's hidden
        const musicPlayer = document.getElementById('music-player');
        if (musicPlayer && musicPlayer.classList.contains('hidden')) {
            musicPlayer.classList.remove('hidden');
        }
        
        // Toggle play/pause
        if (player.paused) {
            player.play().catch(function(error) {
                console.error('Error playing audio:', error);
            });
        } else {
            player.pause();
        }
    };

    window.handleProgressMouseDown = function(event) {
        window.isDragging = true;
        window.seekTo(event);
    };

    window.handleProgressMouseMove = function(event) {
        if (window.isDragging) {
            window.seekTo(event);
        }
    };

    window.handleProgressMouseUp = function(event) {
        if (window.isDragging) {
            window.isDragging = false;
            window.seekTo(event);
        }
    };

    // Touch event handlers for mobile
    window.handleProgressTouchStart = function(event) {
        event.preventDefault();
        window.isDragging = true;
        const touch = event.touches[0];
        const fakeEvent = {
            clientX: touch.clientX,
            preventDefault: () => {},
            stopPropagation: () => {}
        };
        window.seekTo(fakeEvent);
    };

    window.handleProgressTouchMove = function(event) {
        if (window.isDragging) {
            event.preventDefault();
            const touch = event.touches[0];
            const fakeEvent = {
                clientX: touch.clientX,
                preventDefault: () => {},
                stopPropagation: () => {}
            };
            window.seekTo(fakeEvent);
        }
    };

    window.handleProgressTouchEnd = function(event) {
        if (window.isDragging) {
            event.preventDefault();
            window.isDragging = false;
            const touch = event.changedTouches[0];
            const fakeEvent = {
                clientX: touch.clientX,
                preventDefault: () => {},
                stopPropagation: () => {}
            };
            window.seekTo(fakeEvent);
        }
    };

    window.seekTo = function(event) {
        event.preventDefault();
        event.stopPropagation();
        const player = document.getElementById('bottom-audio-player');
        if (player && player.duration && !isNaN(player.duration)) {
            const progressBar = document.getElementById('progress-bar');
            if (!progressBar) return;
            
            const rect = progressBar.getBoundingClientRect();
            const x = event.clientX - rect.left;
            const percentage = Math.max(0, Math.min(1, x / rect.width));
            player.currentTime = percentage * player.duration;
            
            // Update progress indicator position
            const progressIndicator = document.getElementById('progress-indicator');
            if (progressIndicator) {
                progressIndicator.style.left = (percentage * 100) + '%';
            }
            
            // Update progress fill
            const progressFill = document.getElementById('progress-fill');
            if (progressFill) {
                progressFill.style.width = (percentage * 100) + '%';
            }
        }
    };

    window.toggleMute = function() {
        const player = document.getElementById('bottom-audio-player');
        if (player) {
            window.isMuted = !window.isMuted;
            player.muted = window.isMuted;
            const volumeIcon = document.getElementById('volume-icon');
            const muteIcon = document.getElementById('mute-icon');
            if (window.isMuted) {
                if (volumeIcon) volumeIcon.classList.add('hidden');
                if (muteIcon) muteIcon.classList.remove('hidden');
            } else {
                if (volumeIcon) volumeIcon.classList.remove('hidden');
                if (muteIcon) muteIcon.classList.add('hidden');
            }
        }
    };

    window.toggleShuffle = function() {
        window.isShuffled = !window.isShuffled;
        const btn = document.getElementById('shuffle-btn');
        if (btn) {
            if (window.isShuffled) {
                btn.classList.add('text-[#1db954]');
                btn.classList.remove('text-zinc-400');
            } else {
                btn.classList.remove('text-[#1db954]');
                btn.classList.add('text-zinc-400');
            }
        }
    };

    window.toggleRepeat = function() {
        window.isRepeating = !window.isRepeating;
        const btn = document.getElementById('repeat-btn');
        if (btn) {
            if (window.isRepeating) {
                btn.classList.add('text-[#1db954]');
                btn.classList.remove('text-zinc-400');
            } else {
                btn.classList.remove('text-[#1db954]');
                btn.classList.add('text-zinc-400');
            }
        }
    };

    window.setVolume = function(event) {
        const volumeBar = document.getElementById('volume-bar');
        if (!volumeBar) return;
        
        const rect = volumeBar.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const percentage = Math.max(0, Math.min(1, x / rect.width));
        window.currentVolume = percentage;
        
        const player = document.getElementById('bottom-audio-player');
        if (player) {
            player.volume = percentage;
            player.muted = false;
            window.isMuted = false;
            
            const volumeIcon = document.getElementById('volume-icon');
            const muteIcon = document.getElementById('mute-icon');
            if (volumeIcon) volumeIcon.classList.remove('hidden');
            if (muteIcon) muteIcon.classList.add('hidden');
        }
        
        const volumeFill = document.getElementById('volume-fill');
        if (volumeFill) {
            volumeFill.style.width = (percentage * 100) + '%';
        }
    };

    window.previousTrack = function() {
        // Check if we're on an artist page with a song list
        if (window.artistSongList && window.currentSongIndex >= 0) {
            if (window.currentSongIndex > 0) {
                const prevIndex = window.currentSongIndex - 1;
                const prevSong = window.artistSongList[prevIndex];
                if (window.playSongFromList) {
                    window.playSongFromList(prevSong.id, prevIndex);
                }
            } else if (window.isRepeating) {
                // Loop to last song if repeating
                const lastIndex = window.artistSongList.length - 1;
                const lastSong = window.artistSongList[lastIndex];
                if (window.playSongFromList) {
                    window.playSongFromList(lastSong.id, lastIndex);
                }
            }
        } else {
            // Original behavior: skip back 10 seconds
            const player = document.getElementById('bottom-audio-player');
            if (player && player.duration && !isNaN(player.duration)) {
                if (player.currentTime > 10) {
                    player.currentTime -= 10;
                } else {
                    player.currentTime = 0;
                }
            }
        }
    };

    window.nextTrack = function() {
        // Check if we're on an artist page with a song list
        if (window.artistSongList && window.currentSongIndex >= 0) {
            if (window.currentSongIndex < window.artistSongList.length - 1) {
                const nextIndex = window.currentSongIndex + 1;
                const nextSong = window.artistSongList[nextIndex];
                if (window.playSongFromList) {
                    window.playSongFromList(nextSong.id, nextIndex);
                }
            } else if (window.isRepeating) {
                // Loop back to first song if repeating
                const firstSong = window.artistSongList[0];
                if (window.playSongFromList) {
                    window.playSongFromList(firstSong.id, 0);
                }
            }
        } else {
            // Original behavior: skip forward 10 seconds
            const player = document.getElementById('bottom-audio-player');
            if (player && player.duration && !isNaN(player.duration)) {
                if (player.currentTime + 10 < player.duration) {
                    player.currentTime += 10;
                } else {
                    player.currentTime = player.duration;
                }
            }
        }
    };

    // Track listening history
    window.trackListeningHistory = function() {
        const player = document.getElementById('bottom-audio-player');
        if (!player || !player.src) return;
        
        // Extract song ID from current song data (if available)
        const songId = window.currentSongId || null;
        if (!songId) return;
        
        // Track play via API
        fetch('/listening-history/track', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                song_id: songId,
                duration: player.duration || null
            })
        }).catch(err => console.log('Failed to track listening history:', err));
    };

    // Store current song ID when playing
    window.currentSongId = null;
    
    // Define playSongFromPlayer function
    window.playSongFromPlayer = function(uuid, imageUrl, title, artist, audioUrl) {
        const player = document.getElementById('bottom-audio-player');
        const playerImage = document.getElementById('player-image');
        const playerTitle = document.getElementById('player-title');
        const playerArtist = document.getElementById('player-artist');
        const musicPlayer = document.getElementById('music-player');
        
        if (!player) {
            console.error('Audio player not found');
            return;
        }
        
        if (!audioUrl || audioUrl === '') {
            console.error('No audio URL provided');
            return;
        }
        
        // Update player info
        if (playerImage) playerImage.src = imageUrl || '/placeholder.svg';
        if (playerTitle) playerTitle.textContent = title || 'Unknown';
        if (playerArtist) playerArtist.textContent = artist || 'Unknown Artist';
        
        // Show player
        if (musicPlayer) {
            musicPlayer.classList.remove('hidden');
        }
        
        // Set audio source and play
        player.src = audioUrl;
        player.load();
        
        player.play().catch(function(error) {
            console.error('Error playing audio:', error);
            console.log('Audio URL:', audioUrl);
        });
    };
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        window.initializePlayerEvents();
    });
    
    // Also initialize immediately if DOM is already loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', window.initializePlayerEvents);
    } else {
        window.initializePlayerEvents();
    }
</script>

