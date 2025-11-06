@extends('layouts.spotify')

@section('title', 'Karahanyuze - Rwandan Music Heritage')

@section('content')
<div class="px-6 py-8 pb-24">
    <!-- Greeting Section -->
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
            @auth
            Good {{ date('H') < 12 ? 'morning' : (date('H') < 18 ? 'afternoon' : 'evening') }}
            @else
            Welcome to Karahanyuze
            @endauth
        </h1>
    </div>

    <!-- Recently Played (for logged-in users) -->
    @auth
    @if($recentlyPlayed->count() > 0)
    <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">Recently Played</h2>
            <a href="#" class="text-sm text-zinc-400 hover:text-white hover:underline font-medium">Show all</a>
        </div>
        <div class="grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
            @foreach($recentlyPlayed as $song)
            <div class="group min-w-0">
                <a href="{{ route('indirimbo.show', [$song->slug, $song->UUID]) }}" class="block p-0 bg-transparent hover:bg-zinc-800/50 rounded-lg transition-all duration-200 cursor-pointer">
                    <div class="relative mb-2 w-full overflow-hidden rounded-lg" style="padding-bottom: 100%;">
                        <img 
                            src="{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}" 
                            alt="{{ $song->IndirimboName }}"
                            class="absolute top-0 left-0 w-full h-full object-cover rounded-lg"
                            style="max-width: 100%; max-height: 100%; width: 100%; height: 100%;"
                            loading="lazy"
                        />
                        <button 
                            onclick="event.preventDefault(); playSong('{{ $song->UUID }}', '{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}', '{{ addslashes($song->IndirimboName) }}', '{{ addslashes($song->owner() ? $song->owner()->name : 'Unknown') }}', '{{ route('indirimbo.audio', $song->IndirimboID) }}', {{ $song->IndirimboID }});"
                            class="absolute bottom-1.5 right-1.5 w-9 h-9 bg-[#1db954] rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-lg hover:scale-110 z-10"
                        >
                            <svg class="w-4 h-4 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </button>
                    </div>
                    <h3 class="font-semibold text-white mb-0.5 truncate text-[14px] leading-tight">{{ $song->IndirimboName }}</h3>
                    <p class="text-[13px] text-zinc-400 truncate leading-tight">
                        @if($song->artist)
                            {{ $song->artist->StageName }}
                        @elseif($song->orchestra)
                            {{ $song->orchestra->OrchestreName }}
                        @elseif($song->itorero)
                            {{ $song->itorero->ItoreroName }}
                        @else
                            Unknown Artist
                        @endif
                    </p>
                </a>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Made For You -->
    @if($madeForYou->count() > 0)
    <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">Made For You</h2>
            <a href="#" class="text-sm text-zinc-400 hover:text-white hover:underline font-medium">Show all</a>
        </div>
        <div class="grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
            @foreach($madeForYou as $song)
            <div class="group min-w-0">
                <a href="{{ route('indirimbo.show', [$song->slug, $song->UUID]) }}" class="block p-0 bg-transparent hover:bg-zinc-800/50 rounded-lg transition-all duration-200 cursor-pointer">
                    <div class="relative mb-2 w-full overflow-hidden rounded-lg" style="padding-bottom: 100%;">
                        <img 
                            src="{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}" 
                            alt="{{ $song->IndirimboName }}"
                            class="absolute top-0 left-0 w-full h-full object-cover rounded-lg"
                            style="max-width: 100%; max-height: 100%; width: 100%; height: 100%;"
                            loading="lazy"
                        />
                        <button 
                            onclick="event.preventDefault(); playSong('{{ $song->UUID }}', '{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}', '{{ addslashes($song->IndirimboName) }}', '{{ addslashes($song->owner() ? $song->owner()->name : 'Unknown') }}', '{{ route('indirimbo.audio', $song->IndirimboID) }}', {{ $song->IndirimboID }});"
                            class="absolute bottom-1.5 right-1.5 w-9 h-9 bg-[#1db954] rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-lg hover:scale-110 z-10"
                        >
                            <svg class="w-4 h-4 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </button>
                    </div>
                    <h3 class="font-semibold text-white mb-0.5 truncate text-[14px] leading-tight">{{ $song->IndirimboName }}</h3>
                    <p class="text-[13px] text-zinc-400 truncate leading-tight">
                        @if($song->artist)
                            {{ $song->artist->StageName }}
                        @elseif($song->orchestra)
                            {{ $song->orchestra->OrchestreName }}
                        @elseif($song->itorero)
                            {{ $song->itorero->ItoreroName }}
                        @else
                            Unknown Artist
                        @endif
                    </p>
                </a>
            </div>
            @endforeach
        </div>
    </section>
    @endif
    @endauth

    <!-- Recently Added Songs - Horizontal Scroll -->
    @if($recentSongs->count() > 0)
    <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">Recently Added</h2>
            <a href="#" class="text-sm text-zinc-400 hover:text-white hover:underline font-medium">Show all</a>
        </div>
        <div class="grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
            @foreach($recentSongs as $song)
            <div class="group min-w-0">
                <a href="{{ route('indirimbo.show', [$song->slug, $song->UUID]) }}" class="block p-0 bg-transparent hover:bg-zinc-800/50 rounded-lg transition-all duration-200 cursor-pointer">
                    <div class="relative mb-2 w-full overflow-hidden rounded-lg" style="padding-bottom: 100%;">
                        <img 
                            src="{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}" 
                            alt="{{ $song->IndirimboName }}"
                            class="absolute top-0 left-0 w-full h-full object-cover rounded-lg"
                            style="max-width: 100%; max-height: 100%; width: 100%; height: 100%;"
                            loading="lazy"
                        />
                        <button 
                            onclick="event.preventDefault(); playSong('{{ $song->UUID }}', '{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}', '{{ addslashes($song->IndirimboName) }}', '{{ addslashes($song->owner() ? $song->owner()->name : 'Unknown') }}', '{{ route('indirimbo.audio', $song->IndirimboID) }}', {{ $song->IndirimboID }});"
                            class="absolute bottom-1.5 right-1.5 w-9 h-9 bg-[#1db954] rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-lg hover:scale-110 z-10"
                        >
                            <svg class="w-4 h-4 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </button>
                    </div>
                    <h3 class="font-semibold text-white mb-0.5 truncate text-[14px] leading-tight">{{ $song->IndirimboName }}</h3>
                    <p class="text-[13px] text-zinc-400 truncate leading-tight">
                        @if($song->artist)
                            {{ $song->artist->StageName }}
                        @elseif($song->orchestra)
                            {{ $song->orchestra->OrchestreName }}
                        @elseif($song->itorero)
                            {{ $song->itorero->ItoreroName }}
                        @else
                            Unknown Artist
                        @endif
                    </p>
                </a>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Featured Artists - Horizontal Scroll -->
    @if($featuredArtists->count() > 0)
    <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">Popular Artists</h2>
            <a href="{{ route('artists.index') }}" class="text-sm text-zinc-400 hover:text-white hover:underline font-medium">Show all</a>
        </div>
        <div class="grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
            @foreach($featuredArtists as $artist)
            <div class="group min-w-0">
                <a href="{{ route('artists.show', $artist->slug) }}" class="block p-0 bg-transparent hover:bg-zinc-800/50 rounded-lg transition-all duration-200 cursor-pointer">
                    <div class="relative mb-2 w-full overflow-hidden rounded-full" style="padding-bottom: 100%;">
                        <img 
                            src="{{ \App\Helpers\ImageHelper::getImageUrl($artist->ProfilePicture) }}" 
                            alt="{{ $artist->StageName }}"
                            class="absolute top-0 left-0 w-full h-full object-cover rounded-full"
                            style="max-width: 100%; max-height: 100%; width: 100%; height: 100%;"
                            loading="lazy"
                        />
                        <button 
                            onclick="event.preventDefault(); window.location.href='{{ route('artists.show', $artist->slug) }}';"
                            class="absolute bottom-1.5 right-1.5 w-9 h-9 bg-[#1db954] rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-lg hover:scale-110 z-10"
                        >
                            <svg class="w-4 h-4 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </button>
                    </div>
                    <h3 class="font-semibold text-white mb-0.5 truncate text-[14px] leading-tight group-hover:underline">{{ $artist->StageName }}</h3>
                    <p class="text-[13px] text-zinc-400">Artist</p>
                </a>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Featured Playlists - Horizontal Scroll -->
    @if($featuredPlaylists->count() > 0)
    <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">Popular Playlists</h2>
            <a href="{{ route('playlists.index') }}" class="text-sm text-zinc-400 hover:text-white hover:underline font-medium">Show all</a>
        </div>
        <div class="grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
            @foreach($featuredPlaylists as $playlist)
            <div class="group min-w-0">
                <a href="{{ route('playlists.show', $playlist->slug) }}" class="block p-0 bg-transparent hover:bg-zinc-800/50 rounded-lg transition-all duration-200 cursor-pointer">
                    <div class="relative mb-2 w-full overflow-hidden rounded-lg" style="padding-bottom: 100%;">
                        <img 
                            src="{{ \App\Helpers\ImageHelper::getImageUrl($playlist->ProfilePicture) }}" 
                            alt="{{ $playlist->PlaylistName }}"
                            class="absolute top-0 left-0 w-full h-full object-cover rounded-lg"
                            style="max-width: 100%; max-height: 100%; width: 100%; height: 100%;"
                            loading="lazy"
                        />
                        <button 
                            onclick="event.preventDefault(); window.location.href='{{ route('playlists.show', $playlist->slug) }}';"
                            class="absolute bottom-1.5 right-1.5 w-9 h-9 bg-[#1db954] rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-lg hover:scale-110 z-10"
                        >
                            <svg class="w-4 h-4 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </button>
                    </div>
                    <h3 class="font-semibold text-white mb-0.5 truncate text-[14px] leading-tight group-hover:underline">{{ $playlist->PlaylistName }}</h3>
                    <p class="text-[13px] text-zinc-400">{{ $playlist->songs->count() }} songs</p>
                </a>
            </div>
            @endforeach
        </div>
    </section>
    @endif
</div>


<script>
function playSong(uuid, imageUrl, title, artist, audioUrl, songId = null) {
    if (songId) {
        window.currentSongId = songId;
    }
    
    // Check if audio URL is valid
    if (!audioUrl || audioUrl === '') {
        console.error('No audio URL provided for song:', title);
        return;
    }
    
    // Use playSongFromPlayer if available, otherwise use fallback
    if (typeof window.playSongFromPlayer === 'function') {
        window.playSongFromPlayer(uuid, imageUrl, title, artist, audioUrl);
    } else {
        // Fallback to direct player update
        const player = document.getElementById('bottom-audio-player');
        const playerImage = document.getElementById('player-image');
        const playerTitle = document.getElementById('player-title');
        const playerArtist = document.getElementById('player-artist');
        const musicPlayer = document.getElementById('music-player');
        
        if (!player) {
            console.error('Audio player not found');
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
    }
}
</script>
@endsection
