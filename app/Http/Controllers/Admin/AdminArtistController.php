<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminArtistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $artists = Artist::withCount('songs')
            ->latest()
            ->paginate(20);

        return view('admin.artists.index', compact('artists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.artists.create');
    }

    /**
     * Store a newly created resource in storage.
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
            'IsFeatured' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $artist = new Artist();
        $artist->FirstName = $validated['FirstName'] ?? '';
        $artist->LastName = $validated['LastName'] ?? '';
        $artist->StageName = $validated['StageName'];
        $artist->Email = !empty($validated['Email']) ? $validated['Email'] : '';
        $artist->Twitter = !empty($validated['Twitter']) ? $validated['Twitter'] : '';
        $artist->Description = $validated['Description'] ?? '';
        $artist->IsFeatured = isset($validated['IsFeatured']) && $validated['IsFeatured'] ? 1 : 0;
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

        return redirect()->route('admin.artists.index')
            ->with('success', 'Artist has been created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $artist = Artist::where('UUID', $uuid)
            ->with('songs')
            ->firstOrFail();

        return view('admin.artists.show', compact('artist'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $artist = Artist::where('UUID', $uuid)->firstOrFail();
        $songs = Song::where('UmuhanziID', $artist->UmuhanziID)->paginate(20);

        return view('admin.artists.edit', compact('artist', 'songs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $artist = Artist::where('UUID', $uuid)->firstOrFail();

        $validated = $request->validate([
            'FirstName' => 'nullable|string|max:255',
            'LastName' => 'nullable|string|max:255',
            'StageName' => 'required|string|max:255',
            'Email' => 'nullable|email|max:255',
            'Twitter' => 'nullable|string|max:255',
            'Description' => 'nullable|string',
            'IsFeatured' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $artist->FirstName = $validated['FirstName'] ?? '';
        $artist->LastName = $validated['LastName'] ?? '';
        $artist->StageName = $validated['StageName'];
        $artist->Email = !empty($validated['Email']) ? $validated['Email'] : '';
        $artist->Twitter = !empty($validated['Twitter']) ? $validated['Twitter'] : '';
        $artist->Description = $validated['Description'] ?? '';
        $artist->IsFeatured = isset($validated['IsFeatured']) && $validated['IsFeatured'] ? 1 : 0;

        // Handle image file upload
        if ($request->hasFile('image')) {
            // Delete old image file if exists
            if ($artist->ProfilePicture) {
                $oldPath = storage_path('app/' . $artist->ProfilePicture);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $imageFile = $request->file('image');
            $extension = $imageFile->getClientOriginalExtension();
            $fileName = Str::random(100) . '_' . time() . '.' . $extension;
            $path = $imageFile->storeAs('Pictures', $fileName, 'local');
            
            $artist->ProfilePicture = 'Pictures/' . $fileName;
        }

        $artist->save();

        return redirect()->route('admin.artists.index')
            ->with('success', 'Artist has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $artist = Artist::where('UUID', $uuid)->firstOrFail();
        
        // Delete image file if exists
        if ($artist->ProfilePicture) {
            $imagePath = storage_path('app/' . $artist->ProfilePicture);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $artist->delete();

        return redirect()->route('admin.artists.index')
            ->with('success', 'Artist has been deleted successfully.');
    }
}

