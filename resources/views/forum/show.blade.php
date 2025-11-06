@extends('layouts.spotify')

@section('title', $thread->title . ' - Forum - Karahanyuze')

@section('content')
<div class="px-6 py-8 pb-24">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('forum.index') }}" class="inline-flex items-center gap-2 text-zinc-400 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M15 19l-7-7 7-7"/>
            </svg>
            <span>Back to Forum</span>
        </a>
    </div>

    <!-- Thread Header -->
    <div class="bg-zinc-900/50 rounded-lg p-6 mb-6">
        <div class="flex items-center gap-3 mb-4">
            @if($thread->is_pinned)
            <span class="px-2 py-1 bg-[#1db954] text-black text-xs font-bold rounded">PINNED</span>
            @endif
            @if($thread->is_locked)
            <span class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded">LOCKED</span>
            @endif
        </div>
        <h1 class="text-4xl font-bold text-white mb-4">{{ $thread->title }}</h1>
        <div class="flex items-center gap-6 text-sm text-zinc-400 mb-4">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                <span>{{ $thread->user->PublicName ?? $thread->user->FirstName . ' ' . $thread->user->LastName }}</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z"/>
                </svg>
                <span>{{ $thread->created_at->format('M d, Y') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                </svg>
                <span>{{ $thread->view_count }} views</span>
            </div>
        </div>
        @if($thread->body && trim($thread->body) !== '')
        <div class="text-zinc-300 whitespace-pre-wrap mb-4">{{ $thread->body }}</div>
        @endif
        <div class="flex items-center gap-4">
            @auth
            @if($thread->UserID === Auth::id() || Auth::user()->isAdmin())
            <a href="{{ route('forum.edit', $thread->slug) }}" class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold rounded-lg transition-colors text-sm">
                Edit
            </a>
            <button 
                onclick="showDeleteThreadWarning('{{ $thread->slug }}', '{{ addslashes($thread->title) }}')"
                class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition-colors text-sm"
            >
                Delete
            </button>
            @endif
            <button onclick="openFlagModal('thread', {{ $thread->ThreadID }})" class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold rounded-lg transition-colors text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                Flag
            </button>
            @endauth
        </div>
    </div>

    <!-- Comments Section -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white mb-4">
            {{ $thread->comment_count ?? 0 }} {{ ($thread->comment_count ?? 0) == 1 ? 'Comment' : 'Comments' }}
        </h2>

        @if(!$thread->is_locked)
        @auth
        <!-- Comment Form -->
        <div class="bg-zinc-900/50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-white mb-4">Post a Comment</h3>
            <form action="{{ route('forum.comments.store', $thread->slug) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <textarea
                        name="body"
                        rows="4"
                        required
                        maxlength="5000"
                        placeholder="Write your comment..."
                        class="w-full px-4 py-3 bg-black border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white"
                    >{{ old('body') }}</textarea>
                    @error('body')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="px-6 py-2 bg-white hover:bg-zinc-200 text-black font-bold rounded-full transition-colors">
                    Post Comment
                </button>
            </form>
        </div>
        @else
        <div class="bg-zinc-900/50 rounded-lg p-4 mb-6">
            <p class="text-zinc-400 text-sm">
                <a href="{{ route('login') }}" class="text-white hover:underline font-medium">Log in</a> to post a comment.
            </p>
        </div>
        @endauth
        @else
        <div class="bg-zinc-900/50 rounded-lg p-4 mb-6">
            <p class="text-zinc-400 text-sm">This thread is locked. No new comments can be posted.</p>
        </div>
        @endif

        <!-- Comments List -->
        @if($comments->count() > 0)
        <div class="space-y-4">
            @foreach($comments as $comment)
                @include('forum.partials.comment', ['comment' => $comment, 'thread' => $thread, 'depth' => 0])
            @endforeach
        </div>
        @else
        <div class="bg-zinc-900/50 rounded-lg p-8 text-center">
            <p class="text-zinc-400 text-lg">No comments yet. Be the first to comment!</p>
        </div>
        @endif
    </div>
</div>

<!-- Delete Warning Modal -->
<div id="deleteThreadWarningModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center" style="display: none;">
    <div class="bg-zinc-900 rounded-lg p-6 max-w-md w-full mx-4 border border-zinc-800">
        <h3 class="text-xl font-bold text-white mb-4">Delete Thread</h3>
        <p class="text-zinc-300 mb-6" id="deleteThreadWarningMessage">Are you sure you want to delete this thread? This action cannot be undone.</p>
        <form id="deleteThreadForm" method="POST" class="flex gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteThreadWarning()" class="flex-1 px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white rounded-lg transition-colors">
                Cancel
            </button>
            <button type="submit" class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                Delete
            </button>
        </form>
    </div>
</div>

<script>
function showDeleteThreadWarning(slug, threadTitle) {
    const modal = document.getElementById('deleteThreadWarningModal');
    const form = document.getElementById('deleteThreadForm');
    const message = document.getElementById('deleteThreadWarningMessage');
    
    if (modal && form && message) {
        message.textContent = `Are you sure you want to delete "${threadTitle}"? This action cannot be undone.`;
        form.action = `/forum/${slug}`;
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
    }
}

function closeDeleteThreadWarning() {
    const modal = document.getElementById('deleteThreadWarningModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
}

document.addEventListener('click', function(event) {
    const modal = document.getElementById('deleteThreadWarningModal');
    if (modal && event.target === modal) {
        closeDeleteThreadWarning();
    }
});
</script>
@endsection
