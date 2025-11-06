@extends('layouts.spotify')

@section('title', 'Playlists - Karahanyuze')

@section('content')
<div class="px-6 py-8 pb-24">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">Playlists</h1>
        <p class="text-zinc-400">Browse all playlists</p>
    </div>

    @if($playlists->count() > 0)
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
                    <h3 class="font-semibold text-white mb-1 truncate text-sm group-hover:underline">{{ $playlist->PlaylistName }}</h3>
                    <p class="text-xs text-zinc-400">{{ $playlist->songs->count() }} songs</p>
                </a>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $playlists->links() }}
    </div>
    @else
    <div class="text-center py-12 bg-zinc-900/50 rounded-lg">
        <p class="text-zinc-400 text-lg">No playlists found.</p>
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
@endsection
