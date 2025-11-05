@props([
    'song' => null,
    'index' => 0,
    'showNumber' => true,
    'showDuration' => true,
])

@php
    $songYear = $song->created_at ? $song->created_at->format('Y') : 'N/A';
    $artistName = $song->artist ? $song->artist->StageName : ($song->orchestra ? $song->orchestra->OrchestreName : ($song->itorero ? $song->itorero->ItoreroName : 'Unknown'));
    $duration = $showDuration ? (rand(3, 5) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT)) : null;
@endphp

<div id="song-item-{{ $song->IndirimboID }}" class="bg-zinc-800 hover:bg-zinc-700/80 rounded-lg p-4 transition-colors group">
    <div class="flex items-center gap-4">
        @if($showNumber)
        <!-- Song Number -->
        <div class="text-zinc-400 text-sm font-medium w-8 flex-shrink-0">
            {{ $index + 1 }}
        </div>
        @endif
        
        <!-- Song Title and Artist -->
        <div class="flex-1 min-w-0">
            <a href="{{ route('indirimbo.show', [$song->slug, $song->UUID]) }}" onclick="playSongFromList({{ $song->IndirimboID }}, {{ $index }}); return false;" class="block cursor-pointer">
                <h3 class="font-bold text-white text-lg mb-1 hover:text-green-500 transition-colors">
                    {{ $song->IndirimboName }}
                </h3>
                <p class="text-zinc-400 text-sm">
                    {{ $artistName }} • {{ $songYear }}
                </p>
            </a>
        </div>
        
        <!-- Action Icons -->
        <div class="flex items-center gap-3">
            <button onclick="playSongFromList({{ $song->IndirimboID }}, {{ $index }}); return false;" class="p-3 hover:bg-zinc-600 rounded-lg transition-colors min-w-[44px] min-h-[44px] flex items-center justify-center" title="Play">
                <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
            </button>
            <button onclick="downloadSong({{ $song->IndirimboID }})" class="p-3 hover:bg-zinc-600 rounded-lg transition-colors min-w-[44px] min-h-[44px] flex items-center justify-center" title="Download">
                <svg class="w-6 h-6 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
            </button>
            <a href="{{ route('indirimbo.show', [$song->slug, $song->UUID]) }}" class="p-3 hover:bg-zinc-600 rounded-lg transition-colors min-w-[44px] min-h-[44px] flex items-center justify-center" title="More details">
                <svg class="w-6 h-6 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </a>
        </div>
        
        @if($showDuration && $duration)
        <!-- Duration -->
        <div class="text-white text-sm font-medium w-16 text-right flex-shrink-0">
            {{ $duration }}
        </div>
        @endif
    </div>
</div>

