@extends('layouts.app')

@section('title', 'Contact Us - Karahanyuze')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Twandikire</h1>
                <p class="text-xl text-zinc-300">daniyum21@gmail.com cyangwa X.com @Tangente</p>
            </div>

            <!-- Contact Information -->
            <div class="bg-zinc-900 rounded-lg p-8 md:p-12 mb-8">
                <div class="prose prose-invert max-w-none">
                    <p class="text-zinc-300 text-lg leading-relaxed mb-6">
                        Niba wifuza kunyandikira wenda ungezaho ibitekerezo byubaka, cyangwa se hari ubundi bufasha wantera, wanyandikira kuri 
                        <a href="mailto:daniyum21@gmail.com" class="text-green-500 hover:text-green-400 transition-colors font-semibold">daniyum21@gmail.com</a>, 
                        cyangwa kurubuga rwa X.com kuri 
                        <a href="https://www.x.com/tangente" target="_blank" rel="noopener noreferrer" class="text-green-500 hover:text-green-400 transition-colors font-semibold">@tangente</a>
                    </p>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-zinc-900 rounded-lg p-8 md:p-12 mb-8">
                <h2 class="text-2xl font-bold text-white mb-6">Send Us a Message</h2>
                
                @if(session('success'))
                <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="bg-red-500/20 border border-red-500 text-red-400 px-4 py-3 rounded-lg mb-6">
                    {{ session('error') }}
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

                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-white mb-2">Name *</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $userName ?? '') }}"
                            required
                            {{ auth()->check() && $userName ? 'disabled' : '' }}
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 {{ auth()->check() && $userName ? 'opacity-60 cursor-not-allowed' : '' }}"
                            placeholder="Your name"
                        >
                        @if(auth()->check() && $userName)
                            <input type="hidden" name="name" value="{{ $userName }}">
                        @endif
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-white mb-2">Email *</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email', $userEmail ?? '') }}"
                            required
                            {{ auth()->check() && $userEmail ? 'disabled' : '' }}
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 {{ auth()->check() && $userEmail ? 'opacity-60 cursor-not-allowed' : '' }}"
                            placeholder="your.email@example.com"
                        >
                        @if(auth()->check() && $userEmail)
                            <input type="hidden" name="email" value="{{ $userEmail }}">
                        @endif
                    </div>

                    <!-- Subject -->
                    <div>
                        <label for="subject" class="block text-sm font-medium text-white mb-2">Subject *</label>
                        <input 
                            type="text" 
                            id="subject" 
                            name="subject" 
                            value="{{ old('subject') }}"
                            required
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="What is this about?"
                        >
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-sm font-medium text-white mb-2">Message *</label>
                        <textarea 
                            id="message" 
                            name="message" 
                            rows="6"
                            required
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Your message here..."
                        >{{ old('message') }}</textarea>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button 
                            type="submit" 
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
                        >
                            Send Message
                        </button>
                    </div>
                </form>
            </div>

            <!-- Contact Details Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Email Card -->
                <div class="bg-zinc-800 rounded-lg p-6 hover:bg-zinc-700 transition-colors">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white">Email</h3>
                    </div>
                    <a href="mailto:info@karahanyuze.com" class="text-green-500 hover:text-green-400 transition-colors">
                        info@karahanyuze.com
                    </a>
                </div>

                <!-- X.com Card -->
                <div class="bg-zinc-800 rounded-lg p-6 hover:bg-zinc-700 transition-colors">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white">X.com</h3>
                    </div>
                    <a href="https://www.x.com/tangente" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:text-blue-400 transition-colors">
                        @tangente
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

