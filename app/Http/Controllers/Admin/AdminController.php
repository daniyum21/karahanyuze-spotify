<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Models\Artist;
use App\Models\Orchestra;
use App\Models\Itorero;
use App\Models\Playlist;
use App\Models\User;
use App\Models\SongStatus;
use App\Models\ForumThread;
use App\Models\ForumFlag;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get statistics
        $totalSongs = Song::count();
        $totalArtists = Artist::count();
        $totalOrchestras = Orchestra::count();
        $totalItoreros = Itorero::count();
        $totalPlaylists = Playlist::count();
        $totalUsers = User::count();
        
        // Find pending status by name (not hardcoded ID)
        $pendingStatus = SongStatus::where('StatusName', 'Pending')
            ->orWhere('StatusName', 'pending')
            ->orWhere('StatusName', 'Pending Approval')
            ->first();
        
        $approvedStatus = SongStatus::where('StatusName', 'Approved')
            ->orWhere('StatusName', 'approved')
            ->orWhere('StatusName', 'Public')
            ->orWhere('StatusName', 'public')
            ->first();
        
        $approvedSongs = $approvedStatus ? Song::where('StatusID', $approvedStatus->StatusID)->count() : Song::where('StatusID', 2)->count();
        $pendingSongs = $pendingStatus ? Song::where('StatusID', $pendingStatus->StatusID)->count() : Song::where('StatusID', 1)->count();
        $featuredSongs = Song::where('IsFeatured', 1)->count();

        // Get pending songs with user and owner info
        $pendingSongsList = $pendingStatus 
            ? Song::where('StatusID', $pendingStatus->StatusID)
                ->with(['user', 'artist', 'orchestra', 'itorero'])
                ->latest()
                ->get()
            : Song::where('StatusID', 1)
                ->with(['user', 'artist', 'orchestra', 'itorero'])
                ->latest()
                ->get();

        // Get recent songs
        $recentSongs = Song::with(['artist', 'orchestra', 'itorero', 'status'])
            ->latest()
            ->take(5)
            ->get();

        // Forum statistics
        $pendingThreads = ForumThread::where('is_approved', false)->count();
        $unresolvedFlags = ForumFlag::where('is_resolved', false)->count();

        return view('admin.dashboard', compact(
            'totalSongs',
            'totalArtists',
            'totalOrchestras',
            'totalItoreros',
            'totalPlaylists',
            'totalUsers',
            'approvedSongs',
            'pendingSongs',
            'pendingSongsList',
            'featuredSongs',
            'recentSongs',
            'pendingThreads',
            'unresolvedFlags'
        ));
    }
}

