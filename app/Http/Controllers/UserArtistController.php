<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\SongStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserArtistController extends Controller
{
    /**
     * Show the form for creating a new artist.
     */
    public function create()
    {
        return view('user.artists.create');
    }

    /**
     * Store a newly created artist.
     * After creation, redirect to song creation with this artist pre-selected.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'FirstName' => 'nullable|string|max:255',
            'LastName' => 'nullable|string|max:255',
            'StageName' => 'required|string|max:255',
            'Email' => 'nullable|email|max:255',
            'Twitter' => 'nullable|string|max:255',
            'Description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $artist = new Artist();
        $artist->FirstName = $validated['FirstName'] ?? '';
        $artist->LastName = $validated['LastName'] ?? '';
        $artist->StageName = $validated['StageName'];
        $artist->Email = !empty($validated['Email']) ? $validated['Email'] : '';
        $artist->Twitter = !empty($validated['Twitter']) ? $validated['Twitter'] : '';
        $artist->Description = $validated['Description'] ?? '';
        $artist->IsFeatured = 0; // Regular users can't set featured
        $artist->UUID = (string) Str::uuid();
        $artist->ProfilePicture = ''; // Set default empty string

        // Handle image file upload
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $extension = $imageFile->getClientOriginalExtension();
            $fileName = Str::random(100) . '_' . time() . '.' . $extension;
            $path = $imageFile->storeAs('Pictures', $fileName, 'local');
            
            $artist->ProfilePicture = 'Pictures/' . $fileName;
        }

        $artist->save();

        // Redirect to nested song creation route: /artists/{uuid}/songs
        return redirect()->route('artists.songs.create', ['uuid' => $artist->UUID])
            ->with('success', 'Artist created successfully! Now add a song for this artist.');
    }
}

