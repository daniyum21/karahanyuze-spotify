@extends('layouts.spotify')

@section('title', 'Songs - Admin')

@section('content')
<div class="px-6 py-8 pb-24">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Songs</h1>
            <p class="text-zinc-400">
                @if($filterStatus === 'pending')
                    Pending Songs - Review and approve
                @elseif($filterStatus === 'approved')
                    Approved Songs - Manage approved songs
                @elseif($filterFeatured === 'yes')
                    Featured Songs - Add or remove featured status
                @else
                    Manage all songs in the Karahanyuze collection
                @endif
            </p>
        </div>
        <a 
            href="{{ route('admin.songs.create') }}" 
            class="px-6 py-3 bg-[#1db954] hover:bg-[#1ed760] text-black font-bold rounded-full transition-colors flex items-center gap-2"
        >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
            </svg>
            Upload New Song
        </a>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-6 flex gap-3 border-b border-zinc-800">
        <a href="{{ route('admin.songs.index') }}" class="px-4 py-2 {{ $filterStatus === 'all' && $filterFeatured === 'all' ? 'border-b-2 border-[#1db954] text-white' : 'text-zinc-400 hover:text-white' }} font-medium transition-colors">
            All Songs
        </a>
        <a href="{{ route('admin.songs.index', ['status' => 'pending']) }}" class="px-4 py-2 {{ $filterStatus === 'pending' ? 'border-b-2 border-yellow-500 text-yellow-500' : 'text-zinc-400 hover:text-white' }} font-medium transition-colors">
            Pending
        </a>
        <a href="{{ route('admin.songs.index', ['status' => 'approved']) }}" class="px-4 py-2 {{ $filterStatus === 'approved' ? 'border-b-2 border-[#1db954] text-[#1db954]' : 'text-zinc-400 hover:text-white' }} font-medium transition-colors">
            Approved
        </a>
        <a href="{{ route('admin.songs.index', ['featured' => 'yes']) }}" class="px-4 py-2 {{ $filterFeatured === 'yes' ? 'border-b-2 border-purple-500 text-purple-500' : 'text-zinc-400 hover:text-white' }} font-medium transition-colors">
            Featured
        </a>
    </div>

    @if(session('success'))
    <div class="bg-[#1db954]/20 border border-[#1db954] text-[#1db954] px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if($songs->count() > 0)
    <div class="bg-zinc-900/50 rounded-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-zinc-800/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Song</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Owner</th>
                    @if($filterStatus === 'pending')
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Submitted By</th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Featured</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @foreach($songs as $index => $song)
                <tr 
                    id="song-item-{{ $song->IndirimboID }}"
                    class="hover:bg-zinc-800/50 transition-colors cursor-pointer group"
                >
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-4">
                            @if($song->ProfilePicture)
                            <img 
                                src="{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}" 
                                alt="{{ $song->IndirimboName }}"
                                class="w-12 h-12 rounded object-cover"
                            />
                            @else
                            <div class="w-12 h-12 bg-zinc-700 rounded flex items-center justify-center">
                                <svg class="w-6 h-6 text-zinc-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                </svg>
                            </div>
                            @endif
                            <div>
                                <a href="{{ route('indirimbo.show', [$song->slug, $song->UUID]) }}" class="text-white font-medium hover:underline">{{ $song->IndirimboName }}</a>
                                <p class="text-zinc-400 text-sm">
                                    @if($song->artist)
                                        {{ $song->artist->StageName }}
                                    @elseif($song->orchestra)
                                        {{ $song->orchestra->OrchestreName }}
                                    @elseif($song->itorero)
                                        {{ $song->itorero->ItoreroName }}
                                    @else
                                        Unknown
                                    @endif
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-zinc-300">
                        @if($song->artist)
                            <a href="{{ route('artists.show', $song->artist->slug) }}" class="hover:text-[#1db954] hover:underline">{{ $song->artist->StageName }}</a>
                        @elseif($song->orchestra)
                            <a href="{{ route('orchestre.show', $song->orchestra->slug) }}" class="hover:text-[#1db954] hover:underline">{{ $song->orchestra->OrchestreName }}</a>
                        @elseif($song->itorero)
                            <a href="{{ route('itorero.show', $song->itorero->slug) }}" class="hover:text-[#1db954] hover:underline">{{ $song->itorero->ItoreroName }}</a>
                        @else
                            <span class="text-zinc-500">Unknown</span>
                        @endif
                    </td>
                    @if($filterStatus === 'pending')
                    <td class="px-6 py-4 whitespace-nowrap text-zinc-300">
                        @if($song->user)
                            {{ $song->user->FirstName ?? $song->user->UserName }}
                        @else
                            <span class="text-zinc-500">Unknown</span>
                        @endif
                    </td>
                    @endif
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($song->status)
                            @if($song->status->StatusName === 'Approved' || $song->status->StatusName === 'approved')
                                <span class="px-2 py-1 bg-[#1db954]/20 text-[#1db954] rounded-full text-xs font-medium">Approved</span>
                            @elseif($song->status->StatusName === 'Pending' || $song->status->StatusName === 'pending')
                                <span class="px-2 py-1 bg-yellow-500/20 text-yellow-500 rounded-full text-xs font-medium">Pending</span>
                            @else
                                <span class="px-2 py-1 bg-zinc-500/20 text-zinc-400 rounded-full text-xs font-medium">{{ $song->status->StatusName }}</span>
                            @endif
                        @else
                            <span class="px-2 py-1 bg-zinc-500/20 text-zinc-400 rounded-full text-xs font-medium">Unknown</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($song->IsFeatured)
                            <span class="px-2 py-1 bg-purple-500/20 text-purple-500 rounded-full text-xs font-medium">Featured</span>
                        @else
                            <span class="px-2 py-1 bg-zinc-500/20 text-zinc-400 rounded-full text-xs font-medium">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.songs.edit', $song->UUID) }}" class="text-[#1db954] hover:text-[#1ed760] transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                </svg>
                            </a>
                            <button 
                                onclick="showDeleteSongWarning('{{ $song->UUID }}', '{{ addslashes($song->IndirimboName) }}')"
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
        {{ $songs->links() }}
    </div>
    @else
    <div class="text-center py-12 bg-zinc-900/50 rounded-lg">
        <p class="text-zinc-400 text-lg">No songs found.</p>
    </div>
    @endif
