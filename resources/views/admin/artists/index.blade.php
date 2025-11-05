@extends('layouts.app')

@section('title', 'Artists - Admin')

@section('content')
@php
    $sortBy = $sortBy ?? 'created_at';
    $sortDirection = $sortDirection ?? 'desc';
@endphp
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black">
    <div class="container mx-auto px-4 py-12">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">Artists</h1>
                <p class="text-zinc-400">Manage all artists in the Karahanyuze collection</p>
            </div>
            <a 
                href="{{ route('admin.artists.create') }}" 
                class="bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Artist
            </a>
        </div>

        @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
        @endif

        @if($artists->count() > 0)
        <div class="bg-zinc-900 rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Artist</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                            <a href="{{ route('admin.artists.index', ['sort' => 'StageName', 'direction' => $sortBy === 'StageName' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-2 hover:text-white transition-colors">
                                Stage Name
                                @if($sortBy === 'StageName')
                                    @if($sortDirection === 'asc')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @endif
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                            <a href="{{ route('admin.artists.index', ['sort' => 'Email', 'direction' => $sortBy === 'Email' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-2 hover:text-white transition-colors">
                                Email
                                @if($sortBy === 'Email')
                                    @if($sortDirection === 'asc')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @endif
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                            <a href="{{ route('admin.artists.index', ['sort' => 'songs_count', 'direction' => $sortBy === 'songs_count' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-2 hover:text-white transition-colors">
                                Songs
                                @if($sortBy === 'songs_count')
                                    @if($sortDirection === 'asc')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @endif
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                            <a href="{{ route('admin.artists.index', ['sort' => 'IsFeatured', 'direction' => $sortBy === 'IsFeatured' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-2 hover:text-white transition-colors">
                                Featured
                                @if($sortBy === 'IsFeatured')
                                    @if($sortDirection === 'asc')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @endif
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                            <a href="{{ route('admin.artists.index', ['sort' => 'created_at', 'direction' => $sortBy === 'created_at' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-2 hover:text-white transition-colors">
                                Created
                                @if($sortBy === 'created_at')
                                    @if($sortDirection === 'asc')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @endif
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @foreach($artists as $artist)
                    <tr class="hover:bg-zinc-800/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-4">
                                @if($artist->ProfilePicture)
                                <img 
                                    src="{{ \App\Helpers\ImageHelper::getImageUrl($artist->ProfilePicture) }}" 
                                    alt="{{ $artist->StageName }}"
                                    class="w-12 h-12 rounded-full object-cover"
                                >
                                @else
                                <div class="w-12 h-12 rounded-full bg-zinc-700 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                @endif
                                <div>
                                    <div class="text-white font-medium">{{ $artist->FirstName }} {{ $artist->LastName }}</div>
                                    <div class="text-sm text-zinc-400">{{ $artist->created_at ? $artist->created_at->format('M d, Y') : 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-white font-medium">{{ $artist->StageName }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-white">{{ $artist->Email ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-white">{{ $artist->songs_count ?? 0 }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($artist->IsFeatured)
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-500/20 text-blue-400">Featured</span>
                            @else
                                <span class="text-zinc-500 text-sm">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-3">
                                <a 
                                    href="{{ route('admin.artists.edit', $artist->UUID) }}" 
                                    class="text-green-400 hover:text-green-300 transition-colors"
                                >
                                    Edit
                                </a>
                                <button 
                                    type="button"
                                    onclick="showDeleteArtistWarning('{{ $artist->UUID }}', '{{ addslashes($artist->StageName) }}');"
                                    class="text-red-400 hover:text-red-300 transition-colors"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $artists->links() }}
        </div>
        @else
        <div class="text-center py-12 bg-zinc-900 rounded-lg">
            <p class="text-zinc-400 text-lg mb-4">No artists found.</p>
            <a 
                href="{{ route('admin.artists.create') }}" 
                class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
            >
                Add Your First Artist
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Delete Artist Warning Modal -->
<div id="deleteArtistWarningModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-zinc-800 rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-start gap-4 mb-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-bold text-white mb-2">Delete Artist</h3>
                <p class="text-zinc-300 mb-4">Are you sure you want to delete this artist? This action cannot be undone.</p>
                <div class="bg-red-500/10 border border-red-500/50 rounded-lg p-3 mb-4">
                    <p class="text-red-300 text-sm font-medium mb-1">Artist Name:</p>
                    <p class="text-white text-sm" id="deleteArtistName"></p>
                </div>
                <div class="bg-yellow-500/10 border border-yellow-500/50 rounded-lg p-3 mb-4">
                    <p class="text-yellow-300 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        All associated songs and data will be permanently deleted.
                    </p>
                </div>
                <form id="deleteArtistForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-center gap-3">
                        <button type="submit" class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition-colors flex-1">
                            Delete Artist
                        </button>
                        <button type="button" onclick="closeDeleteArtistWarning()" class="px-6 py-3 bg-zinc-700 hover:bg-zinc-600 text-white font-semibold rounded-lg transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showDeleteArtistWarning(uuid, artistName) {
    document.getElementById('deleteArtistName').textContent = artistName;
    document.getElementById('deleteArtistForm').action = '{{ url("/admin/abahanzi") }}/' + uuid;
    document.getElementById('deleteArtistWarningModal').classList.remove('hidden');
    document.getElementById('deleteArtistWarningModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeDeleteArtistWarning() {
    document.getElementById('deleteArtistWarningModal').classList.add('hidden');
    document.getElementById('deleteArtistWarningModal').classList.remove('flex');
    document.body.style.overflow = '';
}

document.getElementById('deleteArtistWarningModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteArtistWarning();
    }
});
</script>
@endsection

