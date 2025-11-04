<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Song;
use App\Models\Artist;
use App\Models\Orchestra;
use App\Models\Itorero;
use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    /**
     * Display the user dashboard with all their favorites.
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Get user's favorite songs
        $favoriteSongs = Song::whereHas('favoritedBy', function($query) use ($user) {
                $query->where('Users.UserID', $user->UserID);
            })
            ->where('Indirimbo.StatusID', 2)
            ->with(['artist', 'orchestra', 'itorero', 'status'])
            ->join('Favorites', function($join) {
                $join->on('Indirimbo.IndirimboID', '=', 'Favorites.FavoriteID')
                     ->where('Favorites.FavoriteType', '=', 'Song');
            })
            ->where('Favorites.UserID', $user->UserID)
            ->select('Indirimbo.*', 'Favorites.created_at as favorited_at')
            ->orderBy('Favorites.created_at', 'desc')
            ->paginate(20);

        // Get user's favorite artists
        $favoriteArtists = Artist::join('Favorites', function($join) {
                $join->on('Abahanzi.UmuhanziID', '=', 'Favorites.FavoriteID')
                     ->where('Favorites.FavoriteType', '=', 'Artist');
            })
            ->where('Favorites.UserID', $user->UserID)
            ->select('Abahanzi.*', 'Favorites.created_at as favorited_at')
            ->with('songs')
            ->orderBy('Favorites.created_at', 'desc')
            ->get();

        // Get user's favorite orchestras
        $favoriteOrchestras = Orchestra::join('Favorites', function($join) {
                $join->on('Orchestres.OrchestreID', '=', 'Favorites.FavoriteID')
                     ->where('Favorites.FavoriteType', '=', 'Orchestra');
            })
            ->where('Favorites.UserID', $user->UserID)
            ->select('Orchestres.*', 'Favorites.created_at as favorited_at')
            ->with('songs')
            ->orderBy('Favorites.created_at', 'desc')
            ->get();

        // Get user's favorite itoreros
        $favoriteItoreros = Itorero::join('Favorites', function($join) {
                $join->on('Amatorero.ItoreroID', '=', 'Favorites.FavoriteID')
                     ->where('Favorites.FavoriteType', '=', 'Itorero');
            })
            ->where('Favorites.UserID', $user->UserID)
            ->select('Amatorero.*', 'Favorites.created_at as favorited_at')
            ->with('songs')
            ->orderBy('Favorites.created_at', 'desc')
            ->get();

        // Get user's favorite playlists
        $favoritePlaylists = Playlist::join('Favorites', function($join) {
                $join->on('Playlist.PlaylistID', '=', 'Favorites.FavoriteID')
                     ->where('Favorites.FavoriteType', '=', 'Playlist');
            })
            ->where('Favorites.UserID', $user->UserID)
            ->select('Playlist.*', 'Favorites.created_at as favorited_at')
            ->with('songs')
            ->orderBy('Favorites.created_at', 'desc')
            ->get();

        return view('user.dashboard', compact('favoriteSongs', 'favoriteArtists', 'favoriteOrchestras', 'favoriteItoreros', 'favoritePlaylists'));
    }

    /**
     * Toggle favorite status for any favoritable entity
     */
    public function toggleFavorite(Request $request, $type, $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validate type
        $allowedTypes = ['song', 'artist', 'orchestra', 'itorero', 'playlist'];
        if (!in_array(strtolower($type), $allowedTypes)) {
            return response()->json(['error' => 'Invalid favorite type'], 400);
        }

        // Map type to model class
        $typeMap = [
            'song' => ['Song', 'Song', 'IndirimboID'],
            'artist' => ['Artist', 'Artist', 'UmuhanziID'],
            'orchestra' => ['Orchestra', 'Orchestra', 'OrchestreID'],
            'itorero' => ['Itorero', 'Itorero', 'ItoreroID'],
            'playlist' => ['Playlist', 'Playlist', 'PlaylistID'],
        ];

        $modelClass = "App\\Models\\{$typeMap[$type][0]}";
        $favoriteType = $typeMap[$type][1];
        $primaryKey = $typeMap[$type][2];

        // Find the entity
        $entity = $modelClass::findOrFail($id);
        
        // Check if already favorited using polymorphic relationship
        $favorite = Favorite::where('UserID', $user->UserID)
            ->where('FavoriteType', $favoriteType)
            ->where('FavoriteID', $id)
            ->first();

        if ($favorite) {
            // Remove from favorites
            $favorite->delete();
            $isFavorited = false;
        } else {
            // Add to favorites using polymorphic relationship
            Favorite::create([
                'UserID' => $user->UserID,
                'FavoriteType' => $favoriteType,
                'FavoriteID' => $id,
                // Keep IndirimboID for backward compatibility with songs
                'IndirimboID' => ($type === 'song') ? $id : null,
            ]);
            $isFavorited = true;
        }

        return response()->json([
            'success' => true,
            'isFavorited' => $isFavorited,
        ]);
    }
}
