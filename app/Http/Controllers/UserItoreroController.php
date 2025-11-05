<?php

namespace App\Http\Controllers;

use App\Models\Itorero;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserItoreroController extends Controller
{
    /**
     * Show the form for creating a new itorero.
     */
    public function create()
    {
        return view('user.itoreros.create');
    }

    /**
     * Store a newly created itorero.
     * After creation, redirect to song creation with this itorero pre-selected.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ItoreroName' => 'required|string|max:255',
            'Description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $itorero = new Itorero();
        $itorero->ItoreroName = $validated['ItoreroName'];
        $itorero->Description = $validated['Description'] ?? '';
        $itorero->IsFeatured = 0; // Regular users can't set featured
        $itorero->UUID = (string) Str::uuid();
        $itorero->ProfilePicture = ''; // Set default empty string

        // Handle image file upload
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $extension = $imageFile->getClientOriginalExtension();
            $fileName = Str::random(100) . '_' . time() . '.' . $extension;
            $path = $imageFile->storeAs('Pictures', $fileName, 'local');
            
            $itorero->ProfilePicture = 'Pictures/' . $fileName;
        }

        $itorero->save();

        // Redirect to nested song creation route: /amatorero/{uuid}/songs
        return redirect()->route('amatorero.songs.create', ['uuid' => $itorero->UUID])
            ->with('success', 'Itorero created successfully! Now add a song for this itorero.');
    }
}

