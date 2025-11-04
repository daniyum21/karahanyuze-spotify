<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Models\Artist;
use App\Models\Orchestra;
use App\Models\Itorero;
use App\Models\SongStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminSongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $songs = Song::with(['artist', 'orchestra', 'itorero', 'status'])
            ->latest()
            ->paginate(20);

        return view('admin.songs.index', compact('songs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $artists = Artist::orderBy('StageName', 'asc')->get();
        $orchestras = Orchestra::orderBy('OrchestreName', 'asc')->get();
        $itoreros = Itorero::orderBy('ItoreroName', 'asc')->get();
        $statuses = SongStatus::orderBy('StatusName', 'asc')->get();

        return view('admin.songs.create', compact('artists', 'orchestras', 'itoreros', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'IndirimboName' => 'required|string|max:255',
            'Description' => 'nullable|string',
            'Lyrics' => 'nullable|string',
            'StatusID' => 'required|exists:IndirimboStatus,StatusID',
            'UmuhanziID' => 'nullable|exists:Abahanzi,UmuhanziID',
            'OrchestreID' => 'nullable|exists:Orchestres,OrchestreID',
            'ItoreroID' => 'nullable|exists:Amatorero,ItoreroID',
            'IsFeatured' => 'boolean',
            'audio' => 'nullable|file|mimes:mp3|max:51200', // 50MB max
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $song = new Song();
        $song->IndirimboName = $validated['IndirimboName'];
        $song->Description = $validated['Description'] ?? '';
        $song->Lyrics = $validated['Lyrics'] ?? '';
        $song->StatusID = $validated['StatusID'];
        $song->IsFeatured = $validated['IsFeatured'] ?? false;
        $song->UUID = (string) Str::uuid();

        // Set owner (artist, orchestra, or itorero)
        if (!empty($validated['UmuhanziID'])) {
            $song->UmuhanziID = $validated['UmuhanziID'];
        } elseif (!empty($validated['OrchestreID'])) {
            $song->OrchestreID = $validated['OrchestreID'];
        } elseif (!empty($validated['ItoreroID'])) {
            $song->ItoreroID = $validated['ItoreroID'];
        }

        // Handle audio file upload
        if ($request->hasFile('audio')) {
            $audioFile = $request->file('audio');
            $extension = $audioFile->getClientOriginalExtension();
            $fileName = Str::random(100) . '_' . time() . '.' . $extension;
            $path = $audioFile->storeAs('Audios', $fileName, 'local');
            
            // Store relative path (without storage/app prefix)
            $song->IndirimboUrl = 'Audios/' . $fileName;
        }

        // Handle image file upload
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $extension = $imageFile->getClientOriginalExtension();
            $fileName = Str::random(100) . '_' . time() . '.' . $extension;
            $path = $imageFile->storeAs('Pictures', $fileName, 'local');
            
            // Store relative path (without storage/app prefix)
            $song->ProfilePicture = 'Pictures/' . $fileName;
        }

        $song->save();

        return redirect()->route('admin.songs.index')
            ->with('success', 'Song has been created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $song = Song::where('UUID', $uuid)
            ->with(['artist', 'orchestra', 'itorero', 'status', 'playlists'])
            ->firstOrFail();

        return view('admin.songs.show', compact('song'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $song = Song::where('UUID', $uuid)->firstOrFail();
        
        $artists = Artist::orderBy('StageName', 'asc')->get();
        $orchestras = Orchestra::orderBy('OrchestreName', 'asc')->get();
        $itoreros = Itorero::orderBy('ItoreroName', 'asc')->get();
        $statuses = SongStatus::orderBy('StatusName', 'asc')->get();

        return view('admin.songs.edit', compact('song', 'artists', 'orchestras', 'itoreros', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $song = Song::where('UUID', $uuid)->firstOrFail();

        $validated = $request->validate([
            'IndirimboName' => 'required|string|max:255',
            'Description' => 'nullable|string',
            'Lyrics' => 'nullable|string',
            'StatusID' => 'required|exists:IndirimboStatus,StatusID',
            'UmuhanziID' => 'nullable|exists:Abahanzi,UmuhanziID',
            'OrchestreID' => 'nullable|exists:Orchestres,OrchestreID',
            'ItoreroID' => 'nullable|exists:Amatorero,ItoreroID',
            'IsFeatured' => 'boolean',
            'audio' => 'nullable|file|mimes:mp3|max:51200', // 50MB max
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $song->IndirimboName = $validated['IndirimboName'];
        $song->Description = $validated['Description'] ?? '';
        $song->Lyrics = $validated['Lyrics'] ?? '';
        $song->StatusID = $validated['StatusID'];
        $song->IsFeatured = $validated['IsFeatured'] ?? false;

        // Clear previous owner associations
        $song->UmuhanziID = null;
        $song->OrchestreID = null;
        $song->ItoreroID = null;

        // Set owner (artist, orchestra, or itorero)
        if (!empty($validated['UmuhanziID'])) {
            $song->UmuhanziID = $validated['UmuhanziID'];
        } elseif (!empty($validated['OrchestreID'])) {
            $song->OrchestreID = $validated['OrchestreID'];
        } elseif (!empty($validated['ItoreroID'])) {
            $song->ItoreroID = $validated['ItoreroID'];
        }

        // Handle audio file upload
        if ($request->hasFile('audio')) {
            // Delete old audio file if exists
            if ($song->IndirimboUrl) {
                $oldPath = storage_path('app/' . $song->IndirimboUrl);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $audioFile = $request->file('audio');
            $extension = $audioFile->getClientOriginalExtension();
            $fileName = Str::random(100) . '_' . time() . '.' . $extension;
            $path = $audioFile->storeAs('Audios', $fileName, 'local');
            
            $song->IndirimboUrl = 'Audios/' . $fileName;
        }

        // Handle image file upload
        if ($request->hasFile('image')) {
            // Delete old image file if exists
            if ($song->ProfilePicture) {
                $oldPath = storage_path('app/' . $song->ProfilePicture);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $imageFile = $request->file('image');
            $extension = $imageFile->getClientOriginalExtension();
            $fileName = Str::random(100) . '_' . time() . '.' . $extension;
            $path = $imageFile->storeAs('Pictures', $fileName, 'local');
            
            $song->ProfilePicture = 'Pictures/' . $fileName;
        }

        $song->save();

        return redirect()->route('admin.songs.index')
            ->with('success', 'Song has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $song = Song::where('UUID', $uuid)->firstOrFail();
        
        // Delete audio file if exists
        if ($song->IndirimboUrl) {
            $audioPath = storage_path('app/' . $song->IndirimboUrl);
            if (file_exists($audioPath)) {
                unlink($audioPath);
            }
        }

        // Delete image file if exists
        if ($song->ProfilePicture) {
            $imagePath = storage_path('app/' . $song->ProfilePicture);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $song->delete();

        return redirect()->route('admin.songs.index')
            ->with('success', 'Song has been deleted successfully.');
    }
}

