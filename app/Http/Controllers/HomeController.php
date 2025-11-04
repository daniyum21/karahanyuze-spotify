<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Artist;
use App\Models\Playlist;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        try {
            // Get recent songs (StatusID 2 = approved/public) - ordered by created_at desc like old app
            $recentSongs = Song::where('StatusID', 2)
                ->with(['artist', 'orchestra', 'itorero'])
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();

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

            return view('home', compact('recentSongs', 'featuredArtists', 'featuredPlaylists'));
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
                'featuredArtists' => collect(),
                'featuredPlaylists' => collect(),
                'error' => config('app.debug') ? $e->getMessage() : null
            ]);
        }
    }

    public function contactUs()
    {
        return view('contact');
    }
}
