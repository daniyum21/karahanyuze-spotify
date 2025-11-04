@extends('layouts.app')

@section('title', 'Admin Dashboard - Karahanyuze')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black">
    <div class="container mx-auto px-4 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Admin Dashboard</h1>
            <p class="text-zinc-400">Welcome back, {{ Auth::user()->FirstName ?? Auth::user()->UserName }}!</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Songs -->
            <div class="bg-zinc-900 rounded-lg p-6 hover:bg-zinc-800 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                    </div>
                    <a href="{{ route('admin.songs.index') }}" class="text-blue-500 hover:text-blue-400 text-sm font-medium">
                        View All →
                    </a>
                </div>
                <h3 class="text-3xl font-bold text-white mb-1">{{ $totalSongs }}</h3>
                <p class="text-zinc-400 text-sm">Total Songs</p>
            </div>

            <!-- Approved Songs -->
            <div class="bg-zinc-900 rounded-lg p-6 hover:bg-zinc-800 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-white mb-1">{{ $approvedSongs }}</h3>
                <p class="text-zinc-400 text-sm">Approved Songs</p>
            </div>

            <!-- Pending Songs -->
            <div class="bg-zinc-900 rounded-lg p-6 hover:bg-zinc-800 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-white mb-1">{{ $pendingSongs }}</h3>
                <p class="text-zinc-400 text-sm">Pending Songs</p>
            </div>

            <!-- Featured Songs -->
            <div class="bg-zinc-900 rounded-lg p-6 hover:bg-zinc-800 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-white mb-1">{{ $featuredSongs }}</h3>
                <p class="text-zinc-400 text-sm">Featured Songs</p>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Artists -->
            <div class="bg-zinc-900 rounded-lg p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">{{ $totalArtists }}</h3>
                        <p class="text-zinc-400 text-sm">Artists</p>
                    </div>
                </div>
            </div>

            <!-- Total Orchestras -->
            <div class="bg-zinc-900 rounded-lg p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">{{ $totalOrchestras }}</h3>
                        <p class="text-zinc-400 text-sm">Orchestras</p>
                    </div>
                </div>
            </div>

            <!-- Total Itoreros -->
            <div class="bg-zinc-900 rounded-lg p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">{{ $totalItoreros }}</h3>
                        <p class="text-zinc-400 text-sm">Itoreros</p>
                    </div>
                </div>
            </div>

            <!-- Total Playlists -->
            <div class="bg-zinc-900 rounded-lg p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">{{ $totalPlaylists }}</h3>
                        <p class="text-zinc-400 text-sm">Playlists</p>
                    </div>
                </div>
            </div>

            <!-- Total Users -->
            <div class="bg-zinc-900 rounded-lg p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-10 h-10 bg-indigo-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">{{ $totalUsers }}</h3>
                        <p class="text-zinc-400 text-sm">Users</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Navigation -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-white mb-4">Admin Navigation</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Songs Management -->
                <div class="bg-zinc-900 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                        Songs
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.songs.create') }}" class="block px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded-lg transition-colors text-sm font-medium">
                            Upload New Song
                        </a>
                        <a href="{{ route('admin.songs.index') }}" class="block px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg transition-colors text-sm font-medium">
                            Manage Songs
                        </a>
                    </div>
                </div>

                <!-- Artists Management -->
                <div class="bg-zinc-900 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Artists
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.artists.index') }}" class="block px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg transition-colors text-sm font-medium">
                            Manage Artists
                        </a>
                        <a href="{{ route('admin.artists.create') }}" class="block px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded-lg transition-colors text-sm font-medium">
                            Add New Artist
                        </a>
                    </div>
                </div>

                <!-- Orchestras Management -->
                <div class="bg-zinc-900 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                        Orchestras
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.orchestras.index') }}" class="block px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg transition-colors text-sm font-medium">
                            Manage Orchestras
                        </a>
                        <a href="{{ route('admin.orchestras.create') }}" class="block px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded-lg transition-colors text-sm font-medium">
                            Add New Orchestra
                        </a>
                    </div>
                </div>

                <!-- Itoreros Management -->
                <div class="bg-zinc-900 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Itoreros
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.itoreros.index') }}" class="block px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg transition-colors text-sm font-medium">
                            Manage Itoreros
                        </a>
                        <a href="{{ route('admin.itoreros.create') }}" class="block px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded-lg transition-colors text-sm font-medium">
                            Add New Itorero
                        </a>
                    </div>
                </div>

                <!-- Playlists Management -->
                <div class="bg-zinc-900 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                        Playlists
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.playlists.index') }}" class="block px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg transition-colors text-sm font-medium">
                            Manage Playlists
                        </a>
                        <a href="{{ route('admin.playlists.create') }}" class="block px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded-lg transition-colors text-sm font-medium">
                            Add New Playlist
                        </a>
                    </div>
                </div>

                <!-- Users Management -->
                <div class="bg-zinc-900 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Users
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg transition-colors text-sm font-medium">
                            Manage Users
                        </a>
                        <a href="{{ route('admin.users.create') }}" class="block px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded-lg transition-colors text-sm font-medium">
                            Add New User
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Songs -->
        @if($recentSongs->count() > 0)
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-white">Recent Songs</h2>
                <a href="{{ route('admin.songs.index') }}" class="text-green-500 hover:text-green-400 text-sm font-medium">
                    View All →
                </a>
            </div>
            <div class="bg-zinc-900 rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-zinc-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Song</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Owner</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800">
                        @foreach($recentSongs as $song)
                        <tr class="hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    @if($song->ProfilePicture)
                                    <img 
                                        src="{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}" 
                                        alt="{{ $song->IndirimboName }}"
                                        class="w-10 h-10 rounded object-cover"
                                    >
                                    @else
                                    <div class="w-10 h-10 rounded bg-zinc-700 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                        </svg>
                                    </div>
                                    @endif
                                    <div>
                                        <div class="text-white font-medium">{{ $song->IndirimboName }}</div>
                                        <div class="text-sm text-zinc-400">{{ $song->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-white">
                                    @if($song->artist)
                                        {{ $song->artist->StageName }}
                                    @elseif($song->orchestra)
                                        {{ $song->orchestra->OrchestreName }}
                                    @elseif($song->itorero)
                                        {{ $song->itorero->ItoreroName }}
                                    @else
                                        <span class="text-zinc-500">No owner</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($song->status)
                                    <span class="px-2 py-1 text-xs rounded-full {{ $song->status->StatusID == 2 ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400' }}">
                                        {{ $song->status->StatusName }}
                                    </span>
                                @else
                                    <span class="text-zinc-500 text-sm">Unknown</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a 
                                    href="{{ route('admin.songs.edit', $song->UUID) }}" 
                                    class="text-green-400 hover:text-green-300 transition-colors"
                                >
                                    Edit
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