</div>

<!-- Delete Warning Modal -->
<div id="deleteSongWarningModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center" style="display: none;">
    <div class="bg-zinc-900 rounded-lg p-6 max-w-md w-full mx-4 border border-zinc-800">
        <h3 class="text-xl font-bold text-white mb-4">Delete Song</h3>
        <p class="text-zinc-300 mb-6" id="deleteSongWarningMessage">Are you sure you want to delete this song? This action cannot be undone.</p>
        <form id="deleteSongForm" method="POST" class="flex gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteSongWarning()" class="flex-1 px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg transition-colors">
                Cancel
            </button>
            <button type="submit" class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                Delete
            </button>
        </form>
    </div>
</div>

<script>
function showDeleteSongWarning(uuid, songName) {
    const modal = document.getElementById('deleteSongWarningModal');
    const form = document.getElementById('deleteSongForm');
    const message = document.getElementById('deleteSongWarningMessage');
    
    if (modal && form && message) {
        message.textContent = `Are you sure you want to delete "${songName}"? This action cannot be undone.`;
        form.action = `/admin/songs/${uuid}`;
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
    }
}

function closeDeleteSongWarning() {
    const modal = document.getElementById('deleteSongWarningModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('deleteSongWarningModal');
    if (modal && event.target === modal) {
        closeDeleteSongWarning();
    }
});
</script>
@endsection
