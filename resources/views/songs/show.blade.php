@extends('layouts.spotify')

@section('title', $song->IndirimboName . ' - Karahanyuze')

@section('content')
<div class="relative">
    <!-- Gradient Header -->
    <div class="absolute top-0 left-0 right-0 h-[400px] bg-gradient-to-b from-[#1db954] via-[#1db954]/80 to-black z-0"></div>
    
    <div class="relative z-10 px-6 pt-8 pb-24">
        <!-- Header Section -->
        <div class="flex items-end gap-6 mb-8">
            <img 
                src="{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}" 
                alt="{{ $song->IndirimboName }}"
                class="w-[232px] h-[232px] rounded-lg object-cover shadow-2xl flex-shrink-0"
            />
            <div class="flex-1 pb-4">
                <p class="text-sm text-white mb-2">Song</p>
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-4">{{ $song->IndirimboName }}</h1>
                
                @if($song->owner())
                <div class="mb-4">
                    <p class="text-white text-lg">
                        <a href="{{ $song->artist ? route('artists.show', $song->artist->slug) : ($song->orchestra ? route('orchestre.show', $song->orchestra->slug) : route('itorero.show', $song->itorero->slug)) }}" class="hover:underline">
                            {{ $song->artist ? $song->artist->StageName : ($song->orchestra ? $song->orchestra->OrchestreName : $song->itorero->ItoreroName) }}
                        </a>
                    </p>
                </div>
                @endif

                <div class="flex items-center gap-2 text-sm text-zinc-300">
                    <span>{{ number_format($song->PlayCount ?? 0) }} plays</span>
                    <span>•</span>
                    <span id="song-duration">--:--</span>
                </div>
            </div>
        </div>

        <!-- Play Button and Actions -->
        <div class="flex items-center gap-4 mb-8">
            <button 
                onclick="playCurrentSong()"
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
                    $isFavorited = \App\Models\Favorite::where('UserID', Auth::id())
                        ->where(function($query) use ($song) {
                            $query->where(function($q) use ($song) {
                                $q->where('FavoriteType', 'Song')
                                  ->where('FavoriteID', $song->IndirimboID);
                            })->orWhere(function($q) use ($song) {
                                $q->whereNull('FavoriteType')
                                  ->where('FavoriteID', $song->IndirimboID);
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
            @endauth
        </div>

        <!-- Song Details -->
        <div class="bg-zinc-900/50 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-white mb-4">About this song</h2>
            
            @if($song->Description)
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-zinc-400 mb-2">Description</h3>
                <div class="text-zinc-300 whitespace-pre-wrap">{{ strip_tags($song->Description) }}</div>
            </div>
            @endif

            @if($song->Lyrics)
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-zinc-400 mb-2">Lyrics</h3>
                <div class="text-zinc-300 whitespace-pre-wrap">{{ strip_tags($song->Lyrics) }}</div>
            </div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-zinc-400 mb-1">Year</p>
                    <p class="text-white">{{ $song->created_at ? $song->created_at->format('Y') : 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-zinc-400 mb-1">Plays</p>
                    <p class="text-white">{{ number_format($song->PlayCount ?? 0) }}</p>
                </div>
                <div>
                    <p class="text-zinc-400 mb-1">Downloads</p>
                    <p class="text-white">{{ number_format($song->DownloadCount ?? 0) }}</p>
                </div>
                <div>
                    <p class="text-zinc-400 mb-1">Duration</p>
                    <p class="text-white" id="song-duration-footer">--:--</p>
                </div>
            </div>
        </div>

        <!-- Related Songs -->
        @if($song->artist && $song->artist->songs->count() > 1)
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-white mb-6">More by {{ $song->artist->StageName }}</h2>
            <div class="overflow-x-auto scrollbar-hide -mx-6 px-6">
                <div class="flex gap-4" style="width: max-content;">
                    @foreach($song->artist->songs->where('IndirimboID', '!=', $song->IndirimboID)->take(10) as $relatedSong)
                    <div class="group flex-shrink-0 w-[180px]">
                        <a href="{{ route('indirimbo.show', [$relatedSong->slug, $relatedSong->UUID]) }}" class="block p-4 bg-zinc-900/50 hover:bg-zinc-800 rounded-lg transition-all duration-200 cursor-pointer">
                            <div class="relative mb-4">
                                <img 
                                    src="{{ \App\Helpers\ImageHelper::getImageUrl($relatedSong->ProfilePicture) }}" 
                                    alt="{{ $relatedSong->IndirimboName }}"
                                    class="w-full aspect-square object-cover rounded-lg shadow-lg"
                                />
                                <button 
                                    onclick="event.preventDefault(); playSong('{{ $relatedSong->UUID }}', '{{ \App\Helpers\ImageHelper::getImageUrl($relatedSong->ProfilePicture) }}', '{{ addslashes($relatedSong->IndirimboName) }}', '{{ addslashes($song->artist->StageName) }}', '{{ route('indirimbo.audio', $relatedSong->IndirimboID) }}', {{ $relatedSong->IndirimboID }});"
                                    class="absolute bottom-2 right-2 w-12 h-12 bg-[#1db954] rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-lg hover:scale-110 z-10"
                                >
                                    <svg class="w-6 h-6 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </button>
                            </div>
                            <h3 class="font-semibold text-white mb-1 truncate text-sm">{{ $relatedSong->IndirimboName }}</h3>
                            <p class="text-xs text-zinc-400 truncate">{{ $song->artist->StageName }}</p>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function playCurrentSong() {
    const audioUrl = '{{ route('indirimbo.audio', $song->IndirimboID) }}';
    const imageUrl = '{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}';
    const title = '{{ addslashes($song->IndirimboName) }}';
    const artist = '{{ addslashes($song->owner() ? $song->owner()->name : 'Unknown') }}';
    
    window.currentSongId = {{ $song->IndirimboID }};
    playSong('{{ $song->UUID }}', imageUrl, title, artist, audioUrl);
}

function playSong(uuid, imageUrl, title, artist, audioUrl) {
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

// Update duration when audio loads
document.addEventListener('DOMContentLoaded', function() {
    const audio = document.createElement('audio');
    audio.src = '{{ route('indirimbo.audio', $song->IndirimboID) }}';
    audio.addEventListener('loadedmetadata', function() {
        const duration = audio.duration;
        if (duration && !isNaN(duration)) {
            const minutes = Math.floor(duration / 60);
            const seconds = Math.floor(duration % 60);
            const durationText = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            document.getElementById('song-duration').textContent = durationText;
            document.getElementById('song-duration-footer').textContent = durationText;
        }
    });
});
</script>

<style>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
@endsection
