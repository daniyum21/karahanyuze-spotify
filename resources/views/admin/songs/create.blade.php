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
                        <textarea 
                            id="Description" 
                            name="Description" 
                            rows="4"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Enter song description"
                        >{{ old('Description') }}</textarea>
                    </div>

                    <!-- Lyrics -->
                    <div>
                        <label for="Lyrics" class="block text-sm font-medium text-white mb-2">Lyrics</label>
                        <textarea 
                            id="Lyrics" 
                            name="Lyrics" 
                            rows="8"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Enter song lyrics"
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
                                        <option value="{{ $artist->UmuhanziID }}" {{ old('UmuhanziID') == $artist->UmuhanziID ? 'selected' : '' }}>
                                            {{ $artist->StageName }}
                                        </option>
                                    @endforeach
                                </select>
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
                                        <option value="{{ $orchestra->OrchestreID }}" {{ old('OrchestreID') == $orchestra->OrchestreID ? 'selected' : '' }}>
                                            {{ $orchestra->OrchestreName }}
                                        </option>
                                    @endforeach
                                </select>
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
                                        <option value="{{ $itorero->ItoreroID }}" {{ old('ItoreroID') == $itorero->ItoreroID ? 'selected' : '' }}>
                                            {{ $itorero->ItoreroName }}
                                        </option>
                                    @endforeach
                                </select>
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
@endsection

