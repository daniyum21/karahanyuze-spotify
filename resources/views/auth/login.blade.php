@extends('layouts.spotify')

@section('title', 'Login - Karahanyuze')

@section('content')
<div class="flex items-center justify-center min-h-screen px-6">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Log in to Karahanyuze</h1>
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
                        class="w-full px-4 py-3 bg-black border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white"
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
                        class="w-full px-4 py-3 bg-black border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white"
                        placeholder="Enter your password"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button
                        type="submit"
                        class="w-full bg-white hover:bg-zinc-200 text-black font-bold py-3 px-6 rounded-full transition-colors"
                    >
                        Log in
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-zinc-400 text-sm">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="text-white hover:underline font-medium">
                            Sign up for Karahanyuze
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
