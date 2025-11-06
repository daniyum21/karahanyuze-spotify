@extends('layouts.spotify')

@section('title', 'Create Account - Karahanyuze')

@section('content')
<div class="flex items-center justify-center min-h-screen px-6 py-12">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Sign up for Karahanyuze</h1>
            <p class="text-zinc-400">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-white hover:underline font-medium">
                    Log in
                </a>
            </p>
        </div>
        
        @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-zinc-900 rounded-lg p-8">
            <form action="{{ route('register') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label for="FirstName" class="block text-sm font-medium text-white mb-2">
                            First Name <span class="text-red-400">*</span>
                        </label>
                        <input 
                            id="FirstName" 
                            name="FirstName" 
                            type="text" 
                            required 
                            value="{{ old('FirstName') }}"
                            class="w-full px-4 py-3 bg-black border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white"
                            placeholder="First Name"
                        >
                        @error('FirstName')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="LastName" class="block text-sm font-medium text-white mb-2">
                            Last Name <span class="text-red-400">*</span>
                        </label>
                        <input 
                            id="LastName" 
                            name="LastName" 
                            type="text" 
                            required 
                            value="{{ old('LastName') }}"
                            class="w-full px-4 py-3 bg-black border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white"
                            placeholder="Last Name"
                        >
                        @error('LastName')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="Email" class="block text-sm font-medium text-white mb-2">
                            Email Address <span class="text-red-400">*</span>
                        </label>
                        <input 
                            id="Email" 
                            name="Email" 
                            type="email" 
                            required 
                            value="{{ old('Email') }}"
                            class="w-full px-4 py-3 bg-black border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white"
                            placeholder="your@email.com"
                        >
                        @error('Email')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="UserName" class="block text-sm font-medium text-white mb-2">
                            Username <span class="text-zinc-500 text-xs">(Optional)</span>
                        </label>
                        <input 
                            id="UserName" 
                            name="UserName" 
                            type="text" 
                            value="{{ old('UserName') }}"
                            class="w-full px-4 py-3 bg-black border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white"
                            placeholder="Username"
                        >
                        @error('UserName')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="PublicName" class="block text-sm font-medium text-white mb-2">
                            Public Name <span class="text-zinc-500 text-xs">(Optional)</span>
                        </label>
                        <input 
                            id="PublicName" 
                            name="PublicName" 
                            type="text" 
                            value="{{ old('PublicName') }}"
                            class="w-full px-4 py-3 bg-black border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white"
                            placeholder="Public Display Name"
                        >
                        @error('PublicName')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-white mb-2">
                            Password <span class="text-red-400">*</span>
                        </label>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required 
                            class="w-full px-4 py-3 bg-black border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white"
                            placeholder="Password (min 6 characters)"
                        >
                        @error('password')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-white mb-2">
                            Confirm Password <span class="text-red-400">*</span>
                        </label>
                        <input 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            required 
                            class="w-full px-4 py-3 bg-black border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white"
                            placeholder="Confirm Password"
                        >
                    </div>
                </div>

                <div>
                    <button 
                        type="submit" 
                        class="w-full bg-white hover:bg-zinc-200 text-black font-bold py-3 px-6 rounded-full transition-colors"
                    >
                        Sign up
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-xs text-zinc-400">
                        By registering, you agree to verify your email address before logging in.
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
