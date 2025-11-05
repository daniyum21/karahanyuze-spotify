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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminSongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Song::with(['artist', 'orchestra', 'itorero', 'status']);

        // Load user relationship for pending songs
        if ($request->has('status') && $request->status === 'pending') {
            $query->with('user');
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'pending') {
                $pendingStatus = SongStatus::where('StatusName', 'Pending')
                    ->orWhere('StatusName', 'pending')
                    ->orWhere('StatusName', 'Pending Approval')
                    ->first();
                
                if ($pendingStatus) {
                    $query->where('StatusID', $pendingStatus->StatusID);
                } else {
                    $query->where('StatusID', 1); // Fallback to ID 1
                }
            } elseif ($request->status === 'approved') {
                $approvedStatus = SongStatus::where('StatusName', 'Approved')
                    ->orWhere('StatusName', 'approved')
                    ->orWhere('StatusName', 'Public')
                    ->orWhere('StatusName', 'public')
                    ->first();
                
                if ($approvedStatus) {
                    $query->where('StatusID', $approvedStatus->StatusID);
                } else {
                    $query->where('StatusID', 2); // Fallback to ID 2
                }
            }
        }

        // Filter by featured
        if ($request->has('featured')) {
            if ($request->featured === 'yes') {
                $query->where('IsFeatured', 1);
            } elseif ($request->featured === 'no') {
                $query->where('IsFeatured', 0);
            }
        }

        $songs = $query->latest()->paginate(20)->withQueryString();

        $filterStatus = $request->get('status', 'all');
        $filterFeatured = $request->get('featured', 'all');

        return view('admin.songs.index', compact('songs', 'filterStatus', 'filterFeatured'));
    }

    /**
     * Show the form for creating a new resource.
     * Accepts optional query parameters: artist, orchestra, or itorero UUID to pre-select.
     */
    public function create(Request $request)
    {
        $artists = Artist::orderBy('StageName', 'asc')->get();
        $orchestras = Orchestra::orderBy('OrchestreName', 'asc')->get();
        $itoreros = Itorero::orderBy('ItoreroName', 'asc')->get();
        $statuses = SongStatus::orderBy('StatusName', 'asc')->get();
        
        // Pre-select entity if provided
        $selectedArtist = null;
        $selectedOrchestra = null;
        $selectedItorero = null;
        
        if ($request->has('artist')) {
            $selectedArtist = Artist::where('UUID', $request->artist)->first();
        } elseif ($request->has('orchestra')) {
            $selectedOrchestra = Orchestra::where('UUID', $request->orchestra)->first();
        } elseif ($request->has('itorero')) {
            $selectedItorero = Itorero::where('UUID', $request->itorero)->first();
        }

        return view('admin.songs.create', compact('artists', 'orchestras', 'itoreros', 'statuses', 'selectedArtist', 'selectedOrchestra', 'selectedItorero'));
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

        // If an entity was pre-selected, redirect back to song creation with that entity preserved
        // This allows admins to quickly add multiple songs for the same artist/orchestra/itorero
        $redirectRoute = route('admin.songs.index');
        if (!empty($validated['UmuhanziID'])) {
            $artist = Artist::find($validated['UmuhanziID']);
            if ($artist) {
                $redirectRoute = route('admin.songs.create', ['artist' => $artist->UUID]);
            }
        } elseif (!empty($validated['OrchestreID'])) {
            $orchestra = Orchestra::find($validated['OrchestreID']);
            if ($orchestra) {
                $redirectRoute = route('admin.songs.create', ['orchestra' => $orchestra->UUID]);
            }
        } elseif (!empty($validated['ItoreroID'])) {
            $itorero = Itorero::find($validated['ItoreroID']);
            if ($itorero) {
                $redirectRoute = route('admin.songs.create', ['itorero' => $itorero->UUID]);
            }
        }

        return redirect($redirectRoute)
            ->with('success', 'Song has been created successfully.' . ($redirectRoute !== route('admin.songs.index') ? ' You can add another song for this ' . (!empty($validated['UmuhanziID']) ? 'artist' : (!empty($validated['OrchestreID']) ? 'orchestra' : 'itorero')) . ' or go back to the songs list.' : ''));
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
     * Toggle featured status of a song
     */
    public function toggleFeatured($uuid)
    {
        $song = Song::where('UUID', $uuid)->firstOrFail();
        $song->IsFeatured = !$song->IsFeatured;
        $song->save();

        $status = $song->IsFeatured ? 'featured' : 'unfeatured';
        return back()->with('success', "Song {$status} successfully!");
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

        // Handle owner associations FIRST (before setting other model attributes)
        // This prevents null values from being included in the final save()
        $umuhanziID = !empty($validated['UmuhanziID']) ? (int)$validated['UmuhanziID'] : null;
        $orchestreID = !empty($validated['OrchestreID']) ? (int)$validated['OrchestreID'] : null;
        $itoreroID = !empty($validated['ItoreroID']) ? (int)$validated['ItoreroID'] : null;

        // Only update owner associations if at least one is explicitly provided
        if ($umuhanziID !== null || $orchestreID !== null || $itoreroID !== null) {
            // Store original values for comparison
            $originalUmuhanziID = $song->UmuhanziID;
            $originalOrchestreID = $song->OrchestreID;
            $originalItoreroID = $song->ItoreroID;
            
            // Prepare updates - separate null and non-null
            $nonNullUpdates = [];
            $nullUpdates = [];
            
            // Clear all first, then set the new one to ensure a song only belongs to one entity
            if ($umuhanziID !== null) {
                // Assigning to artist - clear orchestra and itorero
                $nonNullUpdates['UmuhanziID'] = $umuhanziID;
                // Only clear if they were set before
                if ($originalOrchestreID !== null) {
                    $nullUpdates['OrchestreID'] = null;
                }
                if ($originalItoreroID !== null) {
                    $nullUpdates['ItoreroID'] = null;
                }
            } elseif ($orchestreID !== null) {
                // Assigning to orchestra - clear artist and itorero
                if ($originalUmuhanziID !== null) {
                    $nullUpdates['UmuhanziID'] = null;
                }
                $nonNullUpdates['OrchestreID'] = $orchestreID;
                if ($originalItoreroID !== null) {
                    $nullUpdates['ItoreroID'] = null;
                }
            } elseif ($itoreroID !== null) {
                // Assigning to itorero - clear artist and orchestra
                if ($originalUmuhanziID !== null) {
                    $nullUpdates['UmuhanziID'] = null;
                }
                if ($originalOrchestreID !== null) {
                    $nullUpdates['OrchestreID'] = null;
                }
                $nonNullUpdates['ItoreroID'] = $itoreroID;
            }
            
            // Update non-null owner fields first
            if (!empty($nonNullUpdates)) {
                DB::table('Indirimbo')
                    ->where('IndirimboID', $song->IndirimboID)
                    ->update($nonNullUpdates);
            }
            
            // For null fields, try to update them individually with error handling
            // Skip if database doesn't allow null
            foreach ($nullUpdates as $field => $value) {
                try {
                    DB::table('Indirimbo')
                        ->where('IndirimboID', $song->IndirimboID)
                        ->update([$field => null]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // If setting to null fails, just skip it - field stays as is
                    continue;
                }
            }
            
            // Refresh model to get updated values
            $song->refresh();
        }
        
        // Now set other model attributes (these will be saved later)
        $song->IndirimboName = $validated['IndirimboName'];
        $song->Description = $validated['Description'] ?? '';
        $song->Lyrics = $validated['Lyrics'] ?? '';
        $song->StatusID = $validated['StatusID'];
        $song->IsFeatured = $validated['IsFeatured'] ?? false;

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

        // Check if request came from a filtered view (pending, approved, or featured)
        // Preserve the filter when redirecting back
        $redirectParams = [];
        if ($request->has('status')) {
            $redirectParams['status'] = $request->get('status');
        } elseif ($request->has('featured')) {
            $redirectParams['featured'] = $request->get('featured');
        }
        
        // If no filter in request, check if the song was pending before update
        // If it was pending and is now approved, redirect to pending list to see if there are more
        if (empty($redirectParams)) {
            $originalStatus = $song->getOriginal('StatusID');
            $pendingStatus = SongStatus::where('StatusName', 'Pending')
                ->orWhere('StatusName', 'pending')
                ->orWhere('StatusName', 'Pending Approval')
                ->first();
            
            if ($pendingStatus && $originalStatus == $pendingStatus->StatusID) {
                // Song was pending, redirect to pending list
                $redirectParams['status'] = 'pending';
            }
        }
        
        return redirect()->route('admin.songs.index', $redirectParams)
            ->with('success', 'Song has been updated successfully.');
    }

    /**
     * Decline a song with a reason (does not delete it)
     */
    public function decline(Request $request, $uuid)
    {
        $validated = $request->validate([
            'declined_reason' => 'required|string|max:1000',
        ]);

        $song = Song::where('UUID', $uuid)->firstOrFail();
        
        $song->declined_reason = $validated['declined_reason'];
        $song->declined_at = now();
        $song->declined_by = Auth::id();
        $song->save();

        return back()->with('success', 'Song declined successfully. The user will be notified of the reason.');
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

    /**
     * Show the form for creating a song for a specific artist.
     * Route: /admin/abahanzi/{uuid}/songs
     */
    public function createForArtist($uuid)
    {
        $artist = Artist::where('UUID', $uuid)->firstOrFail();
        $statuses = SongStatus::orderBy('StatusName', 'asc')->get();
        
        return view('admin.songs.create-for-entity', [
            'entity' => $artist,
            'entityType' => 'artist',
            'entityName' => $artist->StageName,
            'entityId' => $artist->UmuhanziID,
            'entityIdField' => 'UmuhanziID',
            'statuses' => $statuses
        ]);
    }

    /**
     * Store a song for a specific artist.
     * Route: POST /admin/abahanzi/{uuid}/songs
     */
    public function storeForArtist(Request $request, $uuid)
    {
        $artist = Artist::where('UUID', $uuid)->firstOrFail();
        return $this->storeSongForEntity($request, $artist, 'artist', $artist->UmuhanziID, 'UmuhanziID', $uuid, true);
    }

    /**
     * Show the form for creating a song for a specific orchestra.
     * Route: /admin/orchestre/{uuid}/songs
     */
    public function createForOrchestra($uuid)
    {
        $orchestra = Orchestra::where('UUID', $uuid)->firstOrFail();
        $statuses = SongStatus::orderBy('StatusName', 'asc')->get();
        
        return view('admin.songs.create-for-entity', [
            'entity' => $orchestra,
            'entityType' => 'orchestra',
            'entityName' => $orchestra->OrchestreName,
            'entityId' => $orchestra->OrchestreID,
            'entityIdField' => 'OrchestreID',
            'statuses' => $statuses
        ]);
    }

    /**
     * Store a song for a specific orchestra.
     * Route: POST /admin/orchestre/{uuid}/songs
     */
    public function storeForOrchestra(Request $request, $uuid)
    {
        $orchestra = Orchestra::where('UUID', $uuid)->firstOrFail();
        return $this->storeSongForEntity($request, $orchestra, 'orchestra', $orchestra->OrchestreID, 'OrchestreID', $uuid, true);
    }

    /**
     * Show the form for creating a song for a specific itorero.
     * Route: /admin/amatorero/{uuid}/songs
     */
    public function createForItorero($uuid)
    {
        $itorero = Itorero::where('UUID', $uuid)->firstOrFail();
        $statuses = SongStatus::orderBy('StatusName', 'asc')->get();
        
        return view('admin.songs.create-for-entity', [
            'entity' => $itorero,
            'entityType' => 'itorero',
            'entityName' => $itorero->ItoreroName,
            'entityId' => $itorero->ItoreroID,
            'entityIdField' => 'ItoreroID',
            'statuses' => $statuses
        ]);
    }

    /**
     * Store a song for a specific itorero.
     * Route: POST /admin/amatorero/{uuid}/songs
     */
    public function storeForItorero(Request $request, $uuid)
    {
        $itorero = Itorero::where('UUID', $uuid)->firstOrFail();
        return $this->storeSongForEntity($request, $itorero, 'itorero', $itorero->ItoreroID, 'ItoreroID', $uuid, true);
    }

    /**
     * Helper method to store a song for a specific entity (artist, orchestra, or itorero).
     */
    private function storeSongForEntity(Request $request, $entity, $entityType, $entityId, $entityIdField, $entityUuid, $isAdmin = false)
    {
        // Log incoming request for debugging
        \Log::info('Admin song upload request', [
            'has_audio' => $request->hasFile('audio'),
            'audio_count' => $request->hasFile('audio') ? count($request->file('audio')) : 0,
            'has_image' => $request->hasFile('image'),
            'image_info' => $request->hasFile('image') ? [
                'name' => $request->file('image')->getClientOriginalName(),
                'size' => $request->file('image')->getSize(),
                'mime' => $request->file('image')->getMimeType(),
                'error' => $request->file('image')->getError(),
            ] : 'no image',
            'all_files' => $request->allFiles(),
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'entity_id_field' => $entityIdField,
        ]);
        
        // First, manually validate that we have at least one file
        if (!$request->hasFile('audio') || !is_array($request->file('audio')) || count($request->file('audio')) === 0) {
            \Log::warning('Admin song upload: No audio files provided', [
                'has_audio' => $request->hasFile('audio'),
                'audio_type' => $request->hasFile('audio') ? gettype($request->file('audio')) : 'none',
            ]);
            return back()->withErrors(['audio' => 'Please select at least one audio file to upload.'])->withInput();
        }
        
        try {
            $validated = $request->validate([
                'IndirimboName' => 'nullable|string|max:255',
                'Description' => 'nullable|string',
                'Lyrics' => 'nullable|string',
                'audio' => 'required|array',
                'audio.*' => 'required|file|mimes:mp3|max:51200',
                'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
                'song_names' => 'nullable|array',
                'song_names.*' => 'nullable|string|max:255',
            ]);
            
            \Log::info('Admin song upload: Validation passed', [
                'audio_count' => count($validated['audio']),
                'song_names_count' => isset($validated['song_names']) ? count($validated['song_names']) : 0,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Admin song upload: Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->except(['audio', 'image']),
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Admin song upload: Unexpected error during validation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()])->withInput();
        }

        // Get pending status for all uploaded songs
        \Log::info('Admin song upload: Looking for pending status');
        $pendingStatus = SongStatus::where('StatusName', 'Pending')
            ->orWhere('StatusName', 'pending')
            ->orWhere('StatusName', 'Pending Approval')
            ->first();
        
        \Log::info('Admin song upload: Pending status query result', [
            'found' => $pendingStatus ? true : false,
            'status_id' => $pendingStatus ? $pendingStatus->StatusID : null,
            'status_name' => $pendingStatus ? $pendingStatus->StatusName : null,
        ]);
        
        if (!$pendingStatus) {
            \Log::warning('Admin song upload: Pending status not found, trying StatusID=1');
            $pendingStatus = SongStatus::find(1);
            \Log::info('Admin song upload: StatusID=1 result', [
                'found' => $pendingStatus ? true : false,
                'status_id' => $pendingStatus ? $pendingStatus->StatusID : null,
                'status_name' => $pendingStatus ? $pendingStatus->StatusName : null,
            ]);
        }
        
        if (!$pendingStatus) {
            \Log::warning('Admin song upload: StatusID=1 not found, trying first status');
            $pendingStatus = SongStatus::first();
            \Log::info('Admin song upload: First status result', [
                'found' => $pendingStatus ? true : false,
                'status_id' => $pendingStatus ? $pendingStatus->StatusID : null,
                'status_name' => $pendingStatus ? $pendingStatus->StatusName : null,
            ]);
        }
        
        if (!$pendingStatus) {
            \Log::error('Admin song upload: No status found at all!');
            return back()->withErrors(['status' => 'Unable to set song status. Please contact administrator.'])->withInput();
        }

        \Log::info('Admin song upload: Using status', [
            'status_id' => $pendingStatus->StatusID,
            'status_name' => $pendingStatus->StatusName,
        ]);

        $uploadedSongs = [];
        $errors = [];

        // Get song names from user input
        $songNames = $validated['song_names'] ?? [];
        \Log::info('Admin song upload: Song names from request', [
            'song_names_count' => count($songNames),
            'song_names' => $songNames,
        ]);

        // Handle multiple audio file uploads
        if ($request->hasFile('audio')) {
            $audioFiles = $request->file('audio');
            \Log::info('Admin song upload: Processing audio files', [
                'audio_files_count' => count($audioFiles),
            ]);
            
            foreach ($audioFiles as $index => $audioFile) {
                \Log::info('Admin song upload: Processing file', [
                    'index' => $index,
                    'file_name' => $audioFile->getClientOriginalName(),
                    'file_size' => $audioFile->getSize(),
                    'file_mime' => $audioFile->getMimeType(),
                    'file_error' => $audioFile->getError(),
                ]);
                
                try {
                    $song = new Song();
                    \Log::info('Admin song upload: Created new Song model', [
                        'index' => $index,
                    ]);
                    
                    // Get song name from user input or extract from filename
                    if (!empty($songNames[$index]) && trim($songNames[$index]) !== '') {
                        $song->IndirimboName = trim($songNames[$index]);
                        \Log::info('Admin song upload: Using user-provided song name', [
                            'index' => $index,
                            'song_name' => $song->IndirimboName,
                        ]);
                    } else {
                        // Extract from filename
                        $originalName = $audioFile->getClientOriginalName();
                        $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                        $songName = str_replace(['_', '-'], ' ', $nameWithoutExt);
                        $songName = ucwords(strtolower($songName));
                        $song->IndirimboName = $songName;
                        \Log::info('Admin song upload: Extracted song name from filename', [
                            'index' => $index,
                            'original_name' => $originalName,
                            'extracted_name' => $songName,
                        ]);
                    }
                    
                    $song->Description = $validated['Description'] ?? '';
                    $song->Lyrics = $validated['Lyrics'] ?? '';
                    $song->StatusID = $pendingStatus->StatusID;
                    $song->IsFeatured = false;
                    $song->UUID = (string) Str::uuid();
                    // Set the entity ID using the correct field name
                    $song->setAttribute($entityIdField, $entityId);
                    $song->UserID = auth()->id();
                    $song->ProfilePicture = ''; // Set default empty string for ProfilePicture
                    
                    \Log::info('Admin song upload: Song model attributes set', [
                        'index' => $index,
                        'song_name' => $song->IndirimboName,
                        'entity_id_field' => $entityIdField,
                        'entity_id' => $entityId,
                        'status_id' => $song->StatusID,
                        'uuid' => $song->UUID,
                        'user_id' => $song->UserID,
                        'description_length' => strlen($song->Description),
                        'lyrics_length' => strlen($song->Lyrics),
                        'profile_picture' => $song->ProfilePicture ?: 'empty',
                    ]);

                    // Handle audio file upload
                    \Log::info('Admin song upload: Storing audio file', [
                        'index' => $index,
                        'original_name' => $audioFile->getClientOriginalName(),
                    ]);
                    
                    $extension = $audioFile->getClientOriginalExtension();
                    $fileName = Str::random(100) . '_' . time() . '_' . $index . '.' . $extension;
                    \Log::info('Admin song upload: Generated audio file name', [
                        'index' => $index,
                        'file_name' => $fileName,
                        'extension' => $extension,
                    ]);
                    
                    $path = $audioFile->storeAs('Audios', $fileName, 'local');
                    $song->IndirimboUrl = 'Audios/' . $fileName;
                    
                    \Log::info('Admin song upload: Audio file stored', [
                        'index' => $index,
                        'storage_path' => $path,
                        'indirimbo_url' => $song->IndirimboUrl,
                    ]);

                    // Handle image file upload (only for first file if provided)
                    if ($index === 0) {
                        \Log::info('Admin song upload: Checking for image file', [
                            'index' => $index,
                            'has_image' => $request->hasFile('image'),
                            'all_files' => array_keys($request->allFiles()),
                            'request_keys' => array_keys($request->all()),
                        ]);
                        
                        if ($request->hasFile('image')) {
                            \Log::info('Admin song upload: Processing image file', [
                                'index' => $index,
                                'image_name' => $request->file('image')->getClientOriginalName(),
                                'image_size' => $request->file('image')->getSize(),
                                'image_error' => $request->file('image')->getError(),
                            ]);
                            
                            $imageFile = $request->file('image');
                            $extension = $imageFile->getClientOriginalExtension();
                            $imageFileName = Str::random(100) . '_' . time() . '.' . $extension;
                            $imagePath = $imageFile->storeAs('Pictures', $imageFileName, 'local');
                            $song->ProfilePicture = 'Pictures/' . $imageFileName;
                            
                            \Log::info('Admin song upload: Image file stored', [
                                'index' => $index,
                                'image_path' => $imagePath,
                                'profile_picture' => $song->ProfilePicture,
                            ]);
                        } else {
                            \Log::warning('Admin song upload: No image file provided', [
                                'index' => $index,
                                'is_first_file' => $index === 0,
                                'has_image' => $request->hasFile('image'),
                                'request_has_image_key' => $request->has('image'),
                            ]);
                            // Ensure ProfilePicture is set to empty string if no image
                            if (empty($song->ProfilePicture)) {
                                $song->ProfilePicture = '';
                            }
                        }
                    } else {
                        // For subsequent files, don't set image
                        \Log::info('Admin song upload: Not first file, skipping image', [
                            'index' => $index,
                        ]);
                    }

                    \Log::info('Admin song upload: Attempting to save song to database', [
                        'index' => $index,
                        'song_attributes' => $song->getAttributes(),
                    ]);
                    
                    $song->save();
                    
                    \Log::info('Admin song upload: Song saved successfully', [
                        'index' => $index,
                        'song_id' => $song->IndirimboID,
                        'song_uuid' => $song->UUID,
                        'song_name' => $song->IndirimboName,
                        'indirimbo_id' => $song->IndirimboID,
                    ]);
                    
                    $uploadedSongs[] = $song->IndirimboName;
                    
                    \Log::info('Admin song upload: Song added to uploaded list', [
                        'index' => $index,
                        'uploaded_count' => count($uploadedSongs),
                    ]);
                } catch (\Exception $e) {
                    $errorMsg = 'Failed to upload ' . $audioFile->getClientOriginalName() . ': ' . $e->getMessage();
                    $errors[] = $errorMsg;
                    
                    \Log::error('Admin song upload: Exception occurred while saving song', [
                        'index' => $index,
                        'file_name' => $audioFile->getClientOriginalName(),
                        'error_message' => $e->getMessage(),
                        'error_code' => $e->getCode(),
                        'error_file' => $e->getFile(),
                        'error_line' => $e->getLine(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }
            
            \Log::info('Admin song upload: Finished processing all files', [
                'total_files' => count($audioFiles),
                'uploaded_count' => count($uploadedSongs),
                'error_count' => count($errors),
            ]);
        } else {
            \Log::warning('Admin song upload: No audio files found after validation', [
                'has_audio' => $request->hasFile('audio'),
            ]);
        }

        // Redirect back to the same entity's song creation page
        // Map entity types to route names
        $routeMap = [
            'artist' => 'artists.songs.create',
            'orchestra' => 'orchestras.songs.create',
            'itorero' => 'itoreros.songs.create',
        ];
        $routePrefix = $isAdmin ? 'admin.' : '';
        $routeName = $routePrefix . ($routeMap[$entityType] ?? $entityType . 's.songs.create');

        $message = count($uploadedSongs) > 0 
            ? count($uploadedSongs) . ' song(s) uploaded successfully and set to pending status!' 
            : 'No songs were uploaded.';

        if (count($errors) > 0) {
            $message .= ' Errors: ' . implode(', ', $errors);
        }
        
        \Log::info('Admin song upload: Preparing redirect', [
            'route_name' => $routeName,
            'entity_uuid' => $entityUuid,
            'uploaded_count' => count($uploadedSongs),
            'error_count' => count($errors),
            'message' => $message,
            'message_type' => count($errors) > 0 ? 'error' : 'success',
        ]);
        
        try {
            $redirect = redirect()->route($routeName, ['uuid' => $entityUuid])
                ->with(count($errors) > 0 ? 'error' : 'success', $message);
            
            \Log::info('Admin song upload: Redirect created successfully', [
                'route_name' => $routeName,
            ]);
            
            return $redirect;
        } catch (\Exception $e) {
            \Log::error('Admin song upload: Failed to create redirect', [
                'route_name' => $routeName,
                'entity_uuid' => $entityUuid,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->with(count($errors) > 0 ? 'error' : 'success', $message);
        }
    }
}

