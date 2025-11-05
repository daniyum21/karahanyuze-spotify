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
    public function index(Request $request)
    {
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validate sort direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        
        // Validate sort column
        $allowedSorts = ['OrchestreName', 'created_at', 'songs_count', 'IsFeatured'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        
        $query = Orchestra::withCount('songs');
        
        // Apply sorting
        if ($sortBy === 'songs_count') {
            $query->orderBy('songs_count', $sortDirection);
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }
        
        $orchestras = $query->paginate(20)->withQueryString();

        return view('admin.orchestras.index', compact('orchestras', 'sortBy', 'sortDirection'));
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

        // Redirect to nested song creation route: /admin/orchestre/{uuid}/songs
        return redirect()->route('admin.orchestras.songs.create', ['uuid' => $orchestra->UUID])
            ->with('success', 'Orchestra created successfully! Now add a song for this orchestra.');
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
        $orchestra = Orchestra::where('UUID', $uuid)
            ->with('songs')
            ->firstOrFail();
        
        $allSongs = Song::where('StatusID', 2)
            ->with('artist')
            ->latest()
            ->get();
        $orchestraSongIds = $orchestra->songs->pluck('IndirimboID')->toArray();

        return view('admin.orchestras.edit', compact('orchestra', 'allSongs', 'orchestraSongIds'));
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
            'songs' => 'nullable|array',
            'songs.*' => 'exists:Indirimbo,IndirimboID',
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

        // Handle song assignments
        if ($request->has('songs')) {
            $selectedSongIds = $validated['songs'];
            
            // Remove songs that are no longer selected (clear their orchestra association)
            Song::where('OrchestreID', $orchestra->OrchestreID)
                ->whereNotIn('IndirimboID', $selectedSongIds)
                ->update(['OrchestreID' => null]);
            
            // Add songs that are newly selected (clear other entity associations first)
            Song::whereIn('IndirimboID', $selectedSongIds)
                ->update([
                    'OrchestreID' => $orchestra->OrchestreID,
                    'UmuhanziID' => null,  // Clear artist association
                    'ItoreroID' => null    // Clear itorero association
                ]);
        } else {
            // If no songs selected, remove all song associations
            Song::where('OrchestreID', $orchestra->OrchestreID)
                ->update(['OrchestreID' => null]);
        }

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

