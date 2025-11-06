<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Artist;
use App\Models\Playlist;
use App\Models\ListeningHistory;
use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        try {
            // Get recently played songs for logged-in users, otherwise recent songs
            $recentSongs = collect();
            $recentlyPlayed = collect();
            $madeForYou = collect();

            if (Auth::check()) {
                // Get user's recently played songs
                $recentlyPlayed = ListeningHistory::where('UserID', Auth::id())
                    ->with(['song.artist', 'song.orchestra', 'song.itorero'])
                    ->whereHas('song', function($query) {
                        $query->where('StatusID', 2);
                    })
                    ->orderBy('played_at', 'desc')
                    ->take(20)
                    ->get()
                    ->map(function ($history) {
                        return $history->song;
                    })
                    ->filter()
                    ->unique('IndirimboID')
                    ->take(20)
                    ->values();

                // Get personalized recommendations
                $controller = new \App\Http\Controllers\ListeningHistoryController();
                $madeForYou = $controller->getRecommendations(20);
            }

            // If no recently played, get recent songs
            if ($recentlyPlayed->isEmpty()) {
                $recentSongs = Song::where('StatusID', 2)
                    ->with(['artist', 'orchestra', 'itorero'])
                    ->orderBy('created_at', 'desc')
                    ->take(20)
                    ->get();
            }

            // Get featured artists
            $featuredArtists = Artist::where('IsFeatured', 1)
                ->latest()
                ->take(4)
                ->get();

            // Get featured playlists
            $featuredPlaylists = Playlist::where('IsFeatured', 1)
                ->with('songs')
                ->latest()
                ->take(6)
                ->get();

            return view('home', compact('recentSongs', 'recentlyPlayed', 'madeForYou', 'featuredArtists', 'featuredPlaylists'));
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('HomeController error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Return empty collections so the page still loads
            return view('home', [
                'recentSongs' => collect(),
                'recentlyPlayed' => collect(),
                'madeForYou' => collect(),
                'featuredArtists' => collect(),
                'featuredPlaylists' => collect(),
                'error' => config('app.debug') ? $e->getMessage() : null
            ]);
        }
    }

    public function contactUs()
    {
        $user = auth()->user();
        $userName = null;
        $userEmail = null;
        
        if ($user) {
            $userEmail = $user->Email;
            // Try to get name from PublicName, or FirstName + LastName, or UserName
            if ($user->PublicName) {
                $userName = $user->PublicName;
            } elseif ($user->FirstName || $user->LastName) {
                $userName = trim(($user->FirstName ?? '') . ' ' . ($user->LastName ?? ''));
            } elseif ($user->UserName) {
                $userName = $user->UserName;
            }
        }
        
        return view('contact', compact('userName', 'userEmail'));
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        try {
            // Send email to info@karahanyuze.com with CC to daniyum21@gmail.com
            Mail::to('info@karahanyuze.com')
                ->cc('daniyum21@gmail.com')
                ->send(new ContactMail(
                    $validated['name'],
                    $validated['email'],
                    $validated['subject'] ?? 'Contact Form Submission from Karahanyuze',
                    $validated['message']
                ));

            return back()->with('success', 'Thank you for contacting us! We will get back to you soon.');
        } catch (\Exception $e) {
            Log::error('Contact form submission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $validated
            ]);

            return back()->with('error', 'Sorry, there was an error sending your message. Please try again or email us directly at info@karahanyuze.com.')
                ->withInput();
        }
    }
}
