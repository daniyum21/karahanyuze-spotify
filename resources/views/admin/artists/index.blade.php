@extends('layouts.spotify')

@section('title', 'Artists - Admin')

@section('content')
@php
    $sortBy = $sortBy ?? 'created_at';
    $sortDirection = $sortDirection ?? 'desc';
@endphp
<div class="px-6 py-8 pb-24">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Artists</h1>
            <p class="text-zinc-400">Manage all artists in the Karahanyuze collection</p>
        </div>
        <a 
            href="{{ route('admin.artists.create') }}" 
            class="px-6 py-3 bg-[#1db954] hover:bg-[#1ed760] text-black font-bold rounded-full transition-colors flex items-center gap-2"
        >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
            </svg>
            Add New Artist
        </a>
    </div>

    @if(session('success'))
    <div class="bg-[#1db954]/20 border border-[#1db954] text-[#1db954] px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if($artists->count() > 0)
    <div class="bg-zinc-900/50 rounded-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-zinc-800/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Artist</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">
                        <a href="{{ route('admin.artists.index', ['sort' => 'StageName', 'direction' => $sortBy === 'StageName' && $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-2 hover:text-white transition-colors">
                            Stage Name
                            @if($sortBy === 'StageName')
                                @if($sortDirection === 'asc')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M5 15l7-7 7 7z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7z"/>
                                    </svg>
                                @endif
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Songs</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Featured</th>
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
                            />
                            @else
                            <div class="w-12 h-12 bg-zinc-700 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-zinc-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                            @endif
                            <div>
                                <a href="{{ route('artists.show', $artist->slug) }}" class="text-white font-medium hover:text-[#1db954] hover:underline">{{ $artist->StageName }}</a>
                                @if($artist->FirstName || $artist->LastName)
                                <p class="text-zinc-400 text-sm">{{ $artist->FirstName }} {{ $artist->LastName }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-zinc-300">{{ $artist->StageName }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-zinc-300">{{ $artist->songs->count() }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($artist->IsFeatured)
                            <span class="px-2 py-1 bg-purple-500/20 text-purple-500 rounded-full text-xs font-medium">Featured</span>
                        @else
                            <span class="px-2 py-1 bg-zinc-500/20 text-zinc-400 rounded-full text-xs font-medium">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.artists.edit', $artist->UUID) }}" class="text-[#1db954] hover:text-[#1ed760] transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                </svg>
                            </a>
                            <button 
                                onclick="showDeleteArtistWarning('{{ $artist->UUID }}', '{{ addslashes($artist->StageName) }}')"
                                class="text-red-500 hover:text-red-400 transition-colors"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $artists->links() }}
    </div>
    @else
    <div class="text-center py-12 bg-zinc-900/50 rounded-lg">
        <p class="text-zinc-400 text-lg">No artists found.</p>
    </div>
    @endif
</div>

<!-- Delete Warning Modal -->
<div id="deleteArtistWarningModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center" style="display: none;">
    <div class="bg-zinc-900 rounded-lg p-6 max-w-md w-full mx-4 border border-zinc-800">
        <h3 class="text-xl font-bold text-white mb-4">Delete Artist</h3>
        <p class="text-zinc-300 mb-6" id="deleteArtistWarningMessage">Are you sure you want to delete this artist? This action cannot be undone.</p>
        <form id="deleteArtistForm" method="POST" class="flex gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteArtistWarning()" class="flex-1 px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg transition-colors">
                Cancel
            </button>
            <button type="submit" class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                Delete
            </button>
        </form>
    </div>
</div>

<script>
function showDeleteArtistWarning(uuid, artistName) {
    const modal = document.getElementById('deleteArtistWarningModal');
    const form = document.getElementById('deleteArtistForm');
    const message = document.getElementById('deleteArtistWarningMessage');
    
    if (modal && form && message) {
        message.textContent = `Are you sure you want to delete "${artistName}"? This action cannot be undone.`;
        form.action = `/admin/abahanzi/${uuid}`;
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
    }
}

function closeDeleteArtistWarning() {
    const modal = document.getElementById('deleteArtistWarningModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
}

document.addEventListener('click', function(event) {
    const modal = document.getElementById('deleteArtistWarningModal');
    if (modal && event.target === modal) {
        closeDeleteArtistWarning();
    }
});
</script>
@endsection
