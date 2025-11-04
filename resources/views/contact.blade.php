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
                    <a href="mailto:daniyum21@gmail.com" class="text-green-500 hover:text-green-400 transition-colors">
                        daniyum21@gmail.com
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

