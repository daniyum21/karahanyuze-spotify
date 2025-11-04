<?php

namespace App\Http\Controllers;

use App\Models\Itorero;
use Illuminate\Http\Request;

class ItoreroController extends Controller
{
    public function index()
    {
        $itoreros = Itorero::withCount('songs')
            ->latest()
            ->paginate(24);

        return view('itoreros.index', compact('itoreros'));
    }

    public function show($slug, $uuid = null)
    {
        // If UUID is provided (old format: /itorero/{slug}/{uuid})
        if ($uuid) {
            $itorero = Itorero::where('UUID', $uuid)
                ->with(['songs' => function($query) {
                    $query->where('Indirimbo.StatusID', 2) // Only public songs
                          ->with(['artist', 'orchestra'])
                          ->orderBy('created_at', 'desc');
                }])
                ->first();
        } else {
            // Try to find by ID first (for backward compatibility)
            $itorero = Itorero::find($slug);
            
            // If not found by ID, find by slug
            if (!$itorero) {
                $itorero = Itorero::all()->first(function($item) use ($slug) {
                    return $item->slug === $slug;
                });
            }
            
            if ($itorero) {
                // Load songs with relationships
                $itorero->load(['songs' => function($query) {
                    $query->where('Indirimbo.StatusID', 2) // Only public songs
                          ->with(['artist', 'orchestra'])
                          ->orderBy('created_at', 'desc');
                }]);
            }
        }
        
        if (!$itorero) {
            abort(404);
        }

        return view('itoreros.show', compact('itorero'));
    }
}

