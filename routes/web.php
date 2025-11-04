<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\OrchestraController;
use App\Http\Controllers\ItoreroController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminSongController;
use App\Http\Controllers\Admin\AdminArtistController;
use App\Http\Controllers\Admin\AdminOrchestraController;
use App\Http\Controllers\Admin\AdminItoreroController;
use App\Http\Controllers\Admin\AdminPlaylistController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserDashboardController;
use App\Models\Song;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [HomeController::class, 'contactUs'])->name('contact');

// Test email route (for debugging - remove or protect in production)
Route::get('/test-email', function () {
    $mailer = config('mail.default');
    $smtpConfig = config('mail.mailers.smtp');
    
    $info = [
        'mailer' => $mailer,
        'smtp_host' => $smtpConfig['host'] ?? 'not set',
        'smtp_port' => $smtpConfig['port'] ?? 'not set',
        'smtp_username' => $smtpConfig['username'] ? 'set' : 'not set',
        'smtp_password' => $smtpConfig['password'] ? 'set' : 'not set',
        'smtp_encryption' => $smtpConfig['encryption'] ?? 'not set',
        'from_address' => config('mail.from.address'),
        'from_name' => config('mail.from.name'),
    ];
    
    if ($mailer === 'log' || $mailer === 'file') {
        return response()->json([
            'error' => 'Mailer is set to ' . $mailer . ' instead of smtp',
            'config' => $info,
            'message' => 'Please set MAIL_MAILER=smtp in your .env file and run: php artisan config:clear'
        ], 400);
    }
    
    try {
        $testEmail = request('email', config('mail.from.address'));
        
        // Test SMTP connection first
        $transport = new \Swift_SmtpTransport(
            $smtpConfig['host'],
            $smtpConfig['port'],
            $smtpConfig['encryption'] ?? 'tls'
        );
        $transport->setUsername($smtpConfig['username']);
        $transport->setPassword($smtpConfig['password']);
        $transport->setTimeout(10);
        
        $connectionTest = 'Not tested';
        try {
            $transport->start();
            $connectionTest = 'Connected successfully';
            $transport->stop();
        } catch (\Exception $e) {
            $connectionTest = 'Connection failed: ' . $e->getMessage();
        }
        
        // Now try to send the email
        $mailer = new \Swift_Mailer($transport);
        
        $message = (new \Swift_Message('Test Email from Karahanyuze'))
            ->setFrom(config('mail.from.address'), config('mail.from.name'))
            ->setTo($testEmail)
            ->setBody('This is a test email from Karahanyuze. If you receive this, your email configuration is working correctly!');
        
        $result = $mailer->send($message);
        
        // Stop the transport
        try {
            $transport->stop();
        } catch (\Exception $e) {
            // Ignore
        }
        
        Log::info('Test email sent successfully', [
            'to' => $testEmail,
            'mailer' => $mailer,
            'connection_test' => $connectionTest,
            'emails_sent' => $result
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Test email sent successfully to ' . $testEmail,
            'connection_test' => $connectionTest,
            'emails_sent' => $result,
            'config' => $info
        ]);
    } catch (\Exception $e) {
        Log::error('Test email failed', [
            'error' => $e->getMessage(),
            'class' => get_class($e),
            'code' => method_exists($e, 'getCode') ? $e->getCode() : null,
            'trace' => $e->getTraceAsString(),
            'config' => $info
        ]);
        
        return response()->json([
            'error' => 'Failed to send test email',
            'message' => $e->getMessage(),
            'class' => get_class($e),
            'config' => $info
        ], 500);
    }
})->middleware('auth')->name('test.email');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

// Email Verification Routes
Route::get('/email/verify', function () {
    // Redirect admins away from verification page (admins don't need email verification)
    $user = Auth::user();
    if ($user && $user->RoleID == 1) {
        return redirect()->route('admin.dashboard');
    }
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

// Verification pending page (for users who just registered, no auth required)
Route::get('/email/verify-pending', function () {
    return view('auth.verify-pending');
})->name('verification.pending');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('user.dashboard')->with('success', 'Email verified successfully!');
})->middleware(['auth', 'signed'])->name('verification.verify');

