<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Orchestra;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminOrchestraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orchestras = Orchestra::withCount('songs')
            ->latest()
            ->paginate(20);

        return view('admin.orchestras.index', compact('orchestras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.orchestras.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'OrchestreName' => 'required|string|max:255',
            'Description' => 'nullable|string',
            'IsFeatured' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $orchestra = new Orchestra();
        $orchestra->OrchestreName = $validated['OrchestreName'];
        $orchestra->Description = $validated['Description'] ?? '';
        $orchestra->IsFeatured = $validated['IsFeatured'] ?? false;
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

        return redirect()->route('admin.orchestras.index')
            ->with('success', 'Orchestra has been created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $orchestra = Orchestra::where('UUID', $uuid)
            ->with('songs')
            ->firstOrFail();

        return view('admin.orchestras.show', compact('orchestra'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $orchestra = Orchestra::where('UUID', $uuid)->firstOrFail();
        $songs = Song::where('OrchestreID', $orchestra->OrchestreID)->paginate(20);

        return view('admin.orchestras.edit', compact('orchestra', 'songs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $orchestra = Orchestra::where('UUID', $uuid)->firstOrFail();

        $validated = $request->validate([
            'OrchestreName' => 'required|string|max:255',
            'Description' => 'nullable|string',
            'IsFeatured' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $orchestra->OrchestreName = $validated['OrchestreName'];
        $orchestra->Description = $validated['Description'] ?? '';
        $orchestra->IsFeatured = $validated['IsFeatured'] ?? false;

        // Handle image file upload
        if ($request->hasFile('image')) {
            // Delete old image file if exists
            if ($orchestra->ProfilePicture) {
                $oldPath = storage_path('app/' . $orchestra->ProfilePicture);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $imageFile = $request->file('image');
            $extension = $imageFile->getClientOriginalExtension();
            $fileName = Str::random(100) . '_' . time() . '.' . $extension;
            $path = $imageFile->storeAs('Pictures', $fileName, 'local');
            
            $orchestra->ProfilePicture = 'Pictures/' . $fileName;
        }

        $orchestra->save();

        return redirect()->route('admin.orchestras.index')
            ->with('success', 'Orchestra has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $orchestra = Orchestra::where('UUID', $uuid)->firstOrFail();
        
        // Delete image file if exists
        if ($orchestra->ProfilePicture) {
            $imagePath = storage_path('app/' . $orchestra->ProfilePicture);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $orchestra->delete();

        return redirect()->route('admin.orchestras.index')
            ->with('success', 'Orchestra has been deleted successfully.');
    }
}

