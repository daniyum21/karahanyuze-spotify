@extends('layouts.app')

@section('title', 'Search Results - Karahanyuze')

@php
    // Helper function to highlight matching text
    function highlightText($text, $query) {
        if (empty($query) || empty($text)) {
            return $text;
        }
        // Escape HTML special characters in the query
        $escapedQuery = htmlspecialchars($query, ENT_QUOTES, 'UTF-8');
        // Split query into words for multi-word highlighting
        $words = preg_split('/\s+/', $escapedQuery);
        $highlighted = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        
        foreach ($words as $word) {
            if (strlen(trim($word)) > 0) {
                // Use case-insensitive highlighting
                $pattern = '/(' . preg_quote($word, '/') . ')/i';
                $highlighted = preg_replace($pattern, '<mark class="bg-green-500/30 text-green-300 rounded px-1">$1</mark>', $highlighted);
            }
        }
        
        return $highlighted;
    }
@endphp

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black">
    <div class="container mx-auto px-4 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Search Results</h1>
            <p class="text-zinc-400">Searching for: <span class="text-green-500 font-semibold">"{{ $query }}"</span></p>
            <p class="text-sm text-zinc-500 mt-2">Found {{ $totalResults }} result(s)</p>
        </div>

        @if($totalResults === 0)
        <div class="text-center py-12 bg-zinc-900 rounded-lg">
            <svg class="w-16 h-16 text-zinc-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <p class="text-zinc-400 text-lg mb-4">No results found for "{{ $query }}"</p>
            <p class="text-zinc-500 text-sm">Try searching with different keywords</p>
        </div>
        @else
        <div class="space-y-12">
            <!-- Songs Results -->
            @if($songs->count() > 0)
            <div>
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                    </svg>
                    Songs ({{ $songs->count() }})
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($songs as $song)
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
                                <h3 class="text-white font-semibold mb-1 line-clamp-2 group-hover:text-green-400 transition-colors">{!! highlightText($song->IndirimboName, $query) !!}</h3>
                                <p class="text-zinc-400 text-sm">
                                    @if($song->artist)
                                        {!! highlightText($song->artist->StageName, $query) !!}
                                    @elseif($song->orchestra)
                                        {!! highlightText($song->orchestra->OrchestreName, $query) !!}
                                    @elseif($song->itorero)
                                        {!! highlightText($song->itorero->ItoreroName, $query) !!}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Artists Results -->
            @if($artists->count() > 0)
            <div>
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Artists ({{ $artists->count() }})
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($artists as $artist)
                    <a href="{{ route('artists.show', $artist->slug) }}" class="group">
                        <div class="bg-zinc-900 rounded-lg overflow-hidden hover:bg-zinc-800 transition-colors">
                            <div class="aspect-square relative">
                                @if($artist->ProfilePicture)
                                <img 
                                    src="{{ \App\Helpers\ImageHelper::getImageUrl($artist->ProfilePicture) }}" 
                                    alt="{{ $artist->StageName }}"
                                    class="w-full h-full object-cover"
                                >
                                @else
                                <div class="w-full h-full bg-zinc-800 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="text-white font-semibold mb-1 group-hover:text-purple-400 transition-colors">{!! highlightText($artist->StageName, $query) !!}</h3>
                                @if($artist->FirstName || $artist->LastName)
                                <p class="text-zinc-400 text-sm">{!! highlightText($artist->FirstName . ' ' . $artist->LastName, $query) !!}</p>
                                @endif
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Orchestras Results -->
            @if($orchestras->count() > 0)
            <div>
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                    </svg>
                    Orchestras ({{ $orchestras->count() }})
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($orchestras as $orchestra)
                    <a href="{{ route('orchestre.show', $orchestra->slug) }}" class="group">
                        <div class="bg-zinc-900 rounded-lg overflow-hidden hover:bg-zinc-800 transition-colors">
                            <div class="aspect-square relative">
                                @if($orchestra->ProfilePicture)
                                <img 
                                    src="{{ \App\Helpers\ImageHelper::getImageUrl($orchestra->ProfilePicture) }}" 
                                    alt="{{ $orchestra->OrchestreName }}"
                                    class="w-full h-full object-cover"
                                >
                                @else
                                <div class="w-full h-full bg-zinc-800 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                    </svg>
                                </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="text-white font-semibold mb-1 group-hover:text-blue-400 transition-colors">{!! highlightText($orchestra->OrchestreName, $query) !!}</h3>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Itoreros Results -->
            @if($itoreros->count() > 0)
            <div>
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Itoreros ({{ $itoreros->count() }})
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($itoreros as $itorero)
                    <a href="{{ route('itorero.show', $itorero->slug) }}" class="group">
                        <div class="bg-zinc-900 rounded-lg overflow-hidden hover:bg-zinc-800 transition-colors">
                            <div class="aspect-square relative">
                                @if($itorero->ProfilePicture)
                                <img 
                                    src="{{ \App\Helpers\ImageHelper::getImageUrl($itorero->ProfilePicture) }}" 
                                    alt="{{ $itorero->ItoreroName }}"
                                    class="w-full h-full object-cover"
                                >
                                @else
                                <div class="w-full h-full bg-zinc-800 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="text-white font-semibold mb-1 group-hover:text-yellow-400 transition-colors">{!! highlightText($itorero->ItoreroName, $query) !!}</h3>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Playlists Results -->
            @if($playlists->count() > 0)
            <div>
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                    <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                    </svg>
                    Playlists ({{ $playlists->count() }})
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($playlists as $playlist)
                    <a href="{{ route('playlists.show', $playlist->slug) }}" class="group">
                        <div class="bg-zinc-900 rounded-lg overflow-hidden hover:bg-zinc-800 transition-colors">
                            <div class="aspect-square relative">
                                @if($playlist->ProfilePicture)
                                <img 
                                    src="{{ \App\Helpers\ImageHelper::getImageUrl($playlist->ProfilePicture) }}" 
                                    alt="{{ $playlist->PlaylistName }}"
                                    class="w-full h-full object-cover"
                                >
                                @else
                                <div class="w-full h-full bg-zinc-800 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                    </svg>
                                </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="text-white font-semibold mb-1 group-hover:text-pink-400 transition-colors">{!! highlightText($playlist->PlaylistName, $query) !!}</h3>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection

