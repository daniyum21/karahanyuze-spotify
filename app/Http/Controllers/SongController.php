<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;

class SongController extends Controller
{
    public function show($slug, $uuid)
    {
        // Match old app behavior: find by UUID only (slug is just for URL structure)
        $song = Song::where('StatusID', 2)
            ->where('UUID', $uuid)
            ->with(['artist', 'orchestra', 'itorero', 'status', 'playlists'])
            ->first();

        if (!$song) {
            abort(404);
        }

        // Ensure relationships are loaded
        if (!$song->relationLoaded('artist')) {
            $song->load(['artist', 'orchestra', 'itorero', 'status', 'playlists']);
        }

        return view('songs.show', compact('song'));
    }

    public function search(Request $request)
    {
        $query = trim($request->get('q', ''));
        
        if (empty($query)) {
            return redirect()->route('home');
        }

        $searchTerm = "%{$query}%";
        
        // Search Songs
        $songs = Song::where('StatusID', 2)
            ->where(function($q) use ($searchTerm) {
                $q->where('IndirimboName', 'like', $searchTerm)
                  ->orWhere('Description', 'like', $searchTerm)
                  ->orWhere('Lyrics', 'like', $searchTerm);
            })
            ->with(['artist', 'orchestra', 'itorero'])
            ->latest()
            ->limit(20)
            ->get();

        // Search Artists
        $artists = \App\Models\Artist::where(function($q) use ($searchTerm) {
                $q->where('StageName', 'like', $searchTerm)
                  ->orWhere('FirstName', 'like', $searchTerm)
                  ->orWhere('LastName', 'like', $searchTerm)
                  ->orWhere('Description', 'like', $searchTerm);
            })
            ->latest()
            ->limit(10)
            ->get();

        // Search Orchestras
        $orchestras = \App\Models\Orchestra::where(function($q) use ($searchTerm) {
                $q->where('OrchestreName', 'like', $searchTerm)
                  ->orWhere('Description', 'like', $searchTerm);
            })
            ->latest()
            ->limit(10)
            ->get();

        // Search Itoreros
        $itoreros = \App\Models\Itorero::where(function($q) use ($searchTerm) {
                $q->where('ItoreroName', 'like', $searchTerm)
                  ->orWhere('Description', 'like', $searchTerm);
            })
            ->latest()
            ->limit(10)
            ->get();

        // Search Playlists
        $playlists = \App\Models\Playlist::where(function($q) use ($searchTerm) {
                $q->where('PlaylistName', 'like', $searchTerm)
                  ->orWhere('Description', 'like', $searchTerm);
            })
            ->latest()
            ->limit(10)
            ->get();

        $totalResults = $songs->count() + $artists->count() + $orchestras->count() + $itoreros->count() + $playlists->count();

        return view('indirimbo.search', compact('songs', 'artists', 'orchestras', 'itoreros', 'playlists', 'query', 'totalResults'));
    }
}
