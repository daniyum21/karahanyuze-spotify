<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Itorero;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminItoreroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $itoreros = Itorero::withCount('songs')
            ->latest()
            ->paginate(20);

        return view('admin.itoreros.index', compact('itoreros'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.itoreros.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ItoreroName' => 'required|string|max:255',
            'Description' => 'nullable|string',
            'IsFeatured' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $itorero = new Itorero();
        $itorero->ItoreroName = $validated['ItoreroName'];
        $itorero->Description = $validated['Description'] ?? '';
        $itorero->IsFeatured = $validated['IsFeatured'] ?? false;
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

        return redirect()->route('admin.itoreros.index')
            ->with('success', 'Itorero has been created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $itorero = Itorero::where('UUID', $uuid)
            ->with('songs')
            ->firstOrFail();

        return view('admin.itoreros.show', compact('itorero'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $itorero = Itorero::where('UUID', $uuid)->firstOrFail();
        $songs = Song::where('ItoreroID', $itorero->ItoreroID)->paginate(20);

        return view('admin.itoreros.edit', compact('itorero', 'songs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $itorero = Itorero::where('UUID', $uuid)->firstOrFail();

        $validated = $request->validate([
            'ItoreroName' => 'required|string|max:255',
            'Description' => 'nullable|string',
            'IsFeatured' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $itorero->ItoreroName = $validated['ItoreroName'];
        $itorero->Description = $validated['Description'] ?? '';
        $itorero->IsFeatured = $validated['IsFeatured'] ?? false;

        // Handle image file upload
        if ($request->hasFile('image')) {
            // Delete old image file if exists
            if ($itorero->ProfilePicture) {
                $oldPath = storage_path('app/' . $itorero->ProfilePicture);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $imageFile = $request->file('image');
            $extension = $imageFile->getClientOriginalExtension();
            $fileName = Str::random(100) . '_' . time() . '.' . $extension;
            $path = $imageFile->storeAs('Pictures', $fileName, 'local');
            
            $itorero->ProfilePicture = 'Pictures/' . $fileName;
        }

        $itorero->save();

        return redirect()->route('admin.itoreros.index')
            ->with('success', 'Itorero has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $itorero = Itorero::where('UUID', $uuid)->firstOrFail();
        
        // Delete image file if exists
        if ($itorero->ProfilePicture) {
            $imagePath = storage_path('app/' . $itorero->ProfilePicture);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $itorero->delete();

        return redirect()->route('admin.itoreros.index')
            ->with('success', 'Itorero has been deleted successfully.');
    }
}

