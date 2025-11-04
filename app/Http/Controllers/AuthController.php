<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        // If user is already logged in, redirect to appropriate dashboard
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('user.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'UserName' => 'required|string',
            'password' => 'required|string',
        ]);

        // Try to login with UserName or Email
        $credentials = $request->only('UserName', 'password');
        
        // First try with UserName
        $user = \App\Models\User::where('UserName', $credentials['UserName'])->first();
        
        // If not found, try with Email
        if (!$user) {
            $user = \App\Models\User::where('Email', $credentials['UserName'])->first();
        }

        // If user found and password matches
        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Check email verification BEFORE logging in
            if (!$user->isAdmin() && !$user->hasVerifiedEmail()) {
                // Don't log in unverified users - redirect to verification pending page
                return redirect()->route('verification.pending')
                    ->with('email', $user->Email)
                    ->with('error', 'Please verify your email address before logging in.');
            }
            
            // Log in only verified users (or admins)
            Auth::login($user);
            $request->session()->regenerate();
            
            // Redirect to appropriate dashboard
            if ($user->isAdmin()) {
                // Admins don't need email verification
                return redirect()->intended(route('admin.dashboard'));
            }
            
            return redirect()->intended(route('user.dashboard'));
        }

        // If both fail, return back with error
        return back()->withErrors([
            'UserName' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('UserName'));
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

