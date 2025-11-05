@extends('layouts.app')

@section('title', 'Upload Song for ' . $entityName . ' - Admin')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Upload Song for {{ $entityName }}</h1>
                <p class="text-zinc-400">Add a new song for this {{ $entityType }}</p>
            </div>

            @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="bg-red-500/20 border border-red-500 text-red-400 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @php
                $routeMap = [
                    'artist' => 'artists.songs.store',
                    'orchestra' => 'orchestras.songs.store',
                    'itorero' => 'itoreros.songs.store',
                ];
                $routeName = 'admin.' . ($routeMap[$entityType] ?? $entityType . 's.songs.store');
            @endphp
            <form action="{{ route($routeName, ['uuid' => $entity->UUID]) }}" method="POST" enctype="multipart/form-data" class="bg-zinc-900 rounded-lg p-8">
                @csrf
                <input type="hidden" name="{{ $entityIdField }}" value="{{ $entityId }}">

                <div class="space-y-6">
                    <!-- Entity Info -->
                    <div class="bg-zinc-800 rounded-lg p-4 mb-6">
                        <p class="text-sm text-zinc-400 mb-1">Adding song for:</p>
                        <p class="text-xl font-semibold text-white">{{ $entityName }}</p>
                    </div>

                    <!-- Song Names Container (for multiple files) -->
                    <div id="song-names-container"></div>

                    <!-- Note about status -->
                    <div class="bg-yellow-500/20 border border-yellow-500/50 rounded-lg p-4">
                        <p class="text-sm text-yellow-400">
                            <strong>Note:</strong> All uploaded songs will be set to <strong>Pending</strong> status by default. 
                            You can edit each song individually later to add description/lyrics, approve, and make them public.
                        </p>
                    </div>

                    <!-- Audio Files (Multiple) -->
                    <div>
                        <label for="audio" class="block text-sm font-medium text-white mb-2">Audio Files (MP3) *</label>
                        <p class="text-xs text-zinc-400 mb-2">Select one or more MP3 files to upload, or drag and drop them here. Song names will be extracted from filenames.</p>
                        <div 
                            id="drop-zone" 
                            class="w-full px-4 py-8 bg-zinc-800 border-2 border-dashed border-zinc-700 rounded-lg text-center transition-colors hover:border-green-500 hover:bg-zinc-700/50 cursor-pointer"
                        >
                            <input 
                                type="file" 
                                id="audio" 
                                name="audio[]"
                                accept="audio/mpeg,audio/mp3"
                                multiple
                                required
                                class="hidden"
                            >
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-zinc-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="text-white font-medium mb-1">Click to select files or drag and drop</p>
                                <p class="text-xs text-zinc-400">MP3 files only • Maximum 50MB per file</p>
                            </div>
                        </div>
                        <p class="text-xs text-zinc-500 mt-2">Maximum file size per file: 50MB. You can select multiple files at once.</p>
                        <div id="file-list" class="mt-4 space-y-2"></div>
                    </div>

                    <!-- Image File -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-white mb-2">Image (Optional)</label>
                        <input 
                            type="file" 
                            id="image" 
                            name="image"
                            accept="image/jpeg,image/jpg,image/png"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-500 file:text-white hover:file:bg-green-600"
                        >
                        <p class="text-xs text-zinc-500 mt-2">Maximum file size: 2MB</p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button 
                            type="submit" 
                            id="submit-btn"
                            class="flex-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
                        >
                            Upload Songs
                        </button>
                        <a 
                            href="{{ route('admin.songs.index') }}" 
                            class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors text-center"
                        >
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
            
            <!-- Existing Songs Section -->
            @if(isset($songs) && $songs->count() > 0)
            <div class="mt-12">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white">Existing Songs for {{ $entityName }}</h2>
                    <span class="text-zinc-400 text-sm">{{ $songs->total() }} song(s) total</span>
                </div>
                
                <div class="bg-zinc-900 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-zinc-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Song</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Uploaded</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-800">
                                @foreach($songs as $song)
                                <tr class="hover:bg-zinc-800/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-4">
                                            @if($song->ProfilePicture)
                                            <img 
                                                src="{{ \App\Helpers\ImageHelper::getImageUrl($song->ProfilePicture) }}" 
                                                alt="{{ $song->IndirimboName }}"
                                                class="w-12 h-12 object-cover rounded"
                                            >
                                            @else
                                            <div class="w-12 h-12 bg-zinc-800 rounded flex items-center justify-center">
                                                <svg class="w-6 h-6 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                                </svg>
                                            </div>
                                            @endif
                                            <div>
                                                <div class="text-white font-medium">{{ $song->IndirimboName }}</div>
                                                @if($song->user)
                                                <div class="text-xs text-zinc-400">By {{ $song->user->PublicName ?? $song->user->UserName }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($song->status)
                                        <span class="px-2 py-1 text-xs font-semibold rounded
                                            @if($song->status->StatusName === 'Approved' || $song->status->StatusName === 'approved') bg-green-500/20 text-green-400
                                            @elseif($song->status->StatusName === 'Pending' || $song->status->StatusName === 'pending') bg-yellow-500/20 text-yellow-400
                                            @else bg-zinc-500/20 text-zinc-400
                                            @endif">
                                            {{ $song->status->StatusName }}
                                        </span>
                                        @else
                                        <span class="text-zinc-500 text-xs">Unknown</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-400">
                                        {{ $song->created_at ? $song->created_at->diffForHumans() : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-3">
                                            <a 
                                                href="{{ route('admin.songs.edit', $song->UUID) }}" 
                                                class="text-blue-400 hover:text-blue-300 transition-colors"
                                            >
                                                Edit
                                            </a>
                                            <a 
                                                href="{{ route('indirimbo.show', [$song->slug, $song->UUID]) }}" 
                                                target="_blank"
                                                class="text-green-400 hover:text-green-300 transition-colors"
                                            >
                                                View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($songs->hasPages())
                    <div class="px-6 py-4 bg-zinc-800 border-t border-zinc-700">
                        {{ $songs->links() }}
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="mt-12 text-center py-12 bg-zinc-900 rounded-lg">
                <svg class="w-16 h-16 text-zinc-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                </svg>
                <p class="text-zinc-400 text-lg mb-2">No songs uploaded yet</p>
                <p class="text-zinc-500 text-sm">Upload your first song using the form above</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Display selected files with remove functionality and drag & drop
    const audioInput = document.getElementById('audio');
    const fileList = document.getElementById('file-list');
    const dropZone = document.getElementById('drop-zone');
    let selectedFiles = [];
    
    if (audioInput && fileList && dropZone) {
        // Click on drop zone to trigger file input
        dropZone.addEventListener('click', function() {
            audioInput.click();
        });
        
        // File input change event
        audioInput.addEventListener('change', function(e) {
            console.log('File input changed, files selected:', e.target.files.length);
            if (e.target.files && e.target.files.length > 0) {
                const newFiles = Array.from(e.target.files);
                console.log('Adding files:', newFiles.map(f => f.name));
                addFiles(newFiles);
                
                // After adding files, ensure they're still in the input
                // This is important for when files are selected via click (not drag-and-drop)
                setTimeout(() => {
                    if (audioInput.files.length === 0 && selectedFiles.length > 0) {
                        console.log('Files lost from input, restoring...');
                        const dataTransfer = new DataTransfer();
                        selectedFiles.forEach(file => {
                            dataTransfer.items.add(file);
                        });
                        audioInput.files = dataTransfer.files;
                        console.log('Files restored, input now has', audioInput.files.length, 'files');
                    }
                }, 100);
            }
        });
        
        // Drag and drop events
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.add('border-green-500', 'bg-green-500/10');
        });
        
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('border-green-500', 'bg-green-500/10');
        });
        
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('border-green-500', 'bg-green-500/10');
            
            const files = Array.from(e.dataTransfer.files).filter(file => {
                // Only accept MP3 files
                return file.type === 'audio/mpeg' || file.type === 'audio/mp3' || file.name.toLowerCase().endsWith('.mp3');
            });
            
            if (files.length > 0) {
                addFiles(files);
            } else {
                alert('Please drop MP3 files only.');
            }
        });
        
        // Prevent default drag behaviors on window
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            document.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });
        
        function addFiles(files) {
            console.log('addFiles called with', files.length, 'files');
            if (!files || files.length === 0) {
                console.warn('addFiles called with no files');
                return;
            }
            
            files.forEach(file => {
                // Check if file is not already in the array
                const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size && f.lastModified === file.lastModified);
                if (!exists) {
                    // Validate file type
                    if (file.type === 'audio/mpeg' || file.type === 'audio/mp3' || file.name.toLowerCase().endsWith('.mp3')) {
                        selectedFiles.push(file);
                        console.log('Added file:', file.name);
                    } else {
                        console.warn('Skipped file (not MP3):', file.name, 'Type:', file.type);
                    }
                } else {
                    console.log('File already exists:', file.name);
                }
            });
            
            console.log('Total selected files:', selectedFiles.length);
            updateFileList();
        }
        
        function updateFileList() {
            console.log('updateFileList called. selectedFiles:', selectedFiles.length);
            fileList.innerHTML = '';
            
            // Update the file input with remaining files
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach((file, index) => {
                try {
                    dataTransfer.items.add(file);
                    console.log('Added file to DataTransfer:', index, file.name);
                } catch (e) {
                    console.error('Error adding file to DataTransfer:', e, file);
                }
            });
            audioInput.files = dataTransfer.files;
            
            console.log('File input now has', audioInput.files.length, 'files');
            
            // Verify files were set correctly
            if (audioInput.files.length !== selectedFiles.length) {
                console.warn('Warning: File count mismatch. Expected:', selectedFiles.length, 'Got:', audioInput.files.length);
            }
            
            // Update hidden inputs for song names
            const namesContainer = document.getElementById('song-names-container');
            if (namesContainer) {
                namesContainer.innerHTML = '';
            }
            
            if (selectedFiles.length > 0) {
                console.log('Displaying', selectedFiles.length, 'files in the file list');
                const fileCount = document.createElement('div');
                fileCount.className = 'text-sm text-white font-medium mb-3';
                fileCount.textContent = `Selected ${selectedFiles.length} file(s). Edit song names below:`;
                fileList.appendChild(fileCount);
                
                selectedFiles.forEach((file, index) => {
                    // Extract default song name from filename
                    const defaultName = file.name.replace(/\.[^/.]+$/, '').replace(/[-_]/g, ' ');
                    const defaultNameFormatted = defaultName.split(' ').map(word => 
                        word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
                    ).join(' ');
                    
                    // Store default name if not already set
                    if (!file.songName) {
                        file.songName = defaultNameFormatted;
                    }
                    
                    const fileItem = document.createElement('div');
                    fileItem.className = 'bg-zinc-800 rounded-lg p-4 mb-3';
                    fileItem.setAttribute('data-file-index', index);
                    fileItem.innerHTML = `
                        <div class="flex items-start gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="text-sm text-zinc-400 mb-2 break-words">${escapeHtml(file.name)}</div>
                                <div class="text-xs text-zinc-500 mb-3">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
                                <div>
                                    <label class="block text-xs text-zinc-400 mb-1">Song Name</label>
                                    <input 
                                        type="text" 
                                        class="song-name-input w-full px-3 py-2 bg-zinc-900 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                        value="${escapeHtml(file.songName)}"
                                        data-file-index="${index}"
                                        placeholder="Enter song name"
                                    >
                                </div>
                            </div>
                            <button 
                                type="button" 
                                class="remove-file-btn flex-shrink-0 text-red-400 hover:text-red-300 transition-colors p-2 mt-1"
                                data-file-index="${index}"
                                aria-label="Remove file"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    `;
                    fileList.appendChild(fileItem);
                    
                    // Add event listener to song name input
                    const nameInput = fileItem.querySelector('.song-name-input');
                    nameInput.addEventListener('input', function() {
                        const fileIndex = parseInt(this.getAttribute('data-file-index'));
                        if (selectedFiles[fileIndex]) {
                            selectedFiles[fileIndex].songName = this.value;
                            // Update hidden input
                            updateHiddenInputs();
                        }
                    });
                });
                
                // Add event listeners to remove buttons
                fileList.querySelectorAll('.remove-file-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation(); // Prevent triggering drop zone click
                        const index = parseInt(this.getAttribute('data-file-index'));
                        selectedFiles.splice(index, 1);
                        updateFileList();
                    });
                });
                
                // Update hidden inputs for song names
                updateHiddenInputs();
            }
        }
        
        function updateHiddenInputs() {
            const namesContainer = document.getElementById('song-names-container');
            if (!namesContainer) return;
            
            namesContainer.innerHTML = '';
            
            selectedFiles.forEach((file, index) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `song_names[${index}]`;
                input.value = file.songName || '';
                namesContainer.appendChild(input);
            });
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Update hidden inputs before form submission
        const form = document.querySelector('form');
        const imageInput = document.getElementById('image');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                console.log('Form submission started');
                
                // Log image input status
                if (imageInput) {
                    console.log('Image input status:', {
                        hasFiles: imageInput.files && imageInput.files.length > 0,
                        fileCount: imageInput.files ? imageInput.files.length : 0,
                        fileName: imageInput.files && imageInput.files.length > 0 ? imageInput.files[0].name : 'none',
                    });
                } else {
                    console.warn('Image input not found!');
                }
                
                // Ensure files are re-attached
                try {
                    const dataTransfer = new DataTransfer();
                    selectedFiles.forEach(file => {
                        try {
                            dataTransfer.items.add(file);
                        } catch (err) {
                            console.error('Error adding file to DataTransfer during submit:', err, file);
                        }
                    });
                    audioInput.files = dataTransfer.files;
                    
                    if (audioInput.files.length === 0) {
                        e.preventDefault();
                        alert('Error: No files selected. Please select files again.');
                        return false;
                    }
                    console.log('Submitting form with', audioInput.files.length, 'audio files');
                } catch (err) {
                    console.error('Error preparing files for upload:', err);
                    e.preventDefault();
                    alert('Error preparing files for upload. Please try again.');
                    return false;
                }
                
                // Update hidden inputs for song names
                updateHiddenInputs();
                
                console.log('Form submission proceeding...');
            });
        }
    }
});
</script>
@endsection

