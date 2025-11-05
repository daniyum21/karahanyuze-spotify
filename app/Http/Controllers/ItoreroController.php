<?php

namespace App\Http\Controllers;

use App\Models\Itorero;
use Illuminate\Http\Request;

class ItoreroController extends Controller
{
    public function index()
    {
        try {
            $itoreros = Itorero::withCount('songs')
                ->orderBy('created_at', 'desc')
                ->orderBy('ItoreroID', 'desc')
                ->paginate(24);

            return view('itoreros.index', compact('itoreros'));
        } catch (\Exception $e) {
            \Log::error('Error loading itoreros index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return empty pagination result on error
            $itoreros = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 24, 1);
            return view('itoreros.index', compact('itoreros'))->withErrors(['error' => 'Unable to load itoreros. Please try again.']);
        }
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
            // Try to find by UUID first (in case slug is actually a UUID)
            $itorero = Itorero::where('UUID', $slug)->first();
            
            // If not found by UUID, try to find by ID (for backward compatibility)
            if (!$itorero && is_numeric($slug)) {
                $itorero = Itorero::find($slug);
            }
            
            // If still not found, find by matching slug (virtual attribute)
            if (!$itorero) {
                try {
                    // Load all itoreros and find by slug attribute
                    $itorero = Itorero::all()->first(function($item) use ($slug) {
                        return $item->slug === $slug;
                    });
                } catch (\Exception $e) {
                    // If collection loading fails, try finding by name (fuzzy match)
                    \Log::warning('Error loading itorero collection for slug lookup', ['slug' => $slug, 'error' => $e->getMessage()]);
                    
                    // Try to find by name that matches the slug
                    $nameFromSlug = str_replace('-', ' ', $slug);
                    $nameFromSlug = ucwords($nameFromSlug);
                    $itorero = Itorero::where('ItoreroName', 'LIKE', '%' . $nameFromSlug . '%')->first();
                }
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

