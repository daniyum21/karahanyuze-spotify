@extends('layouts.app')

@section('title', 'Verify Email - Karahanyuze')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-white mb-2">
                Verify Your Email
            </h2>
            <p class="text-zinc-400">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?
            </p>
        </div>

        @if(session('status') == 'verification-link-sent')
        <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg">
            A new verification link has been sent to your email address.
        </div>
        @endif

        <div class="bg-zinc-900 rounded-lg p-6 space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button 
                    type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
                >
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button 
                    type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-zinc-700 text-sm font-medium rounded-lg text-zinc-300 bg-zinc-800 hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-500 transition-colors"
                >
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

