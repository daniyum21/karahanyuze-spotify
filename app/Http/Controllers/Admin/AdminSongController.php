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
use Illuminate\Support\Facades\Cache;
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
                
                // Exclude declined songs from pending list
                $query->whereNull('declined_at');
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

        // Only include songs with audio URLs
        $query->whereNotNull('IndirimboUrl')
            ->where('IndirimboUrl', '!=', '');
        
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
        // Don't change the status - just mark as declined
        // This way it's not pending, not public, just declined
        $song->save();

        // Redirect back to pending list if that's where we came from
        $redirectParams = [];
        if ($request->has('status') && $request->status === 'pending') {
            $redirectParams['status'] = 'pending';
        }

        return redirect()->route('admin.songs.index', $redirectParams)
            ->with('success', 'Song declined successfully. The user will be notified of the reason.');
    }
    
    /**
     * Approve a song (clears decline status if it was declined)
     */
    public function approve($uuid)
    {
        $song = Song::where('UUID', $uuid)->firstOrFail();
        
        // Find approved status
        $approvedStatus = SongStatus::where('StatusName', 'Approved')
            ->orWhere('StatusName', 'approved')
            ->orWhere('StatusName', 'Public')
            ->orWhere('StatusName', 'public')
            ->first();
        
        if (!$approvedStatus) {
            $approvedStatus = SongStatus::find(2); // Fallback to ID 2
        }
        
        if ($approvedStatus) {
            $song->StatusID = $approvedStatus->StatusID;
        }
        
        // Clear decline status if it was declined
        $song->declined_reason = null;
        $song->declined_at = null;
        $song->declined_by = null;
        $song->save();

        // Redirect back to pending list if that's where we came from
        $redirectParams = [];
        if (request()->has('status') && request()->status === 'pending') {
            $redirectParams['status'] = 'pending';
        }

        return redirect()->route('admin.songs.index', $redirectParams)
            ->with('success', 'Song approved successfully!');
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
        
        // Get existing songs for this artist (only with audio URLs)
        $songs = Song::where('UmuhanziID', $artist->UmuhanziID)
            ->whereNotNull('IndirimboUrl')
            ->where('IndirimboUrl', '!=', '')
            ->with(['status', 'user', 'artist'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.songs.create-for-entity', [
            'entity' => $artist,
            'entityType' => 'artist',
            'entityName' => $artist->StageName,
            'entityId' => $artist->UmuhanziID,
            'entityIdField' => 'UmuhanziID',
            'statuses' => $statuses,
            'songs' => $songs
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
        
        // Get existing songs for this orchestra (only with audio URLs)
        $songs = Song::where('OrchestreID', $orchestra->OrchestreID)
            ->whereNotNull('IndirimboUrl')
            ->where('IndirimboUrl', '!=', '')
            ->with(['status', 'user', 'orchestra'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.songs.create-for-entity', [
            'entity' => $orchestra,
            'entityType' => 'orchestra',
            'entityName' => $orchestra->OrchestreName,
            'entityId' => $orchestra->OrchestreID,
            'entityIdField' => 'OrchestreID',
            'statuses' => $statuses,
            'songs' => $songs
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
        
        // Get existing songs for this itorero (only with audio URLs)
        $songs = Song::where('ItoreroID', $itorero->ItoreroID)
            ->whereNotNull('IndirimboUrl')
            ->where('IndirimboUrl', '!=', '')
            ->with(['status', 'user', 'itorero'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.songs.create-for-entity', [
            'entity' => $itorero,
            'entityType' => 'itorero',
            'entityName' => $itorero->ItoreroName,
            'entityId' => $itorero->ItoreroID,
            'entityIdField' => 'ItoreroID',
            'statuses' => $statuses,
            'songs' => $songs
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
        // First, manually validate that we have at least one file
        if (!$request->hasFile('audio') || !is_array($request->file('audio')) || count($request->file('audio')) === 0) {
            return back()->withErrors(['audio' => 'Please select at least one audio file to upload.'])->withInput();
        }
        
        // Lightweight validation - avoid reading entire files for validation
        try {
            $validated = $request->validate([
                'IndirimboName' => 'nullable|string|max:255',
                'Description' => 'nullable|string',
                'Lyrics' => 'nullable|string',
                'audio' => 'required|array',
                'audio.*' => 'required|file', // Remove mimes check - we'll do basic extension check instead
                'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
                'song_names' => 'nullable|array',
                'song_names.*' => 'nullable|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()])->withInput();
        }

        // Cache status lookup - this is a static lookup that rarely changes
        $pendingStatus = Cache::remember('song_status_pending', 3600, function () {
            return SongStatus::where('StatusName', 'Pending')
                ->orWhere('StatusName', 'pending')
                ->orWhere('StatusName', 'Pending Approval')
                ->first() 
                ?? SongStatus::find(1) 
                ?? SongStatus::first();
        });
        
        if (!$pendingStatus) {
            return back()->withErrors(['status' => 'Unable to set song status. Please contact administrator.'])->withInput();
        }

        $uploadedSongs = [];
        $errors = [];

        // Get song names from user input
        $songNames = $validated['song_names'] ?? [];

        // Handle multiple audio file uploads
        if ($request->hasFile('audio')) {
            $audioFiles = $request->file('audio');
            $userId = auth()->id();
            $now = now();
            $imageFileName = null;
            
            // Handle image file upload once (only for first file if provided)
            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                $extension = $imageFile->getClientOriginalExtension();
                $imageFileName = Str::random(100) . '_' . time() . '.' . $extension;
                $imageFile->storeAs('Pictures', $imageFileName, 'local');
            }
            
            // Prepare bulk insert data for faster database operations
            $songsToInsert = [];
            
            foreach ($audioFiles as $index => $audioFile) {
                try {
                    // Quick validation - check extension only (faster than mime type check)
                    $extension = strtolower($audioFile->getClientOriginalExtension());
                    if ($extension !== 'mp3') {
                        $errors[] = $audioFile->getClientOriginalName() . ' is not an MP3 file.';
                        continue;
                    }
                    
                    // Check file size (50MB max)
                    if ($audioFile->getSize() > 52428800) {
                        $errors[] = $audioFile->getClientOriginalName() . ' exceeds 50MB limit.';
                        continue;
                    }
                    
                    // Get song name from user input or extract from filename
                    if (!empty($songNames[$index]) && trim($songNames[$index]) !== '') {
                        $songName = trim($songNames[$index]);
                    } else {
                        // Extract from filename
                        $originalName = $audioFile->getClientOriginalName();
                        $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                        $songName = str_replace(['_', '-'], ' ', $nameWithoutExt);
                        $songName = ucwords(strtolower($songName));
                    }
                    
                    // Handle audio file upload
                    $fileName = Str::random(100) . '_' . time() . '_' . $index . '.' . $extension;
                    $audioFile->storeAs('Audios', $fileName, 'local');
                    
                    // Set entity fields based on entity type
                    $umuhanziId = null;
                    $orchestreId = null;
                    $itoreroId = null;
                    
                    if ($entityIdField === 'UmuhanziID') {
                        $umuhanziId = $entityId;
                    } elseif ($entityIdField === 'OrchestreID') {
                        $orchestreId = $entityId;
                    } elseif ($entityIdField === 'ItoreroID') {
                        $itoreroId = $entityId;
                    }
                    
                    // Prepare song data for bulk insert
                    $songsToInsert[] = [
                        'IndirimboName' => $songName,
                        'Description' => $validated['Description'] ?? '',
                        'Lyrics' => $validated['Lyrics'] ?? '',
                        'StatusID' => $pendingStatus->StatusID,
                        'IsFeatured' => false,
                        'IsPrivate' => false,
                        'UUID' => (string) Str::uuid(),
                        'UmuhanziID' => $umuhanziId,
                        'OrchestreID' => $orchestreId,
                        'ItoreroID' => $itoreroId,
                        'UserID' => $userId,
                        'ProfilePicture' => ($index === 0 && $imageFileName) ? 'Pictures/' . $imageFileName : '',
                        'IndirimboUrl' => 'Audios/' . $fileName,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    
                    $uploadedSongs[] = $songName;
                } catch (\Exception $e) {
                    $errorMsg = 'Failed to upload ' . $audioFile->getClientOriginalName() . ': ' . $e->getMessage();
                    $errors[] = $errorMsg;
                    // Only log actual errors, not validation issues
                    if (config('app.debug')) {
                        \Log::error('Admin song upload: Exception occurred', [
                            'file_name' => $audioFile->getClientOriginalName(),
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
            
            // Bulk insert all songs at once (much faster than individual saves)
            if (!empty($songsToInsert)) {
                try {
                    \DB::table('Indirimbo')->insert($songsToInsert);
                } catch (\Exception $e) {
                    // Fallback to individual saves if bulk insert fails
                    foreach ($songsToInsert as $songData) {
                        try {
                            $song = new Song();
                            $song->fill($songData);
                            $song->save();
                        } catch (\Exception $e2) {
                            $errors[] = 'Failed to save song: ' . ($songData['IndirimboName'] ?? 'Unknown');
                        }
                    }
                }
            }
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
        
        return redirect()->route($routeName, ['uuid' => $entityUuid])
            ->with(count($errors) > 0 ? 'error' : 'success', $message);
    }
}

