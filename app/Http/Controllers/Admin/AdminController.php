<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Models\Artist;
use App\Models\Orchestra;
use App\Models\Itorero;
use App\Models\Playlist;
use App\Models\User;
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
        $approvedSongs = Song::where('StatusID', 2)->count();
        $pendingSongs = Song::where('StatusID', 1)->count();
        $featuredSongs = Song::where('IsFeatured', 1)->count();

        // Get recent songs
        $recentSongs = Song::with(['artist', 'orchestra', 'itorero', 'status'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalSongs',
            'totalArtists',
            'totalOrchestras',
            'totalItoreros',
            'totalPlaylists',
            'totalUsers',
            'approvedSongs',
            'pendingSongs',
            'featuredSongs',
            'recentSongs'
        ));
    }
}

