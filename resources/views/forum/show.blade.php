@extends('layouts.app')

@section('title', $thread->title . ' - Forum - Karahanyuze')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black pb-24">
    <div class="container mx-auto px-4 py-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('forum.index') }}" class="inline-flex items-center gap-2 text-white hover:text-green-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Back to Forum</span>
            </a>
        </div>

        <!-- Thread Header -->
        <div class="bg-zinc-800 rounded-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-4">
                @if($thread->is_pinned)
                <span class="px-2 py-1 bg-green-500 text-white text-xs font-semibold rounded">Pinned</span>
                @endif
                @if($thread->is_locked)
                <span class="px-2 py-1 bg-red-500 text-white text-xs font-semibold rounded">Locked</span>
                @endif
            </div>
            <h1 class="text-3xl font-bold text-white mb-4">{{ $thread->title }}</h1>
            <div class="flex items-center gap-6 text-sm text-zinc-400 mb-4">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>{{ $thread->user->PublicName ?? $thread->user->FirstName . ' ' . $thread->user->LastName }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $thread->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
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
                <a href="{{ route('forum.edit', $thread->slug) }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-colors text-sm">
                    Edit
                </a>
                <form action="{{ route('forum.destroy', $thread->slug) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this thread?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition-colors text-sm">
                        Delete
                    </button>
                </form>
                @endif
                <button onclick="openFlagModal('thread', {{ $thread->ThreadID }})" class="px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-white font-semibold rounded-lg transition-colors text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
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
            <div class="bg-zinc-800 rounded-lg p-4 mb-4">
                <p class="text-zinc-400 text-sm">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Comments are posted immediately. If a comment is flagged, an admin will review it.
                </p>
            </div>
            <div class="bg-zinc-800 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-white mb-4">Post a Comment</h3>
                <form action="{{ route('forum.comments.store', $thread->slug) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <textarea
                            name="body"
                            rows="5"
                            required
                            placeholder="Write your comment here..."
                            class="w-full px-4 py-3 bg-zinc-900 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        >{{ old('body') }}</textarea>
                        @error('body')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-colors">
                        Post Comment
                    </button>
                </form>
            </div>
            @else
            <div class="bg-zinc-800 rounded-lg p-6 mb-6 text-center">
                <p class="text-zinc-400 mb-4">Please <a href="{{ route('login') }}" class="text-green-500 hover:text-green-400">login</a> to post a comment.</p>
            </div>
            @endauth
            @else
            <div class="bg-zinc-800 rounded-lg p-6 mb-6 text-center">
                <p class="text-zinc-400">This thread is locked and cannot receive new comments.</p>
            </div>
            @endif

            <!-- Comments List -->
            @if($comments->count() > 0)
            <div class="space-y-6">
                @foreach($comments as $comment)
                    @include('forum.partials.comment', ['comment' => $comment, 'depth' => 0])
                @endforeach
            </div>
            @else
            <div class="bg-zinc-800 rounded-lg p-12 text-center">
                <p class="text-zinc-400 text-lg">No comments yet. Be the first to comment!</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Flag Modal -->
<div id="flagModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-zinc-800 rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-white">Flag Content</h3>
            <button onclick="closeFlagModal()" class="text-zinc-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="flagForm" action="{{ route('forum.flags.store') }}" method="POST">
            @csrf
            <input type="hidden" name="flaggable_type" id="flagType">
            <input type="hidden" name="flaggable_id" id="flagId">
            <div class="mb-4">
                <label for="flagReason" class="block text-white font-semibold mb-2">Reason <span class="text-red-400">*</span></label>
                <select name="reason" id="flagReason" required class="w-full px-4 py-3 bg-zinc-900 border border-zinc-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">Select a reason...</option>
                    <option value="Spam">Spam</option>
                    <option value="Inappropriate Content">Inappropriate Content</option>
                    <option value="Harassment">Harassment</option>
                    <option value="Off-topic">Off-topic</option>
                    <option value="Misinformation">Misinformation</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="flagNotes" class="block text-white font-semibold mb-2">Additional Notes (Optional)</label>
                <textarea name="notes" id="flagNotes" rows="4" placeholder="Provide any additional details..." class="w-full px-4 py-3 bg-zinc-900 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition-colors flex-1">
                    Submit Flag
                </button>
                <button type="button" onclick="closeFlagModal()" class="px-6 py-3 bg-zinc-700 hover:bg-zinc-600 text-white font-semibold rounded-lg transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openFlagModal(type, id) {
    document.getElementById('flagType').value = type;
    document.getElementById('flagId').value = id;
    document.getElementById('flagModal').classList.remove('hidden');
    document.getElementById('flagModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeFlagModal() {
    document.getElementById('flagModal').classList.add('hidden');
    document.getElementById('flagModal').classList.remove('flex');
    document.body.style.overflow = '';
    document.getElementById('flagForm').reset();
}

// Close modal when clicking outside
document.getElementById('flagModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeFlagModal();
    }
});
</script>

@if(session('scroll_to'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const element = document.getElementById('{{ session('scroll_to') }}');
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            element.classList.add('animate-pulse');
            setTimeout(() => element.classList.remove('animate-pulse'), 2000);
        }
    });
</script>
@endif
@endsection

