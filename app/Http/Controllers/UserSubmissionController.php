<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Artist;
use App\Models\Orchestra;
use App\Models\Itorero;
use App\Models\SongStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSubmissionController extends Controller
{
    /**
     * Display user's submitted content
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Get user's submitted songs
        $songs = Song::where('UserID', $userId)
            ->with(['status', 'artist', 'orchestra', 'itorero'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Get user's submitted artists
        // Note: Artists don't have UserID, so we need to track this differently
        // For now, we'll get artists that have songs submitted by this user
        $artistIds = Song::where('UserID', $userId)
            ->whereNotNull('UmuhanziID')
            ->distinct()
            ->pluck('UmuhanziID');
        
        $artists = Artist::whereIn('UmuhanziID', $artistIds)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get user's submitted orchestras
        $orchestraIds = Song::where('UserID', $userId)
            ->whereNotNull('OrchestreID')
            ->distinct()
            ->pluck('OrchestreID');
        
        $orchestras = Orchestra::whereIn('OrchestreID', $orchestraIds)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get user's submitted itoreros
        $itoreroIds = Song::where('UserID', $userId)
            ->whereNotNull('ItoreroID')
            ->distinct()
            ->pluck('ItoreroID');
        
        $itoreros = Itorero::whereIn('ItoreroID', $itoreroIds)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get approved status for checking
        $approvedStatus = SongStatus::where('StatusName', 'Approved')
            ->orWhere('StatusName', 'approved')
            ->orWhere('StatusName', 'Public')
            ->orWhere('StatusName', 'public')
            ->first();
        
        return view('user.submissions.index', compact(
            'songs', 
            'artists', 
            'orchestras', 
            'itoreros',
            'approvedStatus'
        ));
    }
}

