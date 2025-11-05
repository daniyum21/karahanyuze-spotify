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
    public function index(Request $request)
    {
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validate sort direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        
        // Validate sort column
        $allowedSorts = ['ItoreroName', 'created_at', 'songs_count', 'IsFeatured'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        
        $query = Itorero::withCount('songs');
        
        // Apply sorting
        if ($sortBy === 'songs_count') {
            $query->orderBy('songs_count', $sortDirection);
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }
        
        $itoreros = $query->paginate(20)->withQueryString();

        return view('admin.itoreros.index', compact('itoreros', 'sortBy', 'sortDirection'));
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

        // Redirect to nested song creation route: /admin/amatorero/{uuid}/songs
        return redirect()->route('admin.itoreros.songs.create', ['uuid' => $itorero->UUID])
            ->with('success', 'Itorero created successfully! Now add a song for this itorero.');
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
        $itorero = Itorero::where('UUID', $uuid)
            ->with('songs')
            ->firstOrFail();
        
        $allSongs = Song::where('StatusID', 2)
            ->with('artist')
            ->latest()
            ->get();
        $itoreroSongIds = $itorero->songs->pluck('IndirimboID')->toArray();

        return view('admin.itoreros.edit', compact('itorero', 'allSongs', 'itoreroSongIds'));
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
            'songs' => 'nullable|array',
            'songs.*' => 'exists:Indirimbo,IndirimboID',
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

        // Handle song assignments
        if ($request->has('songs')) {
            $selectedSongIds = $validated['songs'];
            
            // Remove songs that are no longer selected (clear their itorero association)
            Song::where('ItoreroID', $itorero->ItoreroID)
                ->whereNotIn('IndirimboID', $selectedSongIds)
                ->update(['ItoreroID' => null]);
            
            // Add songs that are newly selected (clear other entity associations first)
            Song::whereIn('IndirimboID', $selectedSongIds)
                ->update([
                    'ItoreroID' => $itorero->ItoreroID,
                    'UmuhanziID' => null,  // Clear artist association
                    'OrchestreID' => null  // Clear orchestra association
                ]);
        } else {
            // If no songs selected, remove all song associations
            Song::where('ItoreroID', $itorero->ItoreroID)
                ->update(['ItoreroID' => null]);
        }

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

