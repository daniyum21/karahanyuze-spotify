<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    /**
     * Display the user dashboard with their favorite songs.
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Get user's favorite songs via the Favorites pivot table
        $favoriteSongs = Song::whereHas('favoritedBy', function($query) use ($user) {
                $query->where('Users.UserID', $user->UserID);
            })
            ->where('Indirimbo.StatusID', 2)
            ->with(['artist', 'orchestra', 'itorero', 'status'])
            ->join('Favorites', 'Indirimbo.IndirimboID', '=', 'Favorites.IndirimboID')
            ->where('Favorites.UserID', $user->UserID)
            ->select('Indirimbo.*', 'Favorites.created_at as favorited_at')
            ->orderBy('Favorites.created_at', 'desc')
            ->paginate(20);

        return view('user.dashboard', compact('favoriteSongs'));
    }

    /**
     * Toggle favorite status for a song
     */
    public function toggleFavorite(Request $request, $songId)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $song = Song::findOrFail($songId);
        
        // Check if already favorited
        $favorite = Favorite::where('UserID', $user->UserID)
            ->where('IndirimboID', $songId)
            ->first();

        if ($favorite) {
            // Remove from favorites
            $favorite->delete();
            $isFavorited = false;
        } else {
            // Add to favorites
            Favorite::create([
                'UserID' => $user->UserID,
                'IndirimboID' => $songId,
            ]);
            $isFavorited = true;
        }

        return response()->json([
            'success' => true,
            'isFavorited' => $isFavorited,
        ]);
    }
}
