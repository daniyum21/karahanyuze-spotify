<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Check if user has admin role (RoleID == 1 or similar)
        // For now, we'll allow all authenticated users to access admin
        // You can add more specific role checking later
        $user = Auth::user();
        
        // If you have a RoleID field, check it:
        // if ($user->RoleID != 1) {
        //     abort(403, 'Unauthorized access');
        // }

        return $next($request);
    }
}

