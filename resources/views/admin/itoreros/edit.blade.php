@extends('layouts.app')

@section('title', 'Edit Itorero - Admin')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Edit Itorero</h1>
                <p class="text-zinc-400">Update itorero information</p>
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

            <form action="{{ route('admin.itoreros.update', $itorero->UUID) }}" method="POST" enctype="multipart/form-data" class="bg-zinc-900 rounded-lg p-8">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Current Cover Image -->
                    @if($itorero->ProfilePicture)
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Current Profile Picture</label>
                        <div class="mb-4">
                            <img 
                                src="{{ \App\Helpers\ImageHelper::getImageUrl($itorero->ProfilePicture) }}" 
                                alt="{{ $itorero->ItoreroName }}"
                                class="w-32 h-32 rounded-lg object-cover"
                            >
                        </div>
                    </div>
                    @endif

                    <!-- Itorero Name -->
                    <div>
                        <label for="ItoreroName" class="block text-sm font-medium text-white mb-2">Itorero Name *</label>
                        <input 
                            type="text" 
                            id="ItoreroName" 
                            name="ItoreroName" 
                            value="{{ old('ItoreroName', $itorero->ItoreroName) }}"
                            required
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Enter itorero name"
                        >
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="Description" class="block text-sm font-medium text-white mb-2">Description</label>
                        <textarea 
                            id="Description" 
                            name="Description" 
                            rows="6"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Enter itorero description"
                        >{{ old('Description', $itorero->Description) }}</textarea>
                    </div>

                    <!-- Featured -->
                    <div>
                        <label class="flex items-center gap-3">
                            <input 
                                type="checkbox" 
                                name="IsFeatured" 
                                value="1"
                                {{ old('IsFeatured', $itorero->IsFeatured) ? 'checked' : '' }}
                                class="w-5 h-5 text-green-500 bg-zinc-800 border-zinc-700 rounded focus:ring-green-500"
                            >
                            <span class="text-white">Featured Itorero</span>
                        </label>
                    </div>

                    <!-- Image File -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-white mb-2">Profile Picture (Optional)</label>
                        <input 
                            type="file" 
                            id="image" 
                            name="image"
                            accept="image/jpeg,image/jpg,image/png"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-500 file:text-white hover:file:bg-green-600"
                        >
                        <p class="text-xs text-zinc-500 mt-2">Maximum file size: 2MB. Leave empty to keep current image.</p>
                    </div>

                    <!-- Current Itorero Songs -->
                    @if($itorero->songs->count() > 0)
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Songs by {{ $itorero->ItoreroName }}</label>
                        <div class="max-h-64 overflow-y-auto bg-zinc-800 border border-zinc-700 rounded-lg p-4 mb-4">
                            <div class="space-y-2">
                                @foreach($itorero->songs as $itoreroSong)
                                <div class="flex items-center justify-between gap-3 p-3 bg-zinc-700 rounded-lg">
                                    <div class="flex items-center gap-3 flex-1">
                                        <input 
                                            type="checkbox" 
                                            name="songs[]" 
                                            value="{{ $itoreroSong->IndirimboID }}"
                                            checked
                                            class="w-5 h-5 text-green-500 bg-zinc-800 border-zinc-700 rounded focus:ring-green-500"
                                        >
                                        <div class="flex-1">
                                            <div class="text-white font-medium">{{ $itoreroSong->IndirimboName }}</div>
                                            @if($itoreroSong->artist)
                                                <div class="text-sm text-zinc-400">{{ $itoreroSong->artist->StageName }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <button 
                                        type="button" 
                                        onclick="removeSongFromItorero(this, '{{ $itoreroSong->IndirimboID }}')"
                                        class="text-red-400 hover:text-red-300 transition-colors text-sm font-medium px-3 py-1"
                                        title="Remove from itorero"
                                    >
                                        Remove
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <p class="text-xs text-zinc-500 mb-4">Uncheck a song or click "Remove" to remove it from this itorero. This does not delete the song.</p>
                    </div>
                    @endif

                    <!-- Upload Multiple Songs -->
                    <div class="mt-6">
                        <div class="bg-zinc-900 rounded-lg p-8 border border-zinc-700">
                            <h3 class="text-xl font-semibold text-white mb-4">Upload New Songs for {{ $itorero->ItoreroName }}</h3>
                            
                            <form action="{{ route('admin.itoreros.songs.store', $itorero->UUID) }}" method="POST" enctype="multipart/form-data" id="upload-form">
                                @csrf
                                <input type="hidden" name="ItoreroID" value="{{ $itorero->ItoreroID }}">

                                <div class="space-y-6">
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

                                    <!-- Submit Button -->
                                    <div class="flex gap-4 pt-4">
                                        <button 
                                            type="submit" 
                                            id="submit-btn"
                                            class="flex-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
                                        >
                                            Upload Songs
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Available Songs to Add -->
                    <div>
                        <label for="songs" class="block text-sm font-medium text-white mb-2">Add Existing Songs (Optional)</label>
                        <p class="text-xs text-zinc-400 mb-3">Select existing songs to add to this itorero</p>
                        <div class="max-h-96 overflow-y-auto bg-zinc-800 border border-zinc-700 rounded-lg p-4">
                            @if($allSongs->count() > 0)
                                <div class="space-y-2">
                                    @foreach($allSongs as $song)
                                        @if(!in_array($song->IndirimboID, $itoreroSongIds))
                                        <label class="flex items-center gap-3 p-3 hover:bg-zinc-700 rounded-lg cursor-pointer">
                                            <input 
                                                type="checkbox" 
                                                name="songs[]" 
                                                value="{{ $song->IndirimboID }}"
                                                {{ in_array($song->IndirimboID, old('songs', [])) ? 'checked' : '' }}
                                                class="w-5 h-5 text-green-500 bg-zinc-800 border-zinc-700 rounded focus:ring-green-500"
                                            >
                                            <div class="flex-1">
                                                <div class="text-white font-medium">{{ $song->IndirimboName }}</div>
                                                @if($song->artist)
                                                    <div class="text-sm text-zinc-400">{{ $song->artist->StageName }}</div>
                                                @endif
                                            </div>
                                        </label>
                                        @endif
                                    @endforeach
                                </div>
                                @if($allSongs->whereNotIn('IndirimboID', $itoreroSongIds)->count() === 0)
                                    <p class="text-zinc-400 text-center py-4">All available songs are already assigned to this itorero.</p>
                                @endif
                            @else
                                <p class="text-zinc-400 text-center py-4">No songs available. Create some songs first.</p>
                            @endif
                        </div>
                        <p class="text-xs text-zinc-500 mt-2">Check songs to add them to this itorero</p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button 
                            type="submit" 
                            class="flex-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
                        >
                            Update Itorero
                        </button>
                        <a 
                            href="{{ route('admin.itoreros.index') }}" 
                            class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors text-center"
                        >
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
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
            if (e.target.files && e.target.files.length > 0) {
                const newFiles = Array.from(e.target.files);
                addFiles(newFiles);
                
                setTimeout(() => {
                    if (audioInput.files.length === 0 && selectedFiles.length > 0) {
                        const dataTransfer = new DataTransfer();
                        selectedFiles.forEach(file => {
                            dataTransfer.items.add(file);
                        });
                        audioInput.files = dataTransfer.files;
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
                return file.type === 'audio/mpeg' || file.type === 'audio/mp3' || file.name.toLowerCase().endsWith('.mp3');
            });
            
            if (files.length > 0) {
                addFiles(files);
            } else {
                alert('Please drop MP3 files only.');
            }
        });
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            document.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });
        
        function addFiles(files) {
            if (!files || files.length === 0) return;
            
            files.forEach(file => {
                const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size && f.lastModified === file.lastModified);
                if (!exists) {
                    if (file.type === 'audio/mpeg' || file.type === 'audio/mp3' || file.name.toLowerCase().endsWith('.mp3')) {
                        selectedFiles.push(file);
                    }
                }
            });
            
            updateFileList();
        }
        
        function updateFileList() {
            fileList.innerHTML = '';
            
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach((file, index) => {
                try {
                    dataTransfer.items.add(file);
                } catch (e) {
                    console.error('Error adding file to DataTransfer:', e, file);
                }
            });
            audioInput.files = dataTransfer.files;
            
            const namesContainer = document.getElementById('song-names-container');
            if (namesContainer) {
                namesContainer.innerHTML = '';
            }
            
            if (selectedFiles.length > 0) {
                const fileCount = document.createElement('div');
                fileCount.className = 'text-sm text-white font-medium mb-3';
                fileCount.textContent = `Selected ${selectedFiles.length} file(s). Edit song names below:`;
                fileList.appendChild(fileCount);
                
                selectedFiles.forEach((file, index) => {
                    const defaultName = file.name.replace(/\.[^/.]+$/, '').replace(/[-_]/g, ' ');
                    const defaultNameFormatted = defaultName.split(' ').map(word => 
                        word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
                    ).join(' ');
                    
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
                    
                    const nameInput = fileItem.querySelector('.song-name-input');
                    nameInput.addEventListener('input', function() {
                        const fileIndex = parseInt(this.getAttribute('data-file-index'));
                        if (selectedFiles[fileIndex]) {
                            selectedFiles[fileIndex].songName = this.value;
                            updateHiddenInputs();
                        }
                    });
                });
                
                fileList.querySelectorAll('.remove-file-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const index = parseInt(this.getAttribute('data-file-index'));
                        selectedFiles.splice(index, 1);
                        updateFileList();
                    });
                });
                
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
        
        const uploadForm = document.getElementById('upload-form');
        
        if (uploadForm) {
            uploadForm.addEventListener('submit', function(e) {
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
                        alert('Please select at least one audio file to upload.');
                        return false;
                    }
                } catch (err) {
                    console.error('Error preparing files for upload:', err);
                    e.preventDefault();
                    alert('Error preparing files for upload. Please try again.');
                    return false;
                }
                
                updateHiddenInputs();
            });
        }
    }
});

function removeSongFromItorero(button, songId) {
    // Find the checkbox for this song
    const checkbox = button.closest('div').querySelector('input[type="checkbox"][value="' + songId + '"]');
    if (checkbox) {
        // Uncheck the checkbox
        checkbox.checked = false;
        // Optionally hide or fade out the song row
        const songRow = button.closest('div.flex.items-center.justify-between');
        if (songRow) {
            songRow.style.opacity = '0.5';
            songRow.style.textDecoration = 'line-through';
        }
    }
}
</script>
@endsection

