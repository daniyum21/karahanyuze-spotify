<?php

namespace App\Http\Controllers;

use App\Models\Orchestra;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserOrchestraController extends Controller
{
    /**
     * Show the form for creating a new orchestra.
     */
    public function create()
    {
        return view('user.orchestras.create');
    }

    /**
     * Store a newly created orchestra.
     * After creation, redirect to song creation with this orchestra pre-selected.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'OrchestreName' => 'required|string|max:255',
            'Description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $orchestra = new Orchestra();
        $orchestra->OrchestreName = $validated['OrchestreName'];
        $orchestra->Description = $validated['Description'] ?? '';
        $orchestra->IsFeatured = 0; // Regular users can't set featured
        $orchestra->UUID = (string) Str::uuid();
        $orchestra->ProfilePicture = ''; // Set default empty string

        // Handle image file upload
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $extension = $imageFile->getClientOriginalExtension();
            $fileName = Str::random(100) . '_' . time() . '.' . $extension;
            $path = $imageFile->storeAs('Pictures', $fileName, 'local');
            
            $orchestra->ProfilePicture = 'Pictures/' . $fileName;
        }

        $orchestra->save();

        // Redirect to nested song creation route: /orchestre/{uuid}/songs
        return redirect()->route('orchestre.songs.create', ['uuid' => $orchestra->UUID])
            ->with('success', 'Orchestra created successfully! Now add a song for this orchestra.');
    }
}

