@extends('layouts.app')

@section('title', 'Verify Your Email - Karahanyuze')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <!-- Email Icon -->
            <div class="mx-auto w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            
            <h2 class="text-3xl font-extrabold text-white mb-2">
                Check Your Email
            </h2>
            <p class="text-zinc-400 mb-4">
                Registration successful! We've sent a verification link to your email address.
            </p>
            @if(session('email'))
            <p class="text-green-400 font-semibold mb-6">
                {{ session('email') }}
            </p>
            @endif
            <p class="text-zinc-300 text-sm mb-6">
                Please check your inbox and click on the verification link to activate your account. You won't be able to log in until you verify your email address.
            </p>
        </div>

        @if(session('status') == 'verification-link-sent')
        <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                A new verification link has been sent to your email address.
            </div>
        </div>
        @endif

        <div class="bg-zinc-900 rounded-lg p-6 space-y-4">
            <div class="text-center mb-4">
                <p class="text-zinc-400 text-sm mb-4">
                    Didn't receive the email? Check your spam folder or resend the verification link.
                </p>
            </div>

            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                @if(session('email'))
                <input type="hidden" name="email" value="{{ session('email') }}">
                @else
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-zinc-300 mb-2">
                        Email Address
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="Enter your email address"
                    >
                    @error('email')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                @endif
                
                <button 
                    type="submit" 
                    class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Resend Verification Email
                </button>
            </form>

            <div class="pt-4 border-t border-zinc-800">
                <p class="text-center text-zinc-400 text-sm mb-4">
                    Already verified your email?
                </p>
                <a 
                    href="{{ route('login') }}" 
                    class="block w-full text-center py-2 px-4 border border-zinc-700 text-sm font-medium rounded-lg text-zinc-300 bg-zinc-800 hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-500 transition-colors"
                >
                    Go to Login
                </a>
            </div>
        </div>

        <!-- Help Section -->
        <div class="bg-zinc-900/50 rounded-lg p-4">
            <p class="text-zinc-400 text-xs text-center">
                If you're having trouble receiving the verification email, please check your spam folder or contact support.
            </p>
        </div>
    </div>
</div>
@endsection

