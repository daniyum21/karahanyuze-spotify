@extends('layouts.app')

@section('title', 'Login - Karahanyuze')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-white mb-2">Login to your Account</h1>
            <p class="text-zinc-400">Niba ushaka gushyiraho indirimbo, ugomba kugira account</p>
        </div>

        @if(session('error'))
        <div class="bg-red-500/20 border border-red-500 text-red-400 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
        @endif

        @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-6">
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

        <div class="bg-zinc-900 rounded-lg p-8">
            <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="UserName" class="block text-sm font-medium text-white mb-2">
                        Username or Email <span class="text-red-400">*</span>
                    </label>
                    <input
                        type="text"
                        id="UserName"
                        name="UserName"
                        value="{{ old('UserName') }}"
                        required
                        autofocus
                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="Enter your username or email"
                    >
                    @error('UserName')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-white mb-2">
                        Password <span class="text-red-400">*</span>
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full px-4 py-3 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="Enter your password"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button
                        type="submit"
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
                    >
                        Login
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-zinc-400 text-sm">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="text-green-500 hover:text-green-400 transition-colors font-medium">
                            Iyandikishe
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