// Resend verification email (for authenticated users)
Route::post('/email/verification-notification', function (Request $request) {
    try {
        $user = $request->user();
        
        // Log SMTP configuration (without sensitive data)
        Log::info('Attempting to send verification email', [
            'user_id' => $user->UserID,
            'email' => $user->Email,
            'mailer' => config('mail.default'),
            'mail_host' => config('mail.mailers.smtp.host'),
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_encryption' => config('mail.mailers.smtp.encryption'),
            'mail_from_address' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),
        ]);
        
        // Actually send the email and catch any errors
        try {
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                \Illuminate\Support\Carbon::now()->addMinutes(60),
                [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ]
            );
            
            Mail::send([], [], function ($message) use ($user, $verificationUrl) {
                $message->to($user->Email)
                    ->subject('Verify Your Email Address - Karahanyuze')
                    ->html(view('emails.verify', [
                        'user' => $user,
                        'verificationUrl' => $verificationUrl
                    ])->render());
            });
            
            Log::info('Email verification notification sent (authenticated)', [
                'user_id' => $user->UserID,
                'email' => $user->Email,
                'mailer' => config('mail.default'),
                'sent_synchronously' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Email send failed (authenticated)', [
                'user_id' => $user->UserID,
                'email' => $user->Email,
                'error' => $e->getMessage(),
                'class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['email' => 'Failed to send email: ' . $e->getMessage()]);
        }
        
        return back()->with('status', 'verification-link-sent');
    } catch (\Swift_TransportException | \Swift_RfcComplianceException $e) {
        Log::error('SMTP Transport error when sending verification email', [
            'user_id' => $request->user()->UserID ?? null,
            'email' => $request->user()->Email ?? null,
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTraceAsString()
        ]);
        return back()->withErrors(['email' => 'SMTP connection error: ' . $e->getMessage()]);
    } catch (\Exception $e) {
        Log::error('Failed to resend email verification (authenticated)', [
            'user_id' => $request->user()->UserID ?? null,
            'email' => $request->user()->Email ?? null,
            'error' => $e->getMessage(),
            'class' => get_class($e),
            'trace' => $e->getTraceAsString()
        ]);
        return back()->withErrors(['email' => 'Failed to send email: ' . $e->getMessage()]);
    }
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Resend verification email (for unauthenticated users, by email)
Route::post('/email/resend-verification', function (Request $request) {
    $request->validate([
        'email' => 'required|email|exists:Users,Email',
    ]);

    $user = \App\Models\User::where('Email', $request->email)->first();
    
    if ($user && !$user->hasVerifiedEmail()) {
        try {
            // Log SMTP configuration
            Log::info('Attempting to send verification email (unauthenticated)', [
                'user_id' => $user->UserID,
                'email' => $user->Email,
                'mailer' => config('mail.default'),
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port'),
                'mail_encryption' => config('mail.mailers.smtp.encryption'),
                'mail_from_address' => config('mail.from.address'),
            ]);
            
            // Actually send the email and catch any errors
            try {
                Mail::send([], [], function ($message) use ($user) {
                    $verificationUrl = URL::temporarySignedRoute(
                        'verification.verify',
                        \Illuminate\Support\Carbon::now()->addMinutes(60),
                        [
                            'id' => $user->getKey(),
                            'hash' => sha1($user->getEmailForVerification()),
                        ]
                    );
                    
                    $message->to($user->Email)
                        ->subject('Verify Your Email Address - Karahanyuze')
                        ->html(view('emails.verify', [
                            'user' => $user,
                            'verificationUrl' => $verificationUrl
                        ])->render());
                });
                
                Log::info('Email verification notification sent (unauthenticated)', [
                    'user_id' => $user->UserID,
                    'email' => $user->Email,
                    'mailer' => config('mail.default'),
                    'sent_synchronously' => true
                ]);
            } catch (\Exception $e) {
                Log::error('Email send failed (unauthenticated)', [
                    'user_id' => $user->UserID,
                    'email' => $user->Email,
                    'error' => $e->getMessage(),
                    'class' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
            
            return back()->with('status', 'verification-link-sent');
        } catch (\Swift_TransportException | \Swift_RfcComplianceException $e) {
            Log::error('SMTP Transport error when sending verification email', [
                'user_id' => $user->UserID,
                'email' => $user->Email,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['email' => 'SMTP connection error: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            Log::error('Failed to resend email verification', [
                'user_id' => $user->UserID,
                'email' => $user->Email,
                'error' => $e->getMessage(),
                'class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['email' => 'Failed to send email: ' . $e->getMessage()]);
        }
    }

    return back()->withErrors(['email' => 'This email is already verified or does not exist.']);
})->middleware('throttle:6,1')->name('verification.resend');

// User Dashboard Routes (authenticated and verified users only)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
});

// Favorites routes (authenticated users only - no verification required)
Route::middleware(['auth'])->group(function () {
    // Legacy route for songs (backward compatibility)
    Route::post('/favorites/{songId}/toggle', function ($songId) {
        return app(\App\Http\Controllers\UserDashboardController::class)->toggleFavorite(request(), 'song', $songId);
    })->name('favorites.toggle');
    // New polymorphic route for all favorite types
    Route::post('/favorites/{type}/{id}/toggle', [UserDashboardController::class, 'toggleFavorite'])->name('favorites.toggle.polymorphic');
});

Route::get('/artists', [ArtistController::class, 'index'])->name('artists.index');
// Old app used /umuhanzi for index, so support that too
Route::get('/umuhanzi', [ArtistController::class, 'index'])->name('umuhanzi.index');
// Support both new format (slug only) and old format (slug with UUID) for backward compatibility
Route::get('/artists/{slug}', [ArtistController::class, 'show'])->name('artists.show');
Route::get('/artists/{slug}/{uuid}', [ArtistController::class, 'show'])->name('artists.show.old');
// Old app used /umuhanzi for show page too
Route::get('/umuhanzi/{slug}/{uuid}', [ArtistController::class, 'show'])->name('umuhanzi.show');

Route::get('/orchestre', [OrchestraController::class, 'index'])->name('orchestre.index');
// Support both new format (slug only) and old format (slug with UUID) for backward compatibility
Route::get('/orchestre/{slug}', [OrchestraController::class, 'show'])->name('orchestre.show');
Route::get('/orchestre/{slug}/{uuid}', [OrchestraController::class, 'show'])->name('orchestre.show.old');

Route::get('/itorero', [ItoreroController::class, 'index'])->name('itorero.index');
// Support both new format (slug only) and old format (slug with UUID) for backward compatibility
Route::get('/itorero/{slug}', [ItoreroController::class, 'show'])->name('itorero.show');
Route::get('/itorero/{slug}/{uuid}', [ItoreroController::class, 'show'])->name('itorero.show.old');

Route::get('/playlists', [PlaylistController::class, 'index'])->name('playlists.index');
// Support both new format (plural) and old format (singular with UUID) for backward compatibility
Route::get('/playlists/{slug}', [PlaylistController::class, 'show'])->name('playlists.show');
Route::get('/playlist/{slug}/{uuid}', [PlaylistController::class, 'show'])->name('playlist.show');

Route::get('/indirimbo/search', [SongController::class, 'search'])->name('indirimbo.search');
// Use old format: /indirimbo/{slug}/{uuid} for backward compatibility with Google listings
Route::get('/indirimbo/{slug}/{uuid}', [SongController::class, 'show'])->name('indirimbo.show');

// Route to serve audio files (increments play count)
Route::get('/audio/{id}', function ($id) {
    $song = Song::findOrFail($id);
    
    if (empty($song->IndirimboUrl)) {
        abort(404, 'Song has no audio URL');
    }
    
    // Handle different path formats
    $url = $song->IndirimboUrl;
    
    // Remove leading slash if present
    $url = ltrim($url, '/');
    
    // Base directory for audio files
    $basePath = storage_path('app/Audios/');
    
    // Try different variations of the filename
    $possiblePaths = [];
    
    // 1. Direct match (with or without extension)
    $possiblePaths[] = $basePath . $url;
    
    // 2. If no extension, try with .mp3
    if (!pathinfo($url, PATHINFO_EXTENSION)) {
        $possiblePaths[] = $basePath . $url . '.mp3';
    }
    
    // 3. Just the filename (basename)
    $possiblePaths[] = $basePath . basename($url);
    
    // 4. Basename with .mp3 if no extension
    if (!pathinfo($url, PATHINFO_EXTENSION)) {
        $possiblePaths[] = $basePath . basename($url) . '.mp3';
    }
    
    // Find the first existing file
    $audioPath = null;
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $audioPath = $path;
            break;
        }
    }
    
    // If still not found, try to find by partial match (first 50 characters)
    if (!$audioPath && strlen($url) > 50) {
        $searchPattern = substr($url, 0, 50);
        $files = glob($basePath . '*');
        foreach ($files as $file) {
            if (strpos(basename($file), $searchPattern) !== false) {
                $audioPath = $file;
                break;
            }
        }
    }
    
    if (!$audioPath) {
        // Return a more helpful error
        Log::warning('Audio file not found', [
            'song_id' => $id,
            'song_name' => $song->IndirimboName,
            'indirimbo_url' => $url,
            'tried_paths' => array_slice($possiblePaths, 0, 4)
        ]);
        abort(404, 'Audio file not found for song: ' . $song->IndirimboName);
    }
    
    // Increment play count (only once per session to avoid multiple increments from seeking)
    $sessionKey = 'song_played_' . $song->IndirimboID;
    if (!session()->has($sessionKey)) {
        $song->increment('PlayCount');
        session()->put($sessionKey, true);
        // Store for 1 hour to prevent re-counting during the same listening session
        session()->put($sessionKey . '_expires', now()->addHour());
    } elseif (session()->has($sessionKey . '_expires') && now()->greaterThan(session()->get($sessionKey . '_expires'))) {
        // Reset after 1 hour to allow re-counting
        session()->forget([$sessionKey, $sessionKey . '_expires']);
        $song->increment('PlayCount');
        session()->put($sessionKey, true);
        session()->put($sessionKey . '_expires', now()->addHour());
    }
    
    return Response::file($audioPath, [
        'Content-Type' => 'audio/mpeg',
        'Cache-Control' => 'public, max-age=3600',
        'Accept-Ranges' => 'bytes',
    ]);
})->name('indirimbo.audio');

// Route to download audio files (increments download count)
Route::get('/download/{id}', function ($id) {
    $song = Song::findOrFail($id);
    
    if (empty($song->IndirimboUrl)) {
        abort(404, 'Song has no audio URL');
    }
    
    // Handle different path formats
    $url = $song->IndirimboUrl;
    
    // Remove leading slash if present
    $url = ltrim($url, '/');
    
    // Base directory for audio files
    $basePath = storage_path('app/Audios/');
    
    // Try different variations of the filename
    $possiblePaths = [];
    
    // 1. Direct match (with or without extension)
    $possiblePaths[] = $basePath . $url;
    
    // 2. If no extension, try with .mp3
    if (!pathinfo($url, PATHINFO_EXTENSION)) {
        $possiblePaths[] = $basePath . $url . '.mp3';
    }
    
    // 3. Just the filename (basename)
    $possiblePaths[] = $basePath . basename($url);
    
    // 4. Basename with .mp3 if no extension
    if (!pathinfo($url, PATHINFO_EXTENSION)) {
        $possiblePaths[] = $basePath . basename($url) . '.mp3';
    }
    
    // Find the first existing file
    $audioPath = null;
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $audioPath = $path;
            break;
        }
    }
    
    // If still not found, try to find by partial match (first 50 characters)
    if (!$audioPath && strlen($url) > 50) {
        $searchPattern = substr($url, 0, 50);
        $files = glob($basePath . '*');
        foreach ($files as $file) {
            if (strpos(basename($file), $searchPattern) !== false) {
                $audioPath = $file;
                break;
            }
        }
    }
    
    if (!$audioPath) {
        // Return a more helpful error
        Log::warning('Audio file not found for download', [
            'song_id' => $id,
            'song_name' => $song->IndirimboName,
            'indirimbo_url' => $url,
            'tried_paths' => array_slice($possiblePaths, 0, 4)
        ]);
        abort(404, 'Audio file not found for song: ' . $song->IndirimboName);
    }
    
    // Increment download count
    $song->increment('DownloadCount');
    
    // Generate filename with song title (spaces replaced by dashes)
    $filename = \Illuminate\Support\Str::slug($song->IndirimboName) . '.mp3';
    
    return Response::download($audioPath, $filename, [
        'Content-Type' => 'audio/mpeg',
    ]);
})->name('indirimbo.download');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    
    // Songs Management
    Route::get('songs', [AdminSongController::class, 'index'])->name('songs.index');
    Route::get('songs/create', [AdminSongController::class, 'create'])->name('songs.create');
    Route::post('songs', [AdminSongController::class, 'store'])->name('songs.store');
    Route::get('songs/{uuid}', [AdminSongController::class, 'show'])->name('songs.show');
    Route::get('songs/{uuid}/edit', [AdminSongController::class, 'edit'])->name('songs.edit');
    Route::put('songs/{uuid}', [AdminSongController::class, 'update'])->name('songs.update');
    Route::delete('songs/{uuid}', [AdminSongController::class, 'destroy'])->name('songs.destroy');
    
    // Artists Management (using 'abahanzi' for old app compatibility)
    Route::get('abahanzi', [AdminArtistController::class, 'index'])->name('artists.index');
    Route::get('abahanzi/create', [AdminArtistController::class, 'create'])->name('artists.create');
    Route::post('abahanzi', [AdminArtistController::class, 'store'])->name('artists.store');
    Route::get('abahanzi/{uuid}', [AdminArtistController::class, 'show'])->name('artists.show');
    Route::get('abahanzi/{uuid}/edit', [AdminArtistController::class, 'edit'])->name('artists.edit');
    Route::put('abahanzi/{uuid}', [AdminArtistController::class, 'update'])->name('artists.update');
    Route::delete('abahanzi/{uuid}', [AdminArtistController::class, 'destroy'])->name('artists.destroy');
    
    // Orchestras Management
    Route::get('orchestre', [AdminOrchestraController::class, 'index'])->name('orchestras.index');
    Route::get('orchestre/create', [AdminOrchestraController::class, 'create'])->name('orchestras.create');
    Route::post('orchestre', [AdminOrchestraController::class, 'store'])->name('orchestras.store');
    Route::get('orchestre/{uuid}', [AdminOrchestraController::class, 'show'])->name('orchestras.show');
    Route::get('orchestre/{uuid}/edit', [AdminOrchestraController::class, 'edit'])->name('orchestras.edit');
    Route::put('orchestre/{uuid}', [AdminOrchestraController::class, 'update'])->name('orchestras.update');
    Route::delete('orchestre/{uuid}', [AdminOrchestraController::class, 'destroy'])->name('orchestras.destroy');
    
    // Itoreros Management (using 'amatorero' for old app compatibility)
    Route::get('amatorero', [AdminItoreroController::class, 'index'])->name('itoreros.index');
    Route::get('amatorero/create', [AdminItoreroController::class, 'create'])->name('itoreros.create');
    Route::post('amatorero', [AdminItoreroController::class, 'store'])->name('itoreros.store');
    Route::get('amatorero/{uuid}', [AdminItoreroController::class, 'show'])->name('itoreros.show');
    Route::get('amatorero/{uuid}/edit', [AdminItoreroController::class, 'edit'])->name('itoreros.edit');
    Route::put('amatorero/{uuid}', [AdminItoreroController::class, 'update'])->name('itoreros.update');
    Route::delete('amatorero/{uuid}', [AdminItoreroController::class, 'destroy'])->name('itoreros.destroy');
    
    // Playlists Management
    Route::get('playlists', [AdminPlaylistController::class, 'index'])->name('playlists.index');
    Route::get('playlists/create', [AdminPlaylistController::class, 'create'])->name('playlists.create');
    Route::post('playlists', [AdminPlaylistController::class, 'store'])->name('playlists.store');
    Route::get('playlists/{uuid}', [AdminPlaylistController::class, 'show'])->name('playlists.show');
    Route::get('playlists/{uuid}/edit', [AdminPlaylistController::class, 'edit'])->name('playlists.edit');
    Route::put('playlists/{uuid}', [AdminPlaylistController::class, 'update'])->name('playlists.update');
    Route::delete('playlists/{uuid}', [AdminPlaylistController::class, 'destroy'])->name('playlists.destroy');
    
    // Users Management
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('users/{id}', [AdminUserController::class, 'show'])->name('users.show');
    Route::get('users/{id}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('users/{id}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});
