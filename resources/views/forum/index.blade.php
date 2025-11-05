@extends('layouts.app')

@section('title', 'Forum - Karahanyuze')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black pb-24">
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">Forum</h1>
                <p class="text-zinc-400">Discuss songs, ask questions, and share your thoughts</p>
            </div>
            @auth
            <a href="{{ route('forum.create') }}" class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Thread
            </a>
            @else
            <a href="{{ route('login') }}" class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-colors">
                Login to Post
            </a>
            @endauth
        </div>

        <!-- Search Bar -->
        <form action="{{ route('forum.index') }}" method="GET" class="mb-6">
            <div class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-zinc-400 pointer-events-none z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search threads..."
                    class="w-full pl-14 pr-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                />
            </div>
        </form>

        <!-- Pending Threads Section (for logged-in users) -->
        @auth
        @if($pendingThreads->count() > 0)
        <div class="bg-yellow-500/10 border border-yellow-500 rounded-lg p-6 mb-6">
            <div class="flex items-start gap-3 mb-4">
                <svg class="w-5 h-5 text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
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
        <div class="space-y-4">
            @foreach($threads as $thread)
            <div class="bg-zinc-800 rounded-lg p-6 hover:bg-zinc-700/80 transition-colors">
                <div class="flex items-start gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            @if($thread->is_pinned)
                            <span class="px-2 py-1 bg-green-500 text-white text-xs font-semibold rounded">Pinned</span>
                            @endif
                            @if($thread->is_locked)
                            <span class="px-2 py-1 bg-red-500 text-white text-xs font-semibold rounded">Locked</span>
                            @endif
                            <a href="{{ route('forum.show', $thread->slug) }}" class="text-xl font-bold text-white hover:text-green-500 transition-colors">
                                {{ $thread->title }}
                            </a>
                        </div>
                        @if($thread->body && trim($thread->body) !== '')
                        <p class="text-zinc-300 mb-4 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($thread->body), 150) }}</p>
                        @endif
                        <div class="flex items-center gap-6 text-sm text-zinc-400">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>{{ $thread->user->PublicName ?? $thread->user->FirstName . ' ' . $thread->user->LastName }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <span>{{ $thread->all_comments_count ?? 0 }} {{ ($thread->all_comments_count ?? 0) == 1 ? 'comment' : 'comments' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span>{{ $thread->view_count }} views</span>
                            </div>
                            @if($thread->last_comment_at)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Last reply {{ $thread->last_comment_at->diffForHumans() }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $threads->links() }}
        </div>
        @else
        <div class="text-center py-12 bg-zinc-800 rounded-lg">
            <svg class="w-16 h-16 text-zinc-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <p class="text-zinc-400 text-lg mb-4">No threads found</p>
            @auth
            <a href="{{ route('forum.create') }}" class="inline-block px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-colors">
                Create First Thread
            </a>
            @endauth
        </div>
        @endif
    </div>
</div>
@endsection

