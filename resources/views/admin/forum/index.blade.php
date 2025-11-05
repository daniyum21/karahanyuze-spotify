@extends('layouts.app')

@section('title', 'Forum Management - Admin - Karahanyuze')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black pb-24">
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Forum Management</h1>
            <p class="text-zinc-400">Manage forum threads, comments, and flags</p>
        </div>

        <!-- Tabs -->
        <div class="flex gap-4 mb-6 border-b border-zinc-700">
            <a href="{{ route('admin.forum.index') }}" class="px-6 py-3 {{ request()->routeIs('admin.forum.index') ? 'border-b-2 border-green-500 text-green-500' : 'text-zinc-400 hover:text-white' }} font-semibold transition-colors">
                Threads
            </a>
            <a href="{{ route('admin.forum.flags.index') }}" class="px-6 py-3 {{ request()->routeIs('admin.forum.flags.*') ? 'border-b-2 border-green-500 text-green-500' : 'text-zinc-400 hover:text-white' }} font-semibold transition-colors">
                Flags ({{ $unresolvedFlags->total() }})
            </a>
        </div>

        <!-- Pending Threads Section -->
        @if($pendingThreads->count() > 0)
        <div class="bg-yellow-500/10 border border-yellow-500 rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-3">
                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Pending Threads ({{ $pendingThreads->count() }})
            </h2>
            <div class="space-y-4">
                @foreach($pendingThreads as $thread)
                <div class="bg-zinc-800 rounded-lg p-6">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-white mb-2">{{ $thread->title }}</h3>
                            @if($thread->body && trim($thread->body) !== '')
                            <p class="text-zinc-300 mb-4">{{ \Illuminate\Support\Str::limit(strip_tags($thread->body), 200) }}</p>
                            @endif
                            <div class="flex items-center gap-4 text-sm text-zinc-400">
                                <span>By: {{ $thread->user->PublicName ?? $thread->user->FirstName . ' ' . $thread->user->LastName }}</span>
                                <span>Submitted: {{ $thread->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <form action="{{ route('admin.forum.threads.approve', $thread->ThreadID) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-colors">
                                Approve
                            </button>
                        </form>
                        <button onclick="showRejectWarning({{ $thread->ThreadID }}, '{{ addslashes($thread->title) }}')" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition-colors">
                            Reject
                        </button>
                        <a href="{{ route('forum.show', $thread->slug) }}" target="_blank" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-colors">
                            View
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Threads Table -->
        <div class="bg-zinc-800 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-900">
                        <tr>
                            <th class="px-6 py-4 text-left text-white font-semibold">Title</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Author</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Comments</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Views</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Status</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Created</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($threads as $thread)
                        <tr class="border-t border-zinc-700 hover:bg-zinc-700/50 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('forum.show', $thread->slug) }}" target="_blank" class="text-white hover:text-green-500 transition-colors font-medium">
                                    {{ $thread->title }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-zinc-300">
                                {{ $thread->user->PublicName ?? $thread->user->FirstName . ' ' . $thread->user->LastName }}
                            </td>
                            <td class="px-6 py-4 text-zinc-300">{{ $thread->all_comments_count ?? 0 }}</td>
                            <td class="px-6 py-4 text-zinc-300">{{ $thread->view_count }}</td>
                            <td class="px-6 py-4">
                                @if($thread->is_pinned)
                                <span class="px-2 py-1 bg-green-500 text-white text-xs font-semibold rounded">Pinned</span>
                                @endif
                                @if($thread->is_locked)
                                <span class="px-2 py-1 bg-red-500 text-white text-xs font-semibold rounded">Locked</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-zinc-300">{{ $thread->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('admin.forum.threads.toggle-pin', $thread->ThreadID) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded transition-colors">
                                            {{ $thread->is_pinned ? 'Unpin' : 'Pin' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.forum.threads.toggle-lock', $thread->ThreadID) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded transition-colors">
                                            {{ $thread->is_locked ? 'Unlock' : 'Lock' }}
                                        </button>
                                    </form>
                                    <button onclick="showDeleteWarning({{ $thread->ThreadID }}, '{{ addslashes($thread->title) }}')" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded transition-colors">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-zinc-400">No threads found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($threads->hasPages())
        <div class="mt-6">
            {{ $threads->links() }}
        </div>
        @endif

        <!-- Unresolved Flags -->
        @if($unresolvedFlags->count() > 0)
        <div class="mt-8">
            <h2 class="text-2xl font-bold text-white mb-4">Unresolved Flags</h2>
            <div class="bg-zinc-800 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-900">
                            <tr>
                                <th class="px-6 py-4 text-left text-white font-semibold">Type</th>
                                <th class="px-6 py-4 text-left text-white font-semibold">Flagged By</th>
                                <th class="px-6 py-4 text-left text-white font-semibold">Reason</th>
                                <th class="px-6 py-4 text-left text-white font-semibold">Date</th>
                                <th class="px-6 py-4 text-left text-white font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unresolvedFlags as $flag)
                            <tr class="border-t border-zinc-700 hover:bg-zinc-700/50 transition-colors">
                                <td class="px-6 py-4 text-zinc-300 capitalize">{{ $flag->flaggable_type }}</td>
                                <td class="px-6 py-4 text-zinc-300">
                                    {{ $flag->user->PublicName ?? $flag->user->FirstName . ' ' . $flag->user->LastName }}
                                </td>
                                <td class="px-6 py-4 text-zinc-300">{{ $flag->reason ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-zinc-300">{{ $flag->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('admin.forum.flags.resolve', $flag->FlagID) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded transition-colors">
                                            Resolve
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Reject Thread Warning Modal -->
<div id="rejectWarningModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-zinc-800 rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-start gap-4 mb-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-bold text-white mb-2">Reject Thread</h3>
                <p class="text-zinc-300 mb-4">Are you sure you want to reject this thread? It will be permanently deleted.</p>
                <div class="bg-red-500/10 border border-red-500/50 rounded-lg p-3 mb-4">
                    <p class="text-red-300 text-sm font-medium mb-1">Thread Title:</p>
                    <p class="text-white text-sm" id="rejectThreadTitle"></p>
                </div>
                <div class="bg-yellow-500/10 border border-yellow-500/50 rounded-lg p-3 mb-4">
                    <p class="text-yellow-300 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        This action cannot be undone.
                    </p>
                </div>
                <form id="rejectThreadForm" method="POST" class="inline">
                    @csrf
                    <div class="flex items-center gap-3">
                        <button type="submit" class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition-colors flex-1">
                            Reject & Delete
                        </button>
                        <button type="button" onclick="closeRejectWarning()" class="px-6 py-3 bg-zinc-700 hover:bg-zinc-600 text-white font-semibold rounded-lg transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Thread Warning Modal -->
<div id="deleteWarningModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
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
                <h3 class="text-xl font-bold text-white mb-2">Delete Thread</h3>
                <p class="text-zinc-300 mb-4">Are you sure you want to delete this thread? This action cannot be undone.</p>
                <div class="bg-red-500/10 border border-red-500/50 rounded-lg p-3 mb-4">
                    <p class="text-red-300 text-sm font-medium mb-1">Thread Title:</p>
                    <p class="text-white text-sm" id="deleteThreadTitle"></p>
                </div>
                <div class="bg-yellow-500/10 border border-yellow-500/50 rounded-lg p-3 mb-4">
                    <p class="text-yellow-300 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        All comments and associated data will also be deleted.
                    </p>
                </div>
                <form id="deleteThreadForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-center gap-3">
                        <button type="submit" class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition-colors flex-1">
                            Delete Thread
                        </button>
                        <button type="button" onclick="closeDeleteWarning()" class="px-6 py-3 bg-zinc-700 hover:bg-zinc-600 text-white font-semibold rounded-lg transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showRejectWarning(threadId, threadTitle) {
    document.getElementById('rejectThreadTitle').textContent = threadTitle;
    document.getElementById('rejectThreadForm').action = '{{ url("/admin/forum/threads") }}/' + threadId + '/reject';
    document.getElementById('rejectWarningModal').classList.remove('hidden');
    document.getElementById('rejectWarningModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeRejectWarning() {
    document.getElementById('rejectWarningModal').classList.add('hidden');
    document.getElementById('rejectWarningModal').classList.remove('flex');
    document.body.style.overflow = '';
}

function showDeleteWarning(threadId, threadTitle) {
    document.getElementById('deleteThreadTitle').textContent = threadTitle;
    document.getElementById('deleteThreadForm').action = '{{ url("/admin/forum/threads") }}/' + threadId;
    document.getElementById('deleteWarningModal').classList.remove('hidden');
    document.getElementById('deleteWarningModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeDeleteWarning() {
    document.getElementById('deleteWarningModal').classList.add('hidden');
    document.getElementById('deleteWarningModal').classList.remove('flex');
    document.body.style.overflow = '';
}

// Close modals when clicking outside
document.getElementById('rejectWarningModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectWarning();
    }
});

document.getElementById('deleteWarningModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteWarning();
    }
});
</script>
@endsection

