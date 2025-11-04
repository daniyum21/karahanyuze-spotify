@extends('layouts.app')

@section('title', 'Create Account - Karahanyuze')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                Create Account
            </h2>
            <p class="mt-2 text-center text-sm text-zinc-400">
                Or
                <a href="{{ route('login') }}" class="font-medium text-green-400 hover:text-green-300">
                    sign in to your existing account
                </a>
            </p>
        </div>
        
        @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="FirstName" class="block text-sm font-medium text-zinc-300 mb-1">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        id="FirstName" 
                        name="FirstName" 
                        type="text" 
                        required 
                        value="{{ old('FirstName') }}"
                        class="appearance-none relative block w-full px-3 py-2 border border-zinc-700 bg-zinc-900 text-white placeholder-zinc-500 rounded-lg focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                        placeholder="First Name"
                    >
                    @error('FirstName')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="LastName" class="block text-sm font-medium text-zinc-300 mb-1">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        id="LastName" 
                        name="LastName" 
                        type="text" 
                        required 
                        value="{{ old('LastName') }}"
                        class="appearance-none relative block w-full px-3 py-2 border border-zinc-700 bg-zinc-900 text-white placeholder-zinc-500 rounded-lg focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                        placeholder="Last Name"
                    >
                    @error('LastName')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="Email" class="block text-sm font-medium text-zinc-300 mb-1">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input 
                        id="Email" 
                        name="Email" 
                        type="email" 
                        required 
                        value="{{ old('Email') }}"
                        class="appearance-none relative block w-full px-3 py-2 border border-zinc-700 bg-zinc-900 text-white placeholder-zinc-500 rounded-lg focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                        placeholder="your@email.com"
                    >
                    @error('Email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="UserName" class="block text-sm font-medium text-zinc-300 mb-1">
                        Username <span class="text-zinc-500 text-xs">(Optional)</span>
                    </label>
                    <input 
                        id="UserName" 
                        name="UserName" 
                        type="text" 
                        value="{{ old('UserName') }}"
                        class="appearance-none relative block w-full px-3 py-2 border border-zinc-700 bg-zinc-900 text-white placeholder-zinc-500 rounded-lg focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                        placeholder="Username"
                    >
                    @error('UserName')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="PublicName" class="block text-sm font-medium text-zinc-300 mb-1">
                        Public Name <span class="text-zinc-500 text-xs">(Optional)</span>
                    </label>
                    <input 
                        id="PublicName" 
                        name="PublicName" 
                        type="text" 
                        value="{{ old('PublicName') }}"
                        class="appearance-none relative block w-full px-3 py-2 border border-zinc-700 bg-zinc-900 text-white placeholder-zinc-500 rounded-lg focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                        placeholder="Public Display Name"
                    >
                    @error('PublicName')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-zinc-300 mb-1">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        required 
                        class="appearance-none relative block w-full px-3 py-2 border border-zinc-700 bg-zinc-900 text-white placeholder-zinc-500 rounded-lg focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                        placeholder="Password (min 6 characters)"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-zinc-300 mb-1">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        type="password" 
                        required 
                        class="appearance-none relative block w-full px-3 py-2 border border-zinc-700 bg-zinc-900 text-white placeholder-zinc-500 rounded-lg focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                        placeholder="Confirm Password"
                    >
                </div>
            </div>

            <div>
                <button 
                    type="submit" 
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
                >
                    Register
                </button>
            </div>

            <div class="text-center">
                <p class="text-xs text-zinc-500">
                    By registering, you agree to verify your email address before logging in.
                </p>
            </div>
        </form>
    </div>
</div>
@endsection

