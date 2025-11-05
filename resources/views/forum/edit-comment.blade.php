@extends('layouts.app')

@section('title', 'Edit Comment - Forum - Karahanyuze')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black pb-24">
    <div class="container mx-auto px-4 py-8 max-w-3xl">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('forum.show', $comment->thread->slug) }}" class="inline-flex items-center gap-2 text-white hover:text-green-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Back to Thread</span>
            </a>
        </div>

        <div class="bg-zinc-800 rounded-lg p-8">
            <h1 class="text-3xl font-bold text-white mb-6">Edit Comment</h1>

            @if($errors->any())
            <div class="bg-red-500/10 border border-red-500 rounded-lg p-4 mb-6">
                <ul class="text-red-400 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('forum.comments.update', $comment->CommentID) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-6">
                    <label for="body" class="block text-white font-semibold mb-2">Comment</label>
                    <textarea
                        id="body"
                        name="body"
                        rows="8"
                        required
                        maxlength="5000"
                        class="w-full px-4 py-3 bg-zinc-900 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    >{{ old('body', $comment->body) }}</textarea>
                    <p class="text-zinc-400 text-sm mt-2">Maximum 5,000 characters</p>
                    @error('body')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-colors">
                        Update Comment
                    </button>
                    <a href="{{ route('forum.show', $comment->thread->slug) }}" class="px-6 py-3 bg-zinc-700 hover:bg-zinc-600 text-white font-semibold rounded-lg transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

