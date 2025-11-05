@php
    $maxDepth = 3; // Maximum nesting depth
@endphp

<div id="comment-{{ $comment->CommentID }}" class="bg-zinc-800 rounded-lg p-6 {{ $depth > 0 ? 'ml-8 border-l-2 border-zinc-700' : '' }}">
    <div class="flex items-start gap-4">
        <div class="flex-shrink-0">
            <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white font-semibold">
                {{ strtoupper(substr($comment->user->PublicName ?? $comment->user->FirstName, 0, 1)) }}
            </div>
        </div>
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                <span class="font-semibold text-white">
                    {{ $comment->user->PublicName ?? $comment->user->FirstName . ' ' . $comment->user->LastName }}
                </span>
                <span class="text-zinc-400 text-sm">{{ $comment->created_at->diffForHumans() }}</span>
                @if($comment->is_edited)
                <span class="text-zinc-500 text-xs">(edited)</span>
                @endif
            </div>
            <div class="text-zinc-300 whitespace-pre-wrap mb-4">{{ $comment->body }}</div>
            <div class="flex items-center gap-4">
                @auth
                @php
                    $thread = $comment->thread ?? null;
                @endphp
                @if($thread && !$thread->is_locked && $depth < $maxDepth)
                <button onclick="toggleReplyForm({{ $comment->CommentID }})" class="text-green-500 hover:text-green-400 text-sm font-medium transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                    </svg>
                    Reply
                </button>
                @endif
                @if($comment->UserID === Auth::id() || Auth::user()->isAdmin())
                <a href="{{ route('forum.comments.edit', $comment->CommentID) }}" class="text-blue-500 hover:text-blue-400 text-sm font-medium transition-colors">
                    Edit
                </a>
                <form action="{{ route('forum.comments.destroy', $comment->CommentID) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-400 text-sm font-medium transition-colors">
                        Delete
                    </button>
                </form>
                @endif
                <button onclick="openFlagModal('comment', {{ $comment->CommentID }})" class="text-zinc-500 hover:text-zinc-400 text-sm font-medium transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Flag
                </button>
                @endauth
            </div>

            <!-- Reply Form (Hidden by default) -->
            @auth
            @if($thread && !$thread->is_locked && $depth < $maxDepth)
            <div id="reply-form-{{ $comment->CommentID }}" class="hidden mt-4 pt-4 border-t border-zinc-700">
                <form action="{{ route('forum.comments.store', $thread->slug) }}" method="POST">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $comment->CommentID }}">
                    <div class="mb-4">
                        <textarea
                            name="body"
                            rows="3"
                            required
                            placeholder="Write your reply..."
                            class="w-full px-4 py-3 bg-zinc-900 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        ></textarea>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-colors text-sm">
                            Post Reply
                        </button>
                        <button type="button" onclick="toggleReplyForm({{ $comment->CommentID }})" class="px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-white font-semibold rounded-lg transition-colors text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
            @endif
            @endauth

            <!-- Nested Replies -->
            @if($comment->replies && $comment->replies->count() > 0)
            <div class="mt-6 space-y-4">
                @foreach($comment->replies as $reply)
                    @php
                        if ($depth < 2) {
                            $reply->load('replies.user');
                        }
                    @endphp
                    @include('forum.partials.comment', ['comment' => $reply, 'depth' => $depth + 1])
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleReplyForm(commentId) {
    const form = document.getElementById('reply-form-' + commentId);
    if (form) {
        form.classList.toggle('hidden');
    }
}
</script>

