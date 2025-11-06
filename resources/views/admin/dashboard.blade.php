@extends('layouts.spotify')

@section('title', 'Admin Dashboard - Karahanyuze')

@section('content')
<div class="px-6 py-8 pb-24">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">Admin Dashboard</h1>
        <p class="text-zinc-400">Welcome back, {{ Auth::user()->FirstName ?? Auth::user()->UserName }}!</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Songs -->
        <a href="{{ route('admin.songs.index') }}" class="bg-zinc-900/50 hover:bg-zinc-800 rounded-lg p-6 transition-colors cursor-pointer block group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-[#1db954]/20 rounded-lg flex items-center justify-center group-hover:bg-[#1db954]/30 transition-colors">
                    <svg class="w-6 h-6 text-[#1db954]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                    </svg>
                </div>
                <span class="text-[#1db954] hover:text-[#1ed760] text-sm font-medium">
                    View All →
                </span>
            </div>
            <h3 class="text-3xl font-bold text-white mb-1">{{ $totalSongs }}</h3>
            <p class="text-zinc-400 text-sm">Total Songs</p>
        </a>

        <!-- Approved Songs -->
        <a href="{{ route('admin.songs.index', ['status' => 'approved']) }}" class="bg-zinc-900/50 hover:bg-zinc-800 rounded-lg p-6 transition-colors cursor-pointer block group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center group-hover:bg-green-500/30 transition-colors">
                    <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                </div>
                <span class="text-green-500 hover:text-green-400 text-sm font-medium">
                    Manage →
                </span>
            </div>
            <h3 class="text-3xl font-bold text-white mb-1">{{ $approvedSongs }}</h3>
            <p class="text-zinc-400 text-sm">Approved Songs</p>
        </a>

        <!-- Pending Songs -->
        <a href="{{ route('admin.songs.index', ['status' => 'pending']) }}" class="bg-zinc-900/50 hover:bg-zinc-800 rounded-lg p-6 transition-colors cursor-pointer block group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center group-hover:bg-yellow-500/30 transition-colors">
                    <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                </div>
                @if($pendingSongs > 0)
                <span class="text-yellow-500 hover:text-yellow-400 text-sm font-medium">
                    Review →
                </span>
                @endif
            </div>
            <h3 class="text-3xl font-bold text-white mb-1">{{ $pendingSongs }}</h3>
            <p class="text-zinc-400 text-sm">Pending Songs</p>
        </a>

        <!-- Featured Songs -->
        <a href="{{ route('admin.songs.index', ['featured' => 'yes']) }}" class="bg-zinc-900/50 hover:bg-zinc-800 rounded-lg p-6 transition-colors cursor-pointer block group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:bg-purple-500/30 transition-colors">
                    <svg class="w-6 h-6 text-purple-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                    </svg>
                </div>
                <span class="text-purple-500 hover:text-purple-400 text-sm font-medium">
                    Manage →
                </span>
            </div>
            <h3 class="text-3xl font-bold text-white mb-1">{{ $featuredSongs }}</h3>
            <p class="text-zinc-400 text-sm">Featured Songs</p>
        </a>
    </div>

    <!-- Additional Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Artists -->
        <a href="{{ route('admin.artists.index') }}" class="bg-zinc-900/50 hover:bg-zinc-800 rounded-lg p-6 transition-colors cursor-pointer block group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-[#1db954]/20 rounded-lg flex items-center justify-center group-hover:bg-[#1db954]/30 transition-colors">
                    <svg class="w-6 h-6 text-[#1db954]" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
                <span class="text-[#1db954] hover:text-[#1ed760] text-sm font-medium">
                    Manage →
                </span>
            </div>
            <h3 class="text-3xl font-bold text-white mb-1">{{ $totalArtists }}</h3>
            <p class="text-zinc-400 text-sm">Artists</p>
        </a>

        <!-- Total Orchestras -->
        <a href="{{ route('admin.orchestras.index') }}" class="bg-zinc-900/50 hover:bg-zinc-800 rounded-lg p-6 transition-colors cursor-pointer block group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center group-hover:bg-blue-500/30 transition-colors">
                    <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                    </svg>
                </div>
                <span class="text-blue-500 hover:text-blue-400 text-sm font-medium">
                    Manage →
                </span>
            </div>
            <h3 class="text-3xl font-bold text-white mb-1">{{ $totalOrchestras }}</h3>
            <p class="text-zinc-400 text-sm">Orchestras</p>
        </a>

        <!-- Total Itoreros -->
        <a href="{{ route('admin.itoreros.index') }}" class="bg-zinc-900/50 hover:bg-zinc-800 rounded-lg p-6 transition-colors cursor-pointer block group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-500/20 rounded-lg flex items-center justify-center group-hover:bg-orange-500/30 transition-colors">
                    <svg class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <span class="text-orange-500 hover:text-orange-400 text-sm font-medium">
                    Manage →
                </span>
            </div>
            <h3 class="text-3xl font-bold text-white mb-1">{{ $totalItoreros }}</h3>
            <p class="text-zinc-400 text-sm">Itoreros</p>
        </a>

        <!-- Total Users -->
        <a href="{{ route('admin.users.index') }}" class="bg-zinc-900/50 hover:bg-zinc-800 rounded-lg p-6 transition-colors cursor-pointer block group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-pink-500/20 rounded-lg flex items-center justify-center group-hover:bg-pink-500/30 transition-colors">
                    <svg class="w-6 h-6 text-pink-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span class="text-pink-500 hover:text-pink-400 text-sm font-medium">
                    Manage →
                </span>
            </div>
            <h3 class="text-3xl font-bold text-white mb-1">{{ $totalUsers }}</h3>
            <p class="text-zinc-400 text-sm">Users</p>
        </a>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-white mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.songs.create') }}" class="bg-zinc-900/50 hover:bg-zinc-800 rounded-lg p-6 transition-colors cursor-pointer block group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#1db954]/20 rounded-lg flex items-center justify-center group-hover:bg-[#1db954]/30 transition-colors">
                        <svg class="w-6 h-6 text-[#1db954]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-1">Upload Song</h3>
                        <p class="text-zinc-400 text-sm">Add a new song to the collection</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.forum.index') }}" class="bg-zinc-900/50 hover:bg-zinc-800 rounded-lg p-6 transition-colors cursor-pointer block group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center group-hover:bg-blue-500/30 transition-colors">
                        <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-1">Forum Management</h3>
                        <p class="text-zinc-400 text-sm">Manage threads and flags</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.playlists.index') }}" class="bg-zinc-900/50 hover:bg-zinc-800 rounded-lg p-6 transition-colors cursor-pointer block group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:bg-purple-500/30 transition-colors">
                        <svg class="w-6 h-6 text-purple-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-1">Manage Playlists</h3>
                        <p class="text-zinc-400 text-sm">Create and edit playlists</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
