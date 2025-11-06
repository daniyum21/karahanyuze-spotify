@extends('layouts.spotify')

@section('title', 'Create Thread - Forum - Karahanyuze')

@section('content')
<div class="px-6 py-8 pb-24">
    <div class="max-w-3xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('forum.index') }}" class="inline-flex items-center gap-2 text-zinc-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Back to Forum</span>
            </a>
        </div>

        <div class="bg-zinc-900/50 rounded-lg p-8">
            <h1 class="text-4xl font-bold text-white mb-6">Create New Thread</h1>

            @if(!Auth::user()->isAdmin())
            <!-- Approval Notice -->
            <div class="bg-blue-500/10 border border-blue-500 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    <div>
                        <p class="text-blue-300 font-semibold mb-1">Thread Approval Required</p>
                        <p class="text-blue-200 text-sm">Your thread will be reviewed by an admin before it becomes visible to other users. You will be notified once it's approved.</p>
                    </div>
                </div>
            </div>
            @endif

            @if($errors->any())
            <div class="bg-red-500/10 border border-red-500 rounded-lg p-4 mb-6">
                <ul class="text-red-400 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('forum.store') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="title" class="block text-white font-semibold mb-2">Title</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        maxlength="255"
                        placeholder="Enter thread title..."
                        class="w-full px-4 py-3 bg-black border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white"
                    />
                    @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="body" class="block text-white font-semibold mb-2">Content <span class="text-zinc-400 font-normal">(Optional)</span></label>
                    <textarea
                        id="body"
                        name="body"
                        rows="12"
                        maxlength="10000"
                        placeholder="Write your thread content here (optional)..."
                        class="w-full px-4 py-3 bg-black border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white"
                    >{{ old('body') }}</textarea>
                    <p class="text-zinc-400 text-sm mt-2">Maximum 10,000 characters. You can create a thread with just a title if you prefer.</p>
                    @error('body')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="px-6 py-3 bg-white hover:bg-zinc-200 text-black font-bold rounded-full transition-colors">
                        Create Thread
                    </button>
                    <a href="{{ route('forum.index') }}" class="px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold rounded-full transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
