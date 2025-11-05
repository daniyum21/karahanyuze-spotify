@extends('layouts.app')

@section('title', $playlist->PlaylistName . ' - Admin')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-6xl mx-auto">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2">{{ $playlist->PlaylistName }}</h1>
                    <p class="text-zinc-400">Playlist Details</p>
                </div>
                <div class="flex gap-3">
                    <a 
                        href="{{ route('admin.playlists.edit', $playlist->UUID) }}" 
                        class="bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
                    >
                        Edit Playlist
                    </a>
                    <a 
                        href="{{ route('admin.playlists.index') }}" 
                        class="bg-zinc-800 hover:bg-zinc-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
                    >
                        Back to Playlists
                    </a>
                </div>
            </div>

            @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Cover Image -->
                <div class="lg:col-span-1">
                    <div class="bg-zinc-900 rounded-lg p-6">
                        @if($playlist->ProfilePicture)
                            <img 
                                src="{{ \App\Helpers\ImageHelper::getImageUrl($playlist->ProfilePicture) }}" 
                                alt="{{ $playlist->PlaylistName }}"
                                class="w-full aspect-square object-cover rounded-lg mb-4"
                            >
                        @else
                            <div class="w-full aspect-square bg-zinc-800 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-24 h-24 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                </svg>
                            </div>
                        @endif
                        
                        <div class="space-y-3">
                            @if($playlist->IsFeatured)
                                <span class="inline-block px-3 py-1 text-sm rounded-full bg-blue-500/20 text-blue-400">Featured</span>
                            @endif
                            <div class="text-zinc-400 text-sm">
                                <p><span class="text-white font-medium">{{ $playlist->songs->count() }}</span> songs</p>
                                @if($playlist->created_at)
                                    <p class="mt-2">Created {{ $playlist->created_at->format('M d, Y') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Playlist Info -->
                <div class="lg:col-span-2">
                    <div class="bg-zinc-900 rounded-lg p-6">
                        <h2 class="text-2xl font-bold text-white mb-4">Playlist Information</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-zinc-400 mb-1">Name</label>
                                <p class="text-white text-lg">{{ $playlist->PlaylistName }}</p>
                            </div>
                            
                            @if($playlist->Description)
                            <div>
                                <label class="block text-sm font-medium text-zinc-400 mb-1">Description</label>
                                <p class="text-white">{{ $playlist->Description }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Songs List -->
            <div class="bg-zinc-900 rounded-lg p-6">
                <h2 class="text-2xl font-bold text-white mb-6">Songs ({{ $playlist->songs->count() }})</h2>
                
                @if($playlist->songs->count() > 0)
                <div class="space-y-3">
                    @foreach($playlist->songs as $song)
                    <div class="flex items-center gap-4 p-4 bg-zinc-800 rounded-lg hover:bg-zinc-700 transition-colors">
                        @if($song->ProfilePicture)
                            <img 
                                src="{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}" 
                                alt="{{ $song->IndirimboName }}"
                                class="w-16 h-16 rounded object-cover"
                            >
                        @else
                            <div class="w-16 h-16 rounded bg-zinc-700 flex items-center justify-center">
                                <svg class="w-8 h-8 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1">
                            <h3 class="text-white font-semibold">{{ $song->IndirimboName }}</h3>
                            @if($song->artist)
                                <p class="text-zinc-400 text-sm">{{ $song->artist->StageName }}</p>
                            @endif
                        </div>
                        <a 
                            href="{{ route('admin.songs.edit', $song->UUID) }}" 
                            class="text-green-400 hover:text-green-300 transition-colors text-sm font-medium"
                        >
                            Edit Song
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
                    <p class="text-zinc-400 text-lg mb-4">No songs in this playlist yet.</p>
                    <a 
                        href="{{ route('admin.playlists.edit', $playlist->UUID) }}" 
                        class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
                    >
                        Add Songs
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

