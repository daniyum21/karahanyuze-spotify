<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Artist;
use App\Models\Orchestra;
use App\Models\Itorero;
use App\Models\SongStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserSongController extends Controller
{
    /**
     * Show the form for creating a new song.
     * Accepts optional query parameters: artist, orchestra, or itorero UUID to pre-select.
     */
    public function create(Request $request)
    {
        $artists = Artist::orderBy('StageName', 'asc')->get();
        $orchestras = Orchestra::orderBy('OrchestreName', 'asc')->get();
        $itoreros = Itorero::orderBy('ItoreroName', 'asc')->get();
        
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

        return view('user.songs.create', compact('artists', 'orchestras', 'itoreros', 'selectedArtist', 'selectedOrchestra', 'selectedItorero'));
    }

    /**
     * Store a newly created song.
     * Automatically set status to "Pending" for approval.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'IndirimboName' => 'required|string|max:255',
            'Description' => 'nullable|string',
            'Lyrics' => 'nullable|string',
            'UmuhanziID' => 'nullable|exists:Abahanzi,UmuhanziID',
            'OrchestreID' => 'nullable|exists:Orchestres,OrchestreID',
            'ItoreroID' => 'nullable|exists:Amatorero,ItoreroID',
            'audio' => 'required|file|mimes:mp3|max:51200', // 50MB max, required for users
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        // Find or create "Pending" status
        // Typically status ID 1 = Pending, 2 = Approved/Public, etc.
        // We'll find the status by name or use ID 1 as default
        $pendingStatus = SongStatus::where('StatusName', 'Pending')
            ->orWhere('StatusName', 'pending')
            ->orWhere('StatusName', 'Pending Approval')
            ->first();
        
        // If no pending status found, try ID 1 (common default)
        if (!$pendingStatus) {
            $pendingStatus = SongStatus::find(1);
        }
        
        // If still no status, get the first available
        if (!$pendingStatus) {
            $pendingStatus = SongStatus::first();
        }

        if (!$pendingStatus) {
            return back()->withErrors(['status' => 'Unable to set song status. Please contact administrator.'])->withInput();
        }

        $song = new Song();
        $song->IndirimboName = $validated['IndirimboName'];
        $song->Description = $validated['Description'] ?? '';
        $song->Lyrics = $validated['Lyrics'] ?? '';
        $song->StatusID = $pendingStatus->StatusID; // Set to pending for approval
        $song->IsFeatured = false; // Regular users can't set featured
        $song->UUID = (string) Str::uuid();
        $song->UserID = Auth::id() ?? 0; // Set user who created it

        // Set owner (artist, orchestra, or itorero)
        if (!empty($validated['UmuhanziID'])) {
            $song->UmuhanziID = $validated['UmuhanziID'];
        } elseif (!empty($validated['OrchestreID'])) {
            $song->OrchestreID = $validated['OrchestreID'];
        } elseif (!empty($validated['ItoreroID'])) {
            $song->ItoreroID = $validated['ItoreroID'];
        }

        // Handle audio file upload (required for users)
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

        // If an entity was pre-selected, preserve it in the redirect
        // This allows users to quickly add multiple songs for the same artist/orchestra/itorero
        $redirectRoute = route('user.songs.create');
        if (!empty($validated['UmuhanziID'])) {
            $artist = Artist::find($validated['UmuhanziID']);
            if ($artist) {
                $redirectRoute = route('user.songs.create', ['artist' => $artist->UUID]);
            }
        } elseif (!empty($validated['OrchestreID'])) {
            $orchestra = Orchestra::find($validated['OrchestreID']);
            if ($orchestra) {
                $redirectRoute = route('user.songs.create', ['orchestra' => $orchestra->UUID]);
            }
        } elseif (!empty($validated['ItoreroID'])) {
            $itorero = Itorero::find($validated['ItoreroID']);
            if ($itorero) {
                $redirectRoute = route('user.songs.create', ['itorero' => $itorero->UUID]);
            }
        }

        return redirect($redirectRoute)
            ->with('success', 'Song submitted successfully! It will be reviewed and approved by an administrator before being published. You can add another song for this ' . (!empty($validated['UmuhanziID']) ? 'artist' : (!empty($validated['OrchestreID']) ? 'orchestra' : 'itorero')) . ' if needed.');
    }

    /**
     * Show the form for creating a song for a specific artist.
     * Route: /artists/{uuid}/songs
     */
    public function createForArtist($uuid)
    {
        $artist = Artist::where('UUID', $uuid)->firstOrFail();
        return view('user.songs.create-for-entity', [
            'entity' => $artist,
            'entityType' => 'artist',
            'entityName' => $artist->StageName,
            'entityId' => $artist->UmuhanziID,
            'entityIdField' => 'UmuhanziID'
        ]);
    }

    /**
     * Store a song for a specific artist.
     * Route: POST /artists/{uuid}/songs
     */
    public function storeForArtist(Request $request, $uuid)
    {
        $artist = Artist::where('UUID', $uuid)->firstOrFail();
        return $this->storeSongForEntity($request, $artist, 'artist', $artist->UmuhanziID, 'UmuhanziID', $uuid);
    }

    /**
     * Show the form for creating a song for a specific orchestra.
     * Route: /orchestre/{uuid}/songs
     */
    public function createForOrchestra($uuid)
    {
        $orchestra = Orchestra::where('UUID', $uuid)->firstOrFail();
        return view('user.songs.create-for-entity', [
            'entity' => $orchestra,
            'entityType' => 'orchestra',
            'entityName' => $orchestra->OrchestreName,
            'entityId' => $orchestra->OrchestreID,
            'entityIdField' => 'OrchestreID'
        ]);
    }

    /**
     * Store a song for a specific orchestra.
     * Route: POST /orchestre/{uuid}/songs
     */
    public function storeForOrchestra(Request $request, $uuid)
    {
        $orchestra = Orchestra::where('UUID', $uuid)->firstOrFail();
        return $this->storeSongForEntity($request, $orchestra, 'orchestra', $orchestra->OrchestreID, 'OrchestreID', $uuid);
    }

    /**
     * Show the form for creating a song for a specific itorero.
     * Route: /amatorero/{uuid}/songs
     */
    public function createForItorero($uuid)
    {
        $itorero = Itorero::where('UUID', $uuid)->firstOrFail();
        return view('user.songs.create-for-entity', [
            'entity' => $itorero,
            'entityType' => 'itorero',
            'entityName' => $itorero->ItoreroName,
            'entityId' => $itorero->ItoreroID,
            'entityIdField' => 'ItoreroID'
        ]);
    }

    /**
     * Store a song for a specific itorero.
     * Route: POST /amatorero/{uuid}/songs
     */
    public function storeForItorero(Request $request, $uuid)
    {
        $itorero = Itorero::where('UUID', $uuid)->firstOrFail();
        return $this->storeSongForEntity($request, $itorero, 'itorero', $itorero->ItoreroID, 'ItoreroID', $uuid);
    }

    /**
     * Helper method to store a song for a specific entity (artist, orchestra, or itorero).
     */
    private function storeSongForEntity(Request $request, $entity, $entityType, $entityId, $entityIdField, $entityUuid)
    {
        // Ensure PHP upload limits are set high enough at runtime
        // This is needed because the built-in PHP server might not apply -d flags correctly
        $currentUploadMax = ini_get('upload_max_filesize');
        $currentPostMax = ini_get('post_max_size');
        
        // Convert to bytes for comparison
        $uploadMaxBytes = $this->convertToBytes($currentUploadMax);
        $postMaxBytes = $this->convertToBytes($currentPostMax);
        
        // Set higher limits if current ones are too low
        if ($uploadMaxBytes < 524288000) { // 500MB
            @ini_set('upload_max_filesize', '500M');
        }
        if ($postMaxBytes < 1073741824) { // 1024MB
            @ini_set('post_max_size', '1024M');
        }
        
        // First, validate that we have at least one file
        if (!$request->hasFile('audio') || !is_array($request->file('audio')) || count($request->file('audio')) === 0) {
            return back()->withErrors(['audio' => 'Please select at least one audio file to upload.'])->withInput();
        }
        
        // Validate each file individually with more lenient rules
        $audioFiles = $request->file('audio');
        $errors = [];
        
        foreach ($audioFiles as $index => $file) {
            // Check if file exists
            if (!$file) {
                $errors["audio.{$index}"] = "File at position {$index} is missing.";
                continue;
            }
            
            // Check if file has upload error
            if ($file->getError() !== UPLOAD_ERR_OK) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive in php.ini (Current limit: ' . ini_get('upload_max_filesize') . ')',
                    UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive in HTML form',
                    UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                    UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',
                ];
                $errorMsg = $errorMessages[$file->getError()] ?? 'Unknown upload error';
                
                // Log PHP configuration for debugging
                \Log::warning('File upload error', [
                    'error_code' => $file->getError(),
                    'error_message' => $errorMsg,
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size'),
                    'file_size' => $file->getSize(),
                    'file_name' => $file->getClientOriginalName(),
                ]);
                
                $errors["audio.{$index}"] = "File at position {$index}: {$errorMsg}. File size: " . round($file->getSize() / 1024 / 1024, 2) . "MB. Current PHP limit: " . ini_get('upload_max_filesize');
                continue;
            }
            
            // Check if file is valid (this checks if it was uploaded successfully)
            if (!$file->isValid()) {
                $errors["audio.{$index}"] = "File at position {$index} is invalid or failed to upload. Error: " . $file->getError();
                continue;
            }
            
            // Check file extension
            $extension = strtolower($file->getClientOriginalExtension());
            if ($extension !== 'mp3') {
                $errors["audio.{$index}"] = "File at position {$index} must be an MP3 file. Got: {$extension}";
                continue;
            }
            
            // Check file size (50MB = 52428800 bytes)
            if ($file->getSize() > 52428800) {
                $errors["audio.{$index}"] = "File at position {$index} exceeds the 50MB limit. Size: " . round($file->getSize() / 1024 / 1024, 2) . "MB";
                continue;
            }
        }
        
        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }
        
        // Validate other fields
        $validated = $request->validate([
            'song_names' => 'nullable|array',
            'song_names.*' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        // Find or create "Pending" status
        $pendingStatus = SongStatus::where('StatusName', 'Pending')
            ->orWhere('StatusName', 'pending')
            ->orWhere('StatusName', 'Pending Approval')
            ->first();
        
        if (!$pendingStatus) {
            $pendingStatus = SongStatus::find(1);
        }
        
        if (!$pendingStatus) {
            $pendingStatus = SongStatus::first();
        }

        if (!$pendingStatus) {
            return back()->withErrors(['status' => 'Unable to set song status. Please contact administrator.'])->withInput();
        }

        $uploadedSongs = [];
        $uploadErrors = [];

        // Handle multiple audio file uploads (we already validated above)
        $audioFiles = $request->file('audio');
        $songNames = $validated['song_names'] ?? [];
        
        // Debug: Log file information
        \Log::info('File upload debug', [
            'file_count' => count($audioFiles),
            'files' => array_map(function($file) {
                return $file ? [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'error' => $file->getError(),
                    'valid' => $file->isValid(),
                    'extension' => $file->getClientOriginalExtension(),
                ] : null;
            }, $audioFiles),
        ]);
        
        // Filter out any null/invalid files and re-index array
        $validAudioFiles = [];
        foreach ($audioFiles as $index => $audioFile) {
            if ($audioFile && $audioFile->isValid()) {
                $validAudioFiles[] = $audioFile;
            } else {
                \Log::warning('Invalid file at index', [
                    'index' => $index,
                    'file' => $audioFile ? [
                        'name' => $audioFile->getClientOriginalName(),
                        'error' => $audioFile->getError(),
                        'valid' => $audioFile->isValid(),
                    ] : 'null',
                ]);
            }
        }
        
        foreach ($validAudioFiles as $index => $audioFile) {
                try {
                    $song = new Song();
                    
                    // Get song name from user input or extract from filename
                    if (!empty($songNames[$index]) && trim($songNames[$index]) !== '') {
                        $song->IndirimboName = trim($songNames[$index]);
                    } else {
                        // Extract from filename
                        $originalName = $audioFile->getClientOriginalName();
                        $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                        $songName = str_replace(['_', '-'], ' ', $nameWithoutExt);
                        $songName = ucwords(strtolower($songName));
                        $song->IndirimboName = $songName;
                    }
                    
                    $song->Description = '';
                    $song->Lyrics = '';
                    $song->StatusID = $pendingStatus->StatusID;
                    $song->IsFeatured = false;
                    $song->UUID = (string) Str::uuid();
                    $song->UserID = Auth::id() ?? 0;
                    $song->$entityIdField = $entityId;

                    // Handle audio file upload
                    $extension = $audioFile->getClientOriginalExtension();
                    $fileName = Str::random(100) . '_' . time() . '_' . $index . '.' . $extension;
                    $path = $audioFile->storeAs('Audios', $fileName, 'local');
                    $song->IndirimboUrl = 'Audios/' . $fileName;

                    // Handle image file upload only for the first file
                    if ($index === 0 && $request->hasFile('image')) {
                        $imageFile = $request->file('image');
                        $imageExtension = $imageFile->getClientOriginalExtension();
                        $imageFileName = Str::random(100) . '_' . time() . '.' . $imageExtension;
                        $imagePath = $imageFile->storeAs('Pictures', $imageFileName, 'local');
                        $song->ProfilePicture = 'Pictures/' . $imageFileName;
                    }

                    $song->save();
                    $uploadedSongs[] = $song->IndirimboName;
                } catch (\Exception $e) {
                    $uploadErrors[] = 'Failed to upload ' . $audioFile->getClientOriginalName() . ': ' . $e->getMessage();
                }
        }

        // Redirect back to the same entity's song creation page
        $routeMap = [
            'artist' => 'artists.songs.create',
            'orchestra' => 'orchestre.songs.create',
            'itorero' => 'amatorero.songs.create',
        ];
        $routeName = $routeMap[$entityType] ?? $entityType . 's.songs.create';

        $message = count($uploadedSongs) > 0 
            ? count($uploadedSongs) . ' song(s) uploaded successfully and set to pending status!' 
            : 'No songs were uploaded.';

        if (count($uploadErrors) > 0) {
            $message .= ' Errors: ' . implode(', ', $uploadErrors);
        }
        
        return redirect()->route($routeName, ['uuid' => $entityUuid])
            ->with(count($uploadErrors) > 0 ? 'error' : 'success', $message);
    }
    
    /**
     * Convert PHP size string (e.g., "500M", "2G") to bytes
     */
    private function convertToBytes($size)
    {
        $size = trim($size);
        $last = strtolower($size[strlen($size) - 1]);
        $size = (int) $size;
        
        switch ($last) {
            case 'g':
                $size *= 1024;
            case 'm':
                $size *= 1024;
            case 'k':
                $size *= 1024;
        }
        
        return $size;
    }
}

