@extends('layouts.app')

@section('title', 'Playlists - Karahanyuze')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black">
    <div class="container mx-auto px-4 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Playlists</h1>
            <p class="text-zinc-400">Browse all playlists</p>
        </div>

        @if($playlists->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($playlists as $playlist)
            <a href="{{ route('playlists.show', $playlist->slug) }}" class="bg-zinc-800 rounded-lg p-4 hover:bg-zinc-700 transition-colors block group">
                <div class="mb-4">
                    <img 
                        src="{{ \App\Helpers\ImageHelper::getImageUrl($playlist->ProfilePicture) }}" 
                        alt="{{ $playlist->PlaylistName }}"
                        class="w-full aspect-square object-cover rounded-lg"
                    />
                </div>
                <h3 class="font-semibold text-white mb-1">{{ $playlist->PlaylistName }}</h3>
                <p class="text-sm text-zinc-400">{{ $playlist->songs->count() }} songs</p>
                @if($playlist->Description)
                <p class="text-sm text-zinc-500 mt-2 line-clamp-2">{{ strip_tags($playlist->Description) }}</p>
                @endif
            </a>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $playlists->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-zinc-400 text-lg">No playlists found.</p>
        </div>
        @endif
    </div>
</div>
@endsection

