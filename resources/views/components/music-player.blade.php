<div id="music-player" class="fixed bottom-0 left-0 right-0 border-t border-zinc-800 bg-zinc-900/95 backdrop-blur-sm shadow-2xl z-50 hidden">
    <!-- Progress Bar -->
    <div class="h-4 sm:h-5 bg-zinc-800 cursor-pointer hover:bg-zinc-700 active:bg-zinc-600 transition-colors relative touch-none" id="progress-bar" 
         onclick="seekTo(event)" 
         onmousedown="handleProgressMouseDown(event)"
         onmousemove="handleProgressMouseMove(event)"
         onmouseup="handleProgressMouseUp(event)"
         onmouseleave="handleProgressMouseUp(event)"
         ontouchstart="handleProgressTouchStart(event)"
         ontouchmove="handleProgressTouchMove(event)"
         ontouchend="handleProgressTouchEnd(event)">
        <div class="h-full bg-blue-500 transition-all" id="progress-fill" style="width: 0%"></div>
        <!-- Progress indicator dot -->
        <div id="progress-indicator" class="absolute top-1/2 w-5 h-5 sm:w-6 sm:h-6 bg-blue-500 rounded-full border-2 border-white shadow-lg transition-all pointer-events-none -translate-y-1/2" style="left: 0%"></div>
    </div>

    <div class="py-3 flex items-center justify-between gap-4 container mx-auto px-4">
        <!-- Song Info (Left) -->
        <div class="flex items-center gap-3 min-w-0 flex-1">
            <img id="player-image" src="/placeholder.svg" alt="Song" class="w-12 h-12 rounded-lg bg-zinc-800 flex-shrink-0 object-cover" />
            <div class="min-w-0">
                <h4 id="player-title" class="font-semibold text-sm truncate text-white">No song selected</h4>
                <p id="player-artist" class="text-xs text-zinc-400 truncate">Select a song to play</p>
            </div>
        </div>

        <!-- Playback Controls (Center) -->
        <div class="flex items-center gap-3">
            <button id="shuffle-btn" class="p-3 hover:bg-zinc-800 rounded-lg transition-colors min-w-[44px] min-h-[44px] flex items-center justify-center" onclick="toggleShuffle()">
                <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
            </button>
            <button id="prev-btn" class="p-3 hover:bg-zinc-800 rounded-lg transition-colors min-w-[44px] min-h-[44px] flex items-center justify-center" onclick="previousTrack()">
                <svg class="w-6 h-6 text-zinc-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6 6h2v12H6zm3.5 6l8.5-6V6l-8.5 6z"/>
                </svg>
            </button>
            <button id="play-pause-btn" class="p-4 bg-blue-500 hover:bg-blue-600 rounded-full transition-colors flex items-center justify-center min-w-[56px] min-h-[56px]" onclick="togglePlayPause()">
                <svg id="play-icon" class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
                <svg id="pause-icon" class="w-7 h-7 text-white hidden" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/>
                </svg>
            </button>
            <button id="next-btn" class="p-3 hover:bg-zinc-800 rounded-lg transition-colors min-w-[44px] min-h-[44px] flex items-center justify-center" onclick="nextTrack()">
                <svg class="w-6 h-6 text-zinc-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/>
                </svg>
            </button>
            <button id="repeat-btn" class="p-3 hover:bg-zinc-800 rounded-lg transition-colors min-w-[44px] min-h-[44px] flex items-center justify-center" onclick="toggleRepeat()">
                <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
        </div>

        <!-- Time & Volume (Right) -->
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 text-xs text-zinc-400">
                <span id="current-time">0:00</span>
                <span>/</span>
                <span id="total-time">0:00</span>
            </div>
            <button id="volume-btn" class="p-3 hover:bg-zinc-800 rounded-lg transition-colors min-w-[44px] min-h-[44px] flex items-center justify-center" onclick="toggleMute()">
                <svg id="volume-icon" class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 14.142M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                </svg>
                <svg id="mute-icon" class="w-6 h-6 text-zinc-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
                </svg>
            </button>
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
                btn.classList.add('bg-blue-500');
                btn.classList.remove('hover:bg-zinc-800');
            } else {
                btn.classList.remove('bg-blue-500');
                btn.classList.add('hover:bg-zinc-800');
            }
        }
    };

    window.toggleRepeat = function() {
        window.isRepeating = !window.isRepeating;
        const btn = document.getElementById('repeat-btn');
        if (btn) {
            if (window.isRepeating) {
                btn.classList.add('bg-blue-500');
                btn.classList.remove('hover:bg-zinc-800');
            } else {
                btn.classList.remove('bg-blue-500');
                btn.classList.add('hover:bg-zinc-800');
            }
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

