@extends('layouts.app')

@section('title', 'My Dashboard - Karahanyuze')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black">
    <div class="container mx-auto px-4 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">My Dashboard</h1>
            <p class="text-zinc-400">Welcome back, {{ Auth::user()->FirstName ?? Auth::user()->UserName }}!</p>
            <p class="text-sm text-zinc-500 mt-2">Your favorite songs</p>
        </div>

        @if($favoriteSongs->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($favoriteSongs as $song)
            <a href="{{ route('indirimbo.show', [$song->slug, $song->UUID]) }}" class="group">
                <div class="bg-zinc-900 rounded-lg overflow-hidden hover:bg-zinc-800 transition-colors">
                    <div class="aspect-square relative">
                        @if($song->ProfilePicture)
                        <img 
                            src="{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}" 
                            alt="{{ $song->IndirimboName }}"
                            class="w-full h-full object-cover"
                        >
                        @else
                        <div class="w-full h-full bg-zinc-800 flex items-center justify-center">
                            <svg class="w-16 h-16 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                            </svg>
                        </div>
                        @endif
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                            <svg class="w-12 h-12 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-white font-semibold mb-1 line-clamp-2 group-hover:text-green-400 transition-colors">{{ $song->IndirimboName }}</h3>
                        <p class="text-zinc-400 text-sm">
                            @if($song->artist)
                                {{ $song->artist->StageName }}
                            @elseif($song->orchestra)
                                {{ $song->orchestra->OrchestreName }}
                            @elseif($song->itorero)
                                {{ $song->itorero->ItoreroName }}
                            @endif
                        </p>
                        <p class="text-xs text-zinc-500 mt-2">Added {{ isset($song->favorited_at) ? \Carbon\Carbon::parse($song->favorited_at)->diffForHumans() : 'Recently' }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $favoriteSongs->links() }}
        </div>
        @else
        <div class="text-center py-12 bg-zinc-900 rounded-lg">
            <svg class="w-16 h-16 text-zinc-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <p class="text-zinc-400 text-lg mb-4">No favorite songs yet</p>
            <p class="text-zinc-500 text-sm mb-6">Start liking songs to see them here!</p>
            <a 
                href="{{ route('home') }}" 
                class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
            >
                Browse Songs
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

