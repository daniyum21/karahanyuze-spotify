<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;

class ArtistController extends Controller
{
    public function index()
    {
        // Match old app behavior: paginate 8 per page, no ordering
        // This ensures backward compatibility with old URLs like /umuhanzi?page=9
        // The old app used Umuhanzi::paginate(8) with no ordering or filtering
        $artists = Artist::withCount('songs')->paginate(8);

        return view('artists.index', compact('artists'));
    }

    public function show($slug, $uuid = null)
    {
        // If UUID is provided (old format: /artists/{slug}/{uuid} or /umuhanzi/{slug}/{uuid})
        if ($uuid) {
            $artist = Artist::where('UUID', $uuid)
                ->with(['songs' => function($query) {
                    $query->where('Indirimbo.StatusID', 2) // Only public songs
                          ->with(['orchestra', 'itorero'])
                          ->orderBy('created_at', 'desc');
                }])
                ->first();
        } else {
            // Try to find by ID first (for backward compatibility)
            $artist = Artist::find($slug);
            
            // If not found by ID, find by slug
            if (!$artist) {
                $artist = Artist::all()->first(function($item) use ($slug) {
                    return $item->slug === $slug;
                });
            }
            
            if ($artist) {
                // Load songs with relationships
                $artist->load(['songs' => function($query) {
                    $query->where('Indirimbo.StatusID', 2) // Only public songs
                          ->with(['orchestra', 'itorero'])
                          ->orderBy('created_at', 'desc');
                }]);
            }
        }
        
        if (!$artist) {
            abort(404);
        }

        return view('artists.show', compact('artist'));
    }
}

