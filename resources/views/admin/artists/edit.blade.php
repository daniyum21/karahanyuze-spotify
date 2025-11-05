@extends('layouts.app')

@section('title', 'Edit Artist - Admin')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Edit Artist</h1>
                <p class="text-zinc-400">Update artist information in the Karahanyuze collection</p>
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

            <form action="{{ route('admin.artists.update', $artist->UUID) }}" method="POST" enctype="multipart/form-data" class="bg-zinc-900 rounded-lg p-8">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- First Name -->
                    <div>
                        <label for="FirstName" class="block text-sm font-medium text-white mb-2">First Name (Optional)</label>
                        <input 
                            type="text" 
                            id="FirstName" 
                            name="FirstName" 
                            value="{{ old('FirstName', $artist->FirstName) }}"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Enter first name (optional)"
                        >
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="LastName" class="block text-sm font-medium text-white mb-2">Last Name (Optional)</label>
                        <input 
                            type="text" 
                            id="LastName" 
                            name="LastName" 
                            value="{{ old('LastName', $artist->LastName) }}"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Enter last name (optional)"
                        >
                    </div>

                    <!-- Stage Name -->
                    <div>
                        <label for="StageName" class="block text-sm font-medium text-white mb-2">Stage Name *</label>
                        <input 
                            type="text" 
                            id="StageName" 
                            name="StageName" 
                            value="{{ old('StageName', $artist->StageName) }}"
                            required
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Enter stage name"
                        >
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="Email" class="block text-sm font-medium text-white mb-2">Email (Optional)</label>
                        <input 
                            type="email" 
                            id="Email" 
                            name="Email" 
                            value="{{ old('Email', $artist->Email) }}"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Enter email address (optional)"
                        >
                    </div>

                    <!-- Twitter -->
                    <div>
                        <label for="Twitter" class="block text-sm font-medium text-white mb-2">Twitter Account (Optional)</label>
                        <input 
                            type="text" 
                            id="Twitter" 
                            name="Twitter" 
                            value="{{ old('Twitter', $artist->Twitter) }}"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Enter Twitter handle (optional)"
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
                            placeholder="Enter artist description"
                        >{{ old('Description', $artist->Description) }}</textarea>
                    </div>

                    <!-- Featured -->
                    <div>
                        <label class="flex items-center gap-3">
                            <input 
                                type="checkbox" 
                                name="IsFeatured" 
                                value="1"
                                {{ old('IsFeatured', $artist->IsFeatured) ? 'checked' : '' }}
                                class="w-5 h-5 text-green-500 bg-zinc-800 border-zinc-700 rounded focus:ring-green-500"
                            >
                            <span class="text-white">Featured Artist</span>
                        </label>
                    </div>

                    <!-- Current Image -->
                    @if($artist->ProfilePicture)
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Current Profile Picture</label>
                        <div class="mb-4">
                            <img 
                                src="{{ \App\Helpers\ImageHelper::getImageUrl($artist->ProfilePicture) }}" 
                                alt="{{ $artist->StageName }}"
                                class="w-32 h-32 rounded-lg object-cover border border-zinc-700"
                            >
                        </div>
                    </div>
                    @endif

                    <!-- Image File -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-white mb-2">
                            {{ $artist->ProfilePicture ? 'Change Profile Picture (Optional)' : 'Profile Picture (Optional)' }}
                        </label>
                        <input 
                            type="file" 
                            id="image" 
                            name="image"
                            accept="image/jpeg,image/jpg,image/png"
                            class="w-full px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-500 file:text-white hover:file:bg-green-600"
                        >
                        <p class="text-xs text-zinc-500 mt-2">Maximum file size: 2MB. Leave empty to keep current image.</p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button 
                            type="submit" 
                            class="flex-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
                        >
                            Update Artist
                        </button>
                        <a 
                            href="{{ route('admin.artists.index') }}" 
                            class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors text-center"
                        >
                            Cancel
                        </a>
                    </div>
                </div>
            </form>

            <!-- Current Artist Songs -->
            @if($artist->songs->count() > 0)
            <div class="mt-12">
                <label class="block text-sm font-medium text-white mb-2">Songs by {{ $artist->StageName }}</label>
                <div class="max-h-64 overflow-y-auto bg-zinc-800 border border-zinc-700 rounded-lg p-4 mb-4">
                    <div class="space-y-2">
                        @foreach($artist->songs as $artistSong)
                        <div class="flex items-center justify-between gap-3 p-3 bg-zinc-700 rounded-lg">
                            <div class="flex items-center gap-3 flex-1">
                                <input 
                                    type="checkbox" 
                                    name="songs[]" 
                                    value="{{ $artistSong->IndirimboID }}"
                                    checked
                                    class="w-5 h-5 text-green-500 bg-zinc-800 border-zinc-700 rounded focus:ring-green-500"
                                >
                                <div class="flex-1">
                                    <div class="text-white font-medium">{{ $artistSong->IndirimboName }}</div>
                                    @if($artistSong->artist)
                                        <div class="text-sm text-zinc-400">{{ $artistSong->artist->StageName }}</div>
                                    @endif
                                </div>
                            </div>
                            <button 
                                type="button" 
                                onclick="removeSongFromArtist(this, '{{ $artistSong->IndirimboID }}')"
                                class="text-red-400 hover:text-red-300 transition-colors text-sm font-medium px-3 py-1"
                                title="Remove from artist"
                            >
                                Remove
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                <p class="text-xs text-zinc-500 mb-4">Uncheck a song or click "Remove" to remove it from this artist. This does not delete the song.</p>
            </div>
            @endif

            <!-- Available Songs to Add -->
            <div class="mt-6">
                <label for="songs" class="block text-sm font-medium text-white mb-2">Add More Songs (Optional)</label>
                <div class="max-h-96 overflow-y-auto bg-zinc-800 border border-zinc-700 rounded-lg p-4">
                    @if($allSongs->count() > 0)
                        <div class="space-y-2">
                            @foreach($allSongs as $song)
                                @if(!in_array($song->IndirimboID, $artistSongIds))
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
                        @if($allSongs->whereNotIn('IndirimboID', $artistSongIds)->count() === 0)
                            <p class="text-zinc-400 text-center py-4">All available songs are already assigned to this artist.</p>
                        @endif
                    @else
                        <p class="text-zinc-400 text-center py-4">No songs available. Create some songs first.</p>
                    @endif
                </div>
                <p class="text-xs text-zinc-500 mt-2">Check songs to add them to this artist</p>
            </div>
        </div>
    </div>
</div>

<script>
function removeSongFromArtist(button, songId) {
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

