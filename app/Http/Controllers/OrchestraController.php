<?php

namespace App\Http\Controllers;

use App\Models\Orchestra;
use Illuminate\Http\Request;

class OrchestraController extends Controller
{
    public function index()
    {
        $orchestras = Orchestra::withCount('songs')
            ->latest()
            ->paginate(24);

        return view('orchestras.index', compact('orchestras'));
    }

    public function show($slug, $uuid = null)
    {
        // If UUID is provided (old format: /orchestre/{slug}/{uuid})
        if ($uuid) {
            $orchestra = Orchestra::where('UUID', $uuid)
                ->with(['songs' => function($query) {
                    $query->where('Indirimbo.StatusID', 2) // Only public songs
                          ->with(['artist', 'itorero'])
                          ->orderBy('created_at', 'desc');
                }])
                ->first();
        } else {
            // Try to find by ID first (for backward compatibility)
            $orchestra = Orchestra::find($slug);
            
            // If not found by ID, find by slug
            if (!$orchestra) {
                $orchestra = Orchestra::all()->first(function($item) use ($slug) {
                    return $item->slug === $slug;
                });
            }
            
            if ($orchestra) {
                // Load songs with relationships
                $orchestra->load(['songs' => function($query) {
                    $query->where('Indirimbo.StatusID', 2) // Only public songs
                          ->with(['artist', 'itorero'])
                          ->orderBy('created_at', 'desc');
                }]);
            }
        }
        
        if (!$orchestra) {
            abort(404);
        }

        return view('orchestras.show', compact('orchestra'));
    }
}

