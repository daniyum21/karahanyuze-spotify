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
            // Try to find by ID first (for backward compatibility)
            $itorero = Itorero::find($slug);
            
            // If not found by ID, find by slug
            if (!$itorero) {
                // Try direct database query first
                $itorero = Itorero::where('slug', $slug)->first();
                
                // Fallback: try to find by matching slug in collection (more expensive)
                if (!$itorero) {
                    try {
                        $itorero = Itorero::all()->first(function($item) use ($slug) {
                            return isset($item->slug) && $item->slug === $slug;
                        });
                    } catch (\Exception $e) {
                        // If collection loading fails, just return null
                        \Log::warning('Error loading itorero collection for slug lookup', ['slug' => $slug, 'error' => $e->getMessage()]);
                    }
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

