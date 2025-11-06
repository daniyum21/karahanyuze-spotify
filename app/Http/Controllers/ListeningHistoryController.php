<?php

namespace App\Http\Controllers;

use App\Models\ListeningHistory;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListeningHistoryController extends Controller
{
    /**
     * Track a song play
     */
    public function track(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Not authenticated']);
        }

        $validated = $request->validate([
            'song_id' => 'required|integer|exists:Indirimbo,IndirimboID',
            'duration' => 'nullable|integer|min:0',
        ]);

        try {
            // Check if already played in last 5 minutes to avoid duplicates
            $recent = ListeningHistory::where('UserID', Auth::id())
                ->where('IndirimboID', $validated['song_id'])
                ->where('played_at', '>', now()->subMinutes(5))
                ->first();

            if (!$recent) {
                ListeningHistory::create([
                    'UserID' => Auth::id(),
                    'IndirimboID' => $validated['song_id'],
                    'played_at' => now(),
                    'play_duration' => $validated['duration'] ?? null,
                ]);

                // Increment song play count
                Song::where('IndirimboID', $validated['song_id'])
                    ->increment('PlayCount');
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error tracking listening history', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'song_id' => $validated['song_id'],
            ]);

            return response()->json(['success' => false, 'message' => 'Failed to track play']);
        }
    }

    /**
     * Get user's recently played songs
     */
    public function getRecentlyPlayed($limit = 20)
    {
        if (!Auth::check()) {
            return collect();
        }

        return ListeningHistory::where('UserID', Auth::id())
            ->with(['song.artist', 'song.orchestra', 'song.itorero'])
            ->orderBy('played_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($history) {
                return $history->song;
            })
            ->filter()
            ->unique('IndirimboID')
            ->values();
    }

    /**
     * Get personalized recommendations based on listening history
     */
    public function getRecommendations($limit = 20)
    {
        if (!Auth::check()) {
            return collect();
        }

        // Get user's most listened artists
        $topArtists = ListeningHistory::where('UserID', Auth::id())
            ->join('Indirimbo', 'listening_history.IndirimboID', '=', 'Indirimbo.IndirimboID')
            ->select('Indirimbo.UmuhanziID', 'Indirimbo.OrchestreID', 'Indirimbo.ItoreroID', DB::raw('COUNT(*) as play_count'))
            ->whereNotNull('Indirimbo.UmuhanziID')
            ->orWhereNotNull('Indirimbo.OrchestreID')
            ->orWhereNotNull('Indirimbo.ItoreroID')
            ->groupBy('Indirimbo.UmuhanziID', 'Indirimbo.OrchestreID', 'Indirimbo.ItoreroID')
            ->orderBy('play_count', 'desc')
            ->limit(5)
            ->get();

        $recommendations = collect();

        foreach ($topArtists as $artist) {
            if ($artist->UmuhanziID) {
                $songs = Song::where('UmuhanziID', $artist->UmuhanziID)
                    ->where('StatusID', 2)
                    ->with(['artist', 'orchestra', 'itorero'])
                    ->limit(5)
                    ->get();
                $recommendations = $recommendations->merge($songs);
            } elseif ($artist->OrchestreID) {
                $songs = Song::where('OrchestreID', $artist->OrchestreID)
                    ->where('StatusID', 2)
                    ->with(['artist', 'orchestra', 'itorero'])
                    ->limit(5)
                    ->get();
                $recommendations = $recommendations->merge($songs);
            } elseif ($artist->ItoreroID) {
                $songs = Song::where('ItoreroID', $artist->ItoreroID)
                    ->where('StatusID', 2)
                    ->with(['artist', 'orchestra', 'itorero'])
                    ->limit(5)
                    ->get();
                $recommendations = $recommendations->merge($songs);
            }
        }

        // If not enough recommendations, add popular songs
        if ($recommendations->count() < $limit) {
            $popularSongs = Song::where('StatusID', 2)
                ->with(['artist', 'orchestra', 'itorero'])
                ->orderBy('PlayCount', 'desc')
                ->limit($limit - $recommendations->count())
                ->get();
            $recommendations = $recommendations->merge($popularSongs);
        }

        return $recommendations->unique('IndirimboID')->take($limit)->values();
    }
}

