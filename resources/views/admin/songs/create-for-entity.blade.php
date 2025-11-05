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

                    <!-- Song Name (Optional - only used if single file upload) -->
                    <div>
                        <label for="IndirimboName" class="block text-sm font-medium text-white mb-2">Song Name (Optional)</label>
                        <p class="text-xs text-zinc-400 mb-2">Leave empty for bulk upload - song names will be extracted from filenames</p>
                        <input 
                            type="text" 
                            id="IndirimboName" 
                            name="IndirimboName" 
                            value="{{ old('IndirimboName') }}"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Enter song name (optional for bulk upload)"
                        >
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="Description" class="block text-sm font-medium text-white mb-2">Description</label>
                        <div id="Description" style="min-height: 200px;">{!! old('Description') !!}</div>
                        <textarea 
                            name="Description" 
                            style="display: none;"
                        >{{ old('Description') }}</textarea>
                    </div>

                    <!-- Lyrics -->
                    <div>
                        <label for="Lyrics" class="block text-sm font-medium text-white mb-2">Lyrics</label>
                        <div id="Lyrics" style="min-height: 400px;">{!! old('Lyrics') !!}</div>
                        <textarea 
                            name="Lyrics" 
                            style="display: none;"
                        >{{ old('Lyrics') }}</textarea>
                    </div>

                    <!-- Note about status -->
                    <div class="bg-yellow-500/20 border border-yellow-500/50 rounded-lg p-4">
                        <p class="text-sm text-yellow-400">
                            <strong>Note:</strong> All uploaded songs will be set to <strong>Pending</strong> status by default. 
                            You can edit each song individually later to approve and make them public.
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
        </div>
    </div>
</div>

<!-- Quill.js WYSIWYG Editor (Free & Open Source) -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<style>
    .ql-editor {
        min-height: 300px;
        background-color: #18181b;
        color: #fff;
    }
    .ql-container {
        background-color: #18181b;
        color: #fff;
        border-color: #3f3f46;
    }
    .ql-toolbar {
        background-color: #27272a;
        border-color: #3f3f46;
    }
    .ql-toolbar .ql-stroke {
        stroke: #fff;
    }
    .ql-toolbar .ql-fill {
        fill: #fff;
    }
    .ql-toolbar .ql-picker-label {
        color: #fff;
    }
    .ql-toolbar button:hover, .ql-toolbar button.ql-active {
        color: #22c55e;
    }
    .ql-toolbar .ql-stroke.ql-thin {
        stroke: #fff;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Quill for Description
    if (document.getElementById('Description')) {
        const descriptionQuill = new Quill('#Description', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['link'],
                    ['clean']
                ]
            },
            placeholder: 'Enter song description'
        });
        
        // Sync Quill content to textarea before form submission
        const descriptionForm = document.querySelector('form');
        if (descriptionForm) {
            descriptionForm.addEventListener('submit', function() {
                const descriptionInput = document.querySelector('textarea[name="Description"]');
                if (descriptionInput) {
                    descriptionInput.value = descriptionQuill.root.innerHTML;
                }
            });
        }
    }
    
    // Initialize Quill for Lyrics
    if (document.getElementById('Lyrics')) {
        const lyricsQuill = new Quill('#Lyrics', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['link'],
                    ['clean']
                ]
            },
            placeholder: 'Enter song lyrics'
        });
        
        // Sync Quill content to textarea before form submission
        const lyricsForm = document.querySelector('form');
        if (lyricsForm) {
            lyricsForm.addEventListener('submit', function() {
                const lyricsInput = document.querySelector('textarea[name="Lyrics"]');
                if (lyricsInput) {
                    lyricsInput.value = lyricsQuill.root.innerHTML;
                }
            });
        }
    }
    
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
            addFiles(Array.from(e.target.files));
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
            files.forEach(file => {
                // Check if file is not already in the array
                const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size && f.lastModified === file.lastModified);
                if (!exists) {
                    // Validate file type
                    if (file.type === 'audio/mpeg' || file.type === 'audio/mp3' || file.name.toLowerCase().endsWith('.mp3')) {
                        selectedFiles.push(file);
                    }
                }
            });
            updateFileList();
        }
        
        function updateFileList() {
            fileList.innerHTML = '';
            
            // Update the file input with remaining files
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => {
                dataTransfer.items.add(file);
            });
            audioInput.files = dataTransfer.files;
            
            if (selectedFiles.length > 0) {
                const fileCount = document.createElement('div');
                fileCount.className = 'text-sm text-white font-medium mb-2';
                fileCount.textContent = `Selected ${selectedFiles.length} file(s):`;
                fileList.appendChild(fileCount);
                
                selectedFiles.forEach((file, index) => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'flex items-start gap-3 bg-zinc-800 rounded-lg p-3';
                    fileItem.setAttribute('data-file-index', index);
                    fileItem.innerHTML = `
                        <div class="flex-1 min-w-0">
                            <div class="text-sm text-white break-words">${escapeHtml(file.name)}</div>
                            <div class="text-xs text-zinc-400 mt-1">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
                            <div class="text-xs text-zinc-400 mt-1 break-words">
                                Song name: ${escapeHtml(file.name.replace(/\.[^/.]+$/, '').replace(/[-_]/g, ' '))}
                            </div>
                        </div>
                        <button 
                            type="button" 
                            class="remove-file-btn flex-shrink-0 text-red-400 hover:text-red-300 transition-colors p-1"
                            data-file-index="${index}"
                            aria-label="Remove file"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    `;
                    fileList.appendChild(fileItem);
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
            }
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    }
});
</script>
@endsection

