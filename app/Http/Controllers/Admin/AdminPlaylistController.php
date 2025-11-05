<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPlaylistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validate sort direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        
        // Validate sort column
        $allowedSorts = ['PlaylistName', 'created_at', 'songs_count', 'IsFeatured'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        
        $query = Playlist::withCount('songs');
        
        // Apply sorting
        if ($sortBy === 'songs_count') {
            $query->orderBy('songs_count', $sortDirection);
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }
        
        $playlists = $query->paginate(20)->withQueryString();

        return view('admin.playlists.index', compact('playlists', 'sortBy', 'sortDirection'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $songs = Song::where('StatusID', 2)
            ->with('artist')
            ->latest()
            ->get();
        return view('admin.playlists.create', compact('songs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'PlaylistName' => 'required|string|max:255',
            'Description' => 'nullable|string',
            'IsFeatured' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'songs' => 'nullable|array',
            'songs.*' => 'exists:Indirimbo,IndirimboID',
        ]);

        $playlist = new Playlist();
        $playlist->PlaylistName = $validated['PlaylistName'];
        $playlist->Description = $validated['Description'] ?? '';
        $playlist->IsFeatured = $validated['IsFeatured'] ?? false;
        $playlist->UUID = (string) Str::uuid();
        $playlist->ProfilePicture = ''; // Set default empty string

        // Handle image file upload
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $extension = $imageFile->getClientOriginalExtension();
            $fileName = Str::random(100) . '_' . time() . '.' . $extension;
            $path = $imageFile->storeAs('Pictures', $fileName, 'local');
            
            $playlist->ProfilePicture = 'Pictures/' . $fileName;
        }

        $playlist->save();

        // Attach songs to playlist
        if ($request->has('songs')) {
            $playlist->songs()->attach($validated['songs']);
        }

        return redirect()->route('admin.playlists.index')
            ->with('success', 'Playlist has been created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $playlist = Playlist::where('UUID', $uuid)
            ->with('songs')
            ->firstOrFail();

        return view('admin.playlists.show', compact('playlist'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $playlist = Playlist::where('UUID', $uuid)
            ->with('songs')
            ->firstOrFail();
        
        $allSongs = Song::where('StatusID', 2)
            ->with('artist')
            ->latest()
            ->get();
        $playlistSongIds = $playlist->songs->pluck('IndirimboID')->toArray();

        return view('admin.playlists.edit', compact('playlist', 'allSongs', 'playlistSongIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $playlist = Playlist::where('UUID', $uuid)->firstOrFail();

        $validated = $request->validate([
            'PlaylistName' => 'required|string|max:255',
            'Description' => 'nullable|string',
            'IsFeatured' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'songs' => 'nullable|array',
            'songs.*' => 'exists:Indirimbo,IndirimboID',
        ]);

        $playlist->PlaylistName = $validated['PlaylistName'];
        $playlist->Description = $validated['Description'] ?? '';
        $playlist->IsFeatured = $validated['IsFeatured'] ?? false;

        // Handle image file upload
        if ($request->hasFile('image')) {
            // Delete old image file if exists
            if ($playlist->ProfilePicture) {
                $oldPath = storage_path('app/' . $playlist->ProfilePicture);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $imageFile = $request->file('image');
            $extension = $imageFile->getClientOriginalExtension();
            $fileName = Str::random(100) . '_' . time() . '.' . $extension;
            $path = $imageFile->storeAs('Pictures', $fileName, 'local');
            
            $playlist->ProfilePicture = 'Pictures/' . $fileName;
        }

        $playlist->save();

        // Sync songs to playlist
        if ($request->has('songs')) {
            $playlist->songs()->sync($validated['songs']);
        } else {
            $playlist->songs()->detach();
        }

        return redirect()->route('admin.playlists.index')
            ->with('success', 'Playlist has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $playlist = Playlist::where('UUID', $uuid)->firstOrFail();
        
        // Delete image file if exists
        if ($playlist->ProfilePicture) {
            $imagePath = storage_path('app/' . $playlist->ProfilePicture);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Detach all songs
        $playlist->songs()->detach();

        $playlist->delete();

        return redirect()->route('admin.playlists.index')
            ->with('success', 'Playlist has been deleted successfully.');
    }
}

