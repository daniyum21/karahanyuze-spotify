@extends('layouts.app')

@section('title', 'Forum Flags - Admin - Karahanyuze')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black pb-24">
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Forum Flags</h1>
            <p class="text-zinc-400">Review and manage flagged content</p>
        </div>

        <!-- Tabs -->
        <div class="flex gap-4 mb-6 border-b border-zinc-700">
            <a href="{{ route('admin.forum.index') }}" class="px-6 py-3 {{ request()->routeIs('admin.forum.index') ? 'border-b-2 border-green-500 text-green-500' : 'text-zinc-400 hover:text-white' }} font-semibold transition-colors">
                Threads
            </a>
            <a href="{{ route('admin.forum.flags.index') }}" class="px-6 py-3 {{ request()->routeIs('admin.forum.flags.*') ? 'border-b-2 border-green-500 text-green-500' : 'text-zinc-400 hover:text-white' }} font-semibold transition-colors">
                Flags
            </a>
        </div>

        <!-- Flags Table -->
        <div class="bg-zinc-800 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-900">
                        <tr>
                            <th class="px-6 py-4 text-left text-white font-semibold">Type</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Content</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Flagged By</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Reason</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Status</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Date</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($flags as $flag)
                        <tr class="border-t border-zinc-700 hover:bg-zinc-700/50 transition-colors">
                            <td class="px-6 py-4 text-zinc-300 capitalize">{{ $flag->flaggable_type }}</td>
                            <td class="px-6 py-4">
                                @if($flag->flaggable_type === 'thread')
                                    <a href="{{ route('forum.show', $flag->flaggable->slug) }}" target="_blank" class="text-white hover:text-green-500 transition-colors">
                                        {{ \Illuminate\Support\Str::limit($flag->flaggable->title, 50) }}
                                    </a>
                                @else
                                    <span class="text-zinc-300">{{ \Illuminate\Support\Str::limit(strip_tags($flag->flaggable->body ?? ''), 50) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-zinc-300">
                                {{ $flag->user->PublicName ?? $flag->user->FirstName . ' ' . $flag->user->LastName }}
                            </td>
                            <td class="px-6 py-4 text-zinc-300">{{ $flag->reason ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                @if($flag->is_resolved)
                                <span class="px-2 py-1 bg-green-500 text-white text-xs font-semibold rounded">Resolved</span>
                                @else
                                <span class="px-2 py-1 bg-red-500 text-white text-xs font-semibold rounded">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-zinc-300">{{ $flag->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                @if(!$flag->is_resolved)
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('admin.forum.flags.resolve', $flag->FlagID) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded transition-colors">
                                            Resolve Flag
                                        </button>
                                    </form>
                                    @if($flag->flaggable_type === 'comment')
                                    <form action="{{ route('admin.forum.comments.approve', $flag->flaggable_id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded transition-colors">
                                            Approve Comment
                                        </button>
                                    </form>
                                    <button onclick="showDeleteCommentWarning({{ $flag->flaggable_id }})" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded transition-colors">
                                        Delete Comment
                                    </button>
                                    @endif
                                </div>
                                @else
                                <span class="text-zinc-500 text-xs">Resolved by {{ $flag->resolvedBy->PublicName ?? $flag->resolvedBy->FirstName }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-zinc-400">No flags found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($flags->hasPages())
        <div class="mt-6">
            {{ $flags->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Delete Comment Warning Modal -->
<div id="deleteCommentWarningModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
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
                <h3 class="text-xl font-bold text-white mb-2">Delete Comment</h3>
                <p class="text-zinc-300 mb-4">Are you sure you want to delete this comment? It will be removed permanently.</p>
                <div class="bg-yellow-500/10 border border-yellow-500/50 rounded-lg p-3 mb-4">
                    <p class="text-yellow-300 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        This action cannot be undone. All replies to this comment will also be deleted.
                    </p>
                </div>
                <form id="deleteCommentForm" method="POST" class="inline">
                    @csrf
                    <div class="flex items-center gap-3">
                        <button type="submit" class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition-colors flex-1">
                            Delete Comment
                        </button>
                        <button type="button" onclick="closeDeleteCommentWarning()" class="px-6 py-3 bg-zinc-700 hover:bg-zinc-600 text-white font-semibold rounded-lg transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showDeleteCommentWarning(commentId) {
    document.getElementById('deleteCommentForm').action = '{{ url("/admin/forum/comments") }}/' + commentId + '/reject';
    document.getElementById('deleteCommentWarningModal').classList.remove('hidden');
    document.getElementById('deleteCommentWarningModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeDeleteCommentWarning() {
    document.getElementById('deleteCommentWarningModal').classList.add('hidden');
    document.getElementById('deleteCommentWarningModal').classList.remove('flex');
    document.body.style.overflow = '';
}

// Close modal when clicking outside
document.getElementById('deleteCommentWarningModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteCommentWarning();
    }
});
</script>
@endsection

