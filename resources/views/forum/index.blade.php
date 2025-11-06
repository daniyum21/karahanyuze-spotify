@extends('layouts.spotify')

@section('title', 'Forum - Karahanyuze')

@section('content')
<div class="px-6 py-8 pb-24">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Forum</h1>
            <p class="text-zinc-400">Discuss songs, ask questions, and share your thoughts</p>
        </div>
        @auth
        <a href="{{ route('forum.create') }}" class="px-6 py-3 bg-white hover:bg-zinc-200 text-black font-bold rounded-full transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
            </svg>
            New Thread
        </a>
        @else
        <a href="{{ route('login') }}" class="px-6 py-3 bg-white hover:bg-zinc-200 text-black font-bold rounded-full transition-colors">
            Log in to Post
        </a>
        @endauth
    </div>

    <!-- Search Bar -->
    <form action="{{ route('forum.index') }}" method="GET" class="mb-8">
        <div class="relative">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-zinc-400 pointer-events-none z-10" fill="currentColor" viewBox="0 0 24 24">
                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
            </svg>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search threads..."
                class="w-full pl-14 pr-4 py-3 bg-zinc-900 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white"
            />
        </div>
    </form>

    <!-- Pending Threads Section (for logged-in users) -->
    @auth
    @if($pendingThreads->count() > 0)
    <div class="bg-yellow-500/10 border border-yellow-500 rounded-lg p-6 mb-8">
        <div class="flex items-start gap-3 mb-4">
            <svg class="w-5 h-5 text-yellow-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
            </svg>
            <div class="flex-1">
                <h2 class="text-lg font-semibold text-yellow-300 mb-2">Your Pending Threads</h2>
                <p class="text-yellow-200 text-sm mb-4">These threads are waiting for admin approval before they become visible to other users.</p>
                <div class="space-y-3">
                    @foreach($pendingThreads as $pendingThread)
                    <div class="bg-zinc-800 rounded-lg p-4">
                        <h3 class="font-semibold text-white mb-1">{{ $pendingThread->title }}</h3>
                        @if($pendingThread->body)
                        <p class="text-zinc-400 text-sm mb-2">{{ \Illuminate\Support\Str::limit(strip_tags($pendingThread->body), 100) }}</p>
                        @endif
                        <div class="flex items-center gap-4 text-xs text-zinc-500">
                            <span>Submitted {{ $pendingThread->created_at->diffForHumans() }}</span>
                            <span class="px-2 py-1 bg-yellow-500/20 text-yellow-300 rounded">Pending Approval</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
    @endauth

    @if($threads->count() > 0)
    <div class="space-y-2">
        @foreach($threads as $thread)
        <a href="{{ route('forum.show', $thread->slug) }}" class="block bg-zinc-900/50 hover:bg-zinc-800 rounded-lg p-6 transition-colors group">
            <div class="flex items-start gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-xl font-bold text-white group-hover:underline">{{ $thread->title }}</h2>
                        @if($thread->is_pinned)
                        <span class="px-2 py-1 bg-[#1db954] text-black text-xs font-bold rounded">PINNED</span>
                        @endif
                    </div>
                    @if($thread->body)
                    <p class="text-zinc-400 text-sm mb-4 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($thread->body), 150) }}</p>
                    @endif
                    <div class="flex items-center gap-4 text-sm text-zinc-400">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                            {{ $thread->user->FirstName ?? $thread->user->UserName }}
                        </span>
                        <span>{{ $thread->created_at->diffForHumans() }}</span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
                            </svg>
                            {{ $thread->all_comments_count ?? 0 }} comments
                        </span>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $threads->links() }}
    </div>
    @else
    <div class="text-center py-12">
        <p class="text-zinc-400 text-lg">No threads found.</p>
    </div>
    @endif
</div>
@endsection
