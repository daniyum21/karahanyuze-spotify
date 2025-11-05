@extends('layouts.app')

@section('title', 'Upload Song - Admin')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Upload New Song</h1>
                <p class="text-zinc-400">Add a new song to the Karahanyuze collection</p>
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

            <form action="{{ route('admin.songs.store') }}" method="POST" enctype="multipart/form-data" class="bg-zinc-900 rounded-lg p-8">
                @csrf

                <div class="space-y-6">
                    <!-- Song Name -->
                    <div>
                        <label for="IndirimboName" class="block text-sm font-medium text-white mb-2">Song Name *</label>
                        <input 
                            type="text" 
                            id="IndirimboName" 
                            name="IndirimboName" 
                            value="{{ old('IndirimboName') }}"
                            required
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Enter song name"
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

                    <!-- Owner Selection (Artist, Orchestra, or Itorero) -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Owner *</label>
                        <div class="space-y-3">
                            <div>
                                <label for="UmuhanziID" class="block text-sm text-zinc-400 mb-1">Artist</label>
                                <select 
                                    id="UmuhanziID" 
                                    name="UmuhanziID"
                                    class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                >
                                    <option value="">Select an artist</option>
                                    @foreach($artists as $artist)
                                        <option value="{{ $artist->UmuhanziID }}" {{ (old('UmuhanziID') == $artist->UmuhanziID || (isset($selectedArtist) && $selectedArtist && $selectedArtist->UmuhanziID == $artist->UmuhanziID)) ? 'selected' : '' }}>
                                            {{ $artist->StageName }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(isset($selectedArtist) && $selectedArtist)
                                <p class="text-xs text-green-400 mt-1">✓ Artist "{{ $selectedArtist->StageName }}" is pre-selected</p>
                                @endif
                            </div>

                            <div>
                                <label for="OrchestreID" class="block text-sm text-zinc-400 mb-1">Orchestra</label>
                                <select 
                                    id="OrchestreID" 
                                    name="OrchestreID"
                                    class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                >
                                    <option value="">Select an orchestra</option>
                                    @foreach($orchestras as $orchestra)
                                        <option value="{{ $orchestra->OrchestreID }}" {{ (old('OrchestreID') == $orchestra->OrchestreID || (isset($selectedOrchestra) && $selectedOrchestra && $selectedOrchestra->OrchestreID == $orchestra->OrchestreID)) ? 'selected' : '' }}>
                                            {{ $orchestra->OrchestreName }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(isset($selectedOrchestra) && $selectedOrchestra)
                                <p class="text-xs text-green-400 mt-1">✓ Orchestra "{{ $selectedOrchestra->OrchestreName }}" is pre-selected</p>
                                @endif
                            </div>

                            <div>
                                <label for="ItoreroID" class="block text-sm text-zinc-400 mb-1">Itorero</label>
                                <select 
                                    id="ItoreroID" 
                                    name="ItoreroID"
                                    class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                >
                                    <option value="">Select an itorero</option>
                                    @foreach($itoreros as $itorero)
                                        <option value="{{ $itorero->ItoreroID }}" {{ (old('ItoreroID') == $itorero->ItoreroID || (isset($selectedItorero) && $selectedItorero && $selectedItorero->ItoreroID == $itorero->ItoreroID)) ? 'selected' : '' }}>
                                            {{ $itorero->ItoreroName }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(isset($selectedItorero) && $selectedItorero)
                                <p class="text-xs text-green-400 mt-1">✓ Itorero "{{ $selectedItorero->ItoreroName }}" is pre-selected</p>
                                @endif
                            </div>
                        </div>
                        <p class="text-xs text-zinc-500 mt-2">Select one owner (artist, orchestra, or itorero)</p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="StatusID" class="block text-sm font-medium text-white mb-2">Status *</label>
                        <select 
                            id="StatusID" 
                            name="StatusID"
                            required
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        >
                            <option value="">Select status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->StatusID }}" {{ old('StatusID') == $status->StatusID ? 'selected' : '' }}>
                                    {{ $status->StatusName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Featured -->
                    <div>
                        <label class="flex items-center gap-3">
                            <input 
                                type="checkbox" 
                                name="IsFeatured" 
                                value="1"
                                {{ old('IsFeatured') ? 'checked' : '' }}
                                class="w-5 h-5 text-green-500 bg-zinc-800 border-zinc-700 rounded focus:ring-green-500"
                            >
                            <span class="text-white">Featured Song</span>
                        </label>
                    </div>

                    <!-- Audio File -->
                    <div>
                        <label for="audio" class="block text-sm font-medium text-white mb-2">Audio File (MP3)</label>
                        <input 
                            type="file" 
                            id="audio" 
                            name="audio"
                            accept="audio/mpeg,audio/mp3"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-500 file:text-white hover:file:bg-green-600"
                        >
                        <p class="text-xs text-zinc-500 mt-2">Maximum file size: 50MB</p>
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
                            Upload Song
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
});
</script>
@endsection

