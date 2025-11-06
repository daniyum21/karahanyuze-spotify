@extends('layouts.spotify')

@section('title', 'Itoreros - Admin')

@section('content')
<div class="px-6 py-8 pb-24">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Itoreros</h1>
            <p class="text-zinc-400">Manage all itoreros in the Karahanyuze collection</p>
        </div>
        <a 
            href="{{ route('admin.itoreros.create') }}" 
            class="px-6 py-3 bg-[#1db954] hover:bg-[#1ed760] text-black font-bold rounded-full transition-colors flex items-center gap-2"
        >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
            </svg>
            Add New Itorero
        </a>
    </div>

    @if(session('success'))
    <div class="bg-[#1db954]/20 border border-[#1db954] text-[#1db954] px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if($itoreros->count() > 0)
    <div class="bg-zinc-900/50 rounded-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-zinc-800/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Itorero</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Songs</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @foreach($itoreros as $itorero)
                <tr class="hover:bg-zinc-800/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-4">
                            @if($itorero->ProfilePicture)
                            <img 
                                src="{{ \App\Helpers\ImageHelper::getImageUrl($itorero->ProfilePicture) }}" 
                                alt="{{ $itorero->ItoreroName }}"
                                class="w-12 h-12 rounded-lg object-cover"
                            />
                            @else
                            <div class="w-12 h-12 bg-zinc-700 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-zinc-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            @endif
                            <div>
                                <a href="{{ route('itorero.show', $itorero->slug) }}" class="text-white font-medium hover:text-[#1db954] hover:underline">{{ $itorero->ItoreroName }}</a>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-zinc-300">{{ $itorero->ItoreroName }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-zinc-300">{{ $itorero->songs->count() }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.itoreros.edit', $itorero->UUID) }}" class="text-[#1db954] hover:text-[#1ed760] transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                </svg>
                            </a>
                            <button 
                                onclick="showDeleteItoreroWarning('{{ $itorero->UUID }}', '{{ addslashes($itorero->ItoreroName) }}')"
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
        {{ $itoreros->links() }}
    </div>
    @else
    <div class="text-center py-12 bg-zinc-900/50 rounded-lg">
        <p class="text-zinc-400 text-lg">No itoreros found.</p>
    </div>
    @endif
</div>

<!-- Delete Warning Modal -->
<div id="deleteItoreroWarningModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center" style="display: none;">
    <div class="bg-zinc-900 rounded-lg p-6 max-w-md w-full mx-4 border border-zinc-800">
        <h3 class="text-xl font-bold text-white mb-4">Delete Itorero</h3>
        <p class="text-zinc-300 mb-6" id="deleteItoreroWarningMessage">Are you sure you want to delete this itorero? This action cannot be undone.</p>
        <form id="deleteItoreroForm" method="POST" class="flex gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteItoreroWarning()" class="flex-1 px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg transition-colors">
                Cancel
            </button>
            <button type="submit" class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                Delete
            </button>
        </form>
    </div>
</div>

<script>
function showDeleteItoreroWarning(uuid, itoreroName) {
    const modal = document.getElementById('deleteItoreroWarningModal');
    const form = document.getElementById('deleteItoreroForm');
    const message = document.getElementById('deleteItoreroWarningMessage');
    
    if (modal && form && message) {
        message.textContent = `Are you sure you want to delete "${itoreroName}"? This action cannot be undone.`;
        form.action = `/admin/amatorero/${uuid}`;
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
    }
}

function closeDeleteItoreroWarning() {
    const modal = document.getElementById('deleteItoreroWarningModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
}

document.addEventListener('click', function(event) {
    const modal = document.getElementById('deleteItoreroWarningModal');
    if (modal && event.target === modal) {
        closeDeleteItoreroWarning();
    }
});
</script>
@endsection
