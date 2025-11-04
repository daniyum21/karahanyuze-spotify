<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    public function index()
    {
        $playlists = Playlist::with('songs')
            ->latest()
            ->paginate(12);

        return view('playlists.index', compact('playlists'));
    }

    public function show($slug, $uuid = null)
    {
        // If UUID is provided (old format: /playlist/{slug}/{uuid})
        if ($uuid) {
            $playlist = Playlist::where('UUID', $uuid)
                ->with(['songs' => function($query) {
                    $query->where('Indirimbo.StatusID', 2) // Only public songs
                          ->with(['artist', 'orchestra', 'itorero'])
                          ->orderBy('created_at', 'desc');
                }])
                ->first();
        } else {
            // Try to find by ID first (for backward compatibility)
            $playlist = Playlist::find($slug);
            
            // If not found by ID, find by slug
            if (!$playlist) {
                $playlist = Playlist::all()->first(function($item) use ($slug) {
                    return $item->slug === $slug;
                });
            }
            
            if ($playlist) {
                // Load songs with relationships
                $playlist->load(['songs' => function($query) {
                    $query->where('Indirimbo.StatusID', 2) // Only public songs
                          ->with(['artist', 'orchestra', 'itorero'])
                          ->orderBy('created_at', 'desc');
                }]);
            }
        }
        
        if (!$playlist) {
            abort(404);
        }

        return view('playlists.show', compact('playlist'));
    }
}
