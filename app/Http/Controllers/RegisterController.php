<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        // If user is already logged in, redirect to dashboard
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('user.dashboard');
        }

        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'FirstName' => 'required|string|max:255',
            'LastName' => 'required|string|max:255',
            'Email' => 'required|string|email|max:255|unique:Users,Email',
            'UserName' => 'nullable|string|max:255|unique:Users,UserName',
            'PublicName' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|max:15|confirmed',
        ], [
            'FirstName.required' => 'First Name is required.',
            'LastName.required' => 'Last Name is required.',
            'Email.required' => 'Email address is required.',
            'Email.email' => 'Please provide a valid email address.',
            'Email.unique' => 'This email is already registered.',
            'UserName.unique' => 'This username is already taken.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.max' => 'Password must not exceed 15 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create new user
        $user = User::create([
            'FirstName' => $request->FirstName,
            'LastName' => $request->LastName,
            'Email' => $request->Email,
            'UserName' => $request->UserName ?? $request->Email,
            'PublicName' => $request->PublicName ?? $request->FirstName . ' ' . $request->LastName,
            'password' => Hash::make($request->password),
            'RoleID' => 2, // Regular user (not admin)
            'StatusID' => 1, // Active status
            'UUID' => Str::uuid()->toString(),
            'email_verified_at' => null, // Not verified yet
        ]);

        // Send email verification notification (only for regular users, admins don't need it)
        try {
            // Log SMTP configuration
            \Log::info('Attempting to send email verification notification', [
                'user_id' => $user->UserID,
                'email' => $user->Email,
                'mailer' => config('mail.default'),
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port'),
                'mail_encryption' => config('mail.mailers.smtp.encryption'),
                'mail_from_address' => config('mail.from.address'),
            ]);
            
            $user->sendEmailVerificationNotification();
            
            \Log::info('Email verification notification sent', [
                'user_id' => $user->UserID,
                'email' => $user->Email,
                'mailer' => config('mail.default')
            ]);
        } catch (\Swift_TransportException | \Swift_RfcComplianceException $e) {
            \Log::error('SMTP Transport error when sending verification email', [
                'user_id' => $user->UserID,
                'email' => $user->Email,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            // Continue anyway - user can resend later
        } catch (\Exception $e) {
            \Log::error('Failed to send email verification notification', [
                'user_id' => $user->UserID,
                'email' => $user->Email,
                'error' => $e->getMessage(),
                'class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            // Continue anyway - user can resend later
        }

        // Redirect to verification notice page (no login required)
        return redirect()->route('verification.pending')->with('email', $user->Email);
    }
}
