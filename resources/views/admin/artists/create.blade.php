@extends('layouts.app')

@section('title', 'Create Artist - Admin')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Create New Artist</h1>
                <p class="text-zinc-400">Add a new artist to the Karahanyuze collection</p>
            </div>

            @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="bg-red-500/20 border border-red-500 text-red-400 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.artists.store') }}" method="POST" enctype="multipart/form-data" class="bg-zinc-900 rounded-lg p-8">
                @csrf

                <div class="space-y-6">
                    <!-- First Name -->
                    <div>
                        <label for="FirstName" class="block text-sm font-medium text-white mb-2">First Name (Optional)</label>
                        <input 
                            type="text" 
                            id="FirstName" 
                            name="FirstName" 
                            value="{{ old('FirstName') }}"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Enter first name (optional)"
                        >
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="LastName" class="block text-sm font-medium text-white mb-2">Last Name (Optional)</label>
                        <input 
                            type="text" 
                            id="LastName" 
                            name="LastName" 
                            value="{{ old('LastName') }}"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Enter last name (optional)"
                        >
                    </div>

                    <!-- Stage Name -->
                    <div>
                        <label for="StageName" class="block text-sm font-medium text-white mb-2">Stage Name *</label>
                        <input 
                            type="text" 
                            id="StageName" 
                            name="StageName" 
                            value="{{ old('StageName') }}"
                            required
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Enter stage name"
                        >
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="Email" class="block text-sm font-medium text-white mb-2">Email (Optional)</label>
                        <input 
                            type="email" 
                            id="Email" 
                            name="Email" 
                            value="{{ old('Email') }}"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Enter email address (optional)"
                        >
                    </div>

                    <!-- Twitter -->
                    <div>
                        <label for="Twitter" class="block text-sm font-medium text-white mb-2">Twitter Account (Optional)</label>
                        <input 
                            type="text" 
                            id="Twitter" 
                            name="Twitter" 
                            value="{{ old('Twitter') }}"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Enter Twitter handle (optional)"
                        >
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="Description" class="block text-sm font-medium text-white mb-2">Description</label>
                        <textarea 
                            id="Description" 
                            name="Description" 
                            rows="6"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Enter artist description"
                        >{{ old('Description') }}</textarea>
                    </div>

                    <!-- Featured -->
                    <div>
                        <label class="flex items-center gap-3">
                            <input 
                                type="checkbox" 
                                name="IsFeatured" 
                                value="1"
                                {{ old('IsFeatured') ? 'checked' : '' }}
                                class="w-5 h-5 text-green-500 bg-zinc-800 border-zinc-700 rounded focus:ring-green-500"
                            >
                            <span class="text-white">Featured Artist</span>
                        </label>
                    </div>

                    <!-- Image File -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-white mb-2">Profile Picture (Optional)</label>
                        <input 
                            type="file" 
                            id="image" 
                            name="image"
                            accept="image/jpeg,image/jpg,image/png"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-500 file:text-white hover:file:bg-green-600"
                        >
                        <p class="text-xs text-zinc-500 mt-2">Maximum file size: 2MB</p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button 
                            type="submit" 
                            class="flex-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
                        >
                            Create Artist
                        </button>
                        <a 
                            href="{{ route('admin.artists.index') }}" 
                            class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors text-center"
                        >
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

