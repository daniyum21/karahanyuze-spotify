<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Song;
use App\Models\Artist;
use App\Models\Orchestra;
use App\Models\Itorero;
use App\Models\Playlist;
use App\Models\SongStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        // Get user's favorite songs - use direct join instead of whereHas to avoid polymorphic column name issues
        $favoriteSongs = Song::where('Indirimbo.StatusID', 2)
            ->with(['artist', 'orchestra', 'itorero', 'status'])
            ->join('Favorites', function($join) use ($user) {
                $join->on('Indirimbo.IndirimboID', '=', 'Favorites.FavoriteID')
                     ->where('Favorites.FavoriteType', '=', 'Song')
                     ->where('Favorites.UserID', '=', $user->UserID);
            })
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

        // Get user's submitted content
        // Songs
        $mySongs = Song::where('UserID', $user->UserID)
            ->with(['status', 'artist', 'orchestra', 'itorero'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Artists (through songs created by user)
        $artistIds = Song::where('UserID', $user->UserID)
            ->whereNotNull('UmuhanziID')
            ->distinct()
            ->pluck('UmuhanziID');
        
        $myArtists = Artist::whereIn('UmuhanziID', $artistIds)
            ->with('songs')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Orchestras (through songs created by user)
        $orchestraIds = Song::where('UserID', $user->UserID)
            ->whereNotNull('OrchestreID')
            ->distinct()
            ->pluck('OrchestreID');
        
        $myOrchestras = Orchestra::whereIn('OrchestreID', $orchestraIds)
            ->with('songs')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Itoreros (through songs created by user)
        $itoreroIds = Song::where('UserID', $user->UserID)
            ->whereNotNull('ItoreroID')
            ->distinct()
            ->pluck('ItoreroID');
        
        $myItoreros = Itorero::whereIn('ItoreroID', $itoreroIds)
            ->with('songs')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get approved status for checking
        $approvedStatus = SongStatus::where('StatusName', 'Approved')
            ->orWhere('StatusName', 'approved')
            ->orWhere('StatusName', 'Public')
            ->orWhere('StatusName', 'public')
            ->first();

        return view('user.dashboard', compact(
            'favoriteSongs', 
            'favoriteArtists', 
            'favoriteOrchestras', 
            'favoriteItoreros', 
            'favoritePlaylists',
            'mySongs',
            'myArtists',
            'myOrchestras',
            'myItoreros',
            'approvedStatus'
        ));
    }

    /**
     * Toggle favorite status for any favoritable entity
     */
    public function toggleFavorite(Request $request, $type, $id)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Please log in to favorite items.'
                ], 401);
            }
            
            // Check if email is verified (admins don't need verification)
            if (!$user->hasVerifiedEmail() && !$user->isAdmin()) {
                return response()->json([
                    'error' => 'Email Not Verified',
                    'message' => 'Please verify your email address before favoriting items.',
                    'redirect' => route('verification.notice')
                ], 403);
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
            // First, check if it exists
            $favorite = Favorite::where('UserID', $user->UserID)
                ->where('FavoriteType', $favoriteType)
                ->where('FavoriteID', $id)
                ->first();

            if ($favorite) {
                // It already exists, so we're removing it (unlike)
                $favorite->delete();
                $isFavorited = false;
            } else {
                // It doesn't exist, so we're adding it (like)
                // Prepare data array
                $favoriteData = [
                    'UserID' => $user->UserID,
                    'FavoriteType' => $favoriteType,
                    'FavoriteID' => $id,
                ];
                
                // Only set IndirimboID for songs (backward compatibility)
                if ($type === 'song') {
                    $favoriteData['IndirimboID'] = $id;
                }
                // For non-songs, IndirimboID will be null (must be nullable in DB)
                
                // Use create with try-catch to handle any race conditions
                try {
                    $favorite = Favorite::create($favoriteData);
                    $isFavorited = true;
                } catch (\Illuminate\Database\QueryException $e) {
                    // If we get a duplicate entry error, it means another request already created it
                    // Check again to see the current state
                    $existingFavorite = Favorite::where('UserID', $user->UserID)
                        ->where('FavoriteType', $favoriteType)
                        ->where('FavoriteID', $id)
                        ->first();
                    
                    if ($existingFavorite) {
                        // It was created by another request, so delete it (toggle behavior)
                        $existingFavorite->delete();
                        $isFavorited = false;
                    } else {
                        // Some other error, re-throw
                        throw $e;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'isFavorited' => $isFavorited,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Favorite toggle: Entity not found', [
                'type' => $type,
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'error' => 'Item not found',
                'message' => 'The item you are trying to favorite does not exist.'
            ], 404);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Favorite toggle: Database error', [
                'type' => $type,
                'id' => $id,
                'error' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings()
            ]);
            return response()->json([
                'error' => 'Database error',
                'message' => 'An error occurred while saving your favorite. Please try again.'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Favorite toggle: Unexpected error', [
                'type' => $type,
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Server Error',
                'message' => 'An unexpected error occurred. Please try again.'
            ], 500);
        }
    }
}
