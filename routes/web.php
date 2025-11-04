<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
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

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('user.dashboard')->with('success', 'Email verified successfully!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// User Dashboard Routes (authenticated and verified users)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::post('/favorites/{songId}/toggle', [UserDashboardController::class, 'toggleFavorite'])->name('favorites.toggle');
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

// Route to serve audio files
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
    
    return Response::file($audioPath, [
        'Content-Type' => 'audio/mpeg',
        'Cache-Control' => 'public, max-age=3600',
        'Accept-Ranges' => 'bytes',
    ]);
})->name('indirimbo.audio');

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
