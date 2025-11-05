@extends('layouts.app')

@section('title', 'My Submissions - Karahanyuze')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black">
    <div class="container mx-auto px-4 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">My Submissions</h1>
            <p class="text-zinc-400">View the status of all your submitted content</p>
        </div>

        <!-- Tabs -->
        <div class="mb-6 flex flex-wrap gap-4 border-b border-zinc-800">
            <button onclick="showTab('songs')" class="tab-button px-4 py-2 text-white border-b-2 border-green-500 font-medium" data-tab="songs">
                Songs ({{ $songs->total() }})
            </button>
            <button onclick="showTab('artists')" class="tab-button px-4 py-2 text-zinc-400 hover:text-white border-b-2 border-transparent font-medium" data-tab="artists">
                Artists ({{ $artists->count() }})
            </button>
            <button onclick="showTab('orchestras')" class="tab-button px-4 py-2 text-zinc-400 hover:text-white border-b-2 border-transparent font-medium" data-tab="orchestras">
                Orchestras ({{ $orchestras->count() }})
            </button>
            <button onclick="showTab('itoreros')" class="tab-button px-4 py-2 text-zinc-400 hover:text-white border-b-2 border-transparent font-medium" data-tab="itoreros">
                Itoreros ({{ $itoreros->count() }})
            </button>
        </div>

        <!-- Songs Tab -->
        <div id="tab-songs" class="tab-content">
            @if($songs->count() > 0)
            <div class="bg-zinc-900 rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-zinc-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Song</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Submitted</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800">
                        @foreach($songs as $song)
                        <tr class="hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-white font-medium">{{ $song->IndirimboName }}</div>
                                @if($song->artist)
                                <div class="text-sm text-zinc-400">Artist: {{ $song->artist->StageName }}</div>
                                @elseif($song->orchestra)
                                <div class="text-sm text-zinc-400">Orchestra: {{ $song->orchestra->OrchestreName }}</div>
                                @elseif($song->itorero)
                                <div class="text-sm text-zinc-400">Itorero: {{ $song->itorero->ItoreroName }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($song->isApproved())
                                <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Approved</span>
                                @elseif($song->isDeclined())
                                <span class="px-2 py-1 bg-red-500/20 text-red-400 rounded text-xs font-semibold">Declined</span>
                                @else
                                <span class="px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded text-xs font-semibold">Pending</span>
                                @endif
                                @if($song->isDeclined() && $song->declined_reason)
                                <div class="mt-2 text-xs text-red-400">
                                    <strong>Reason:</strong> {{ $song->declined_reason }}
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-zinc-300 text-sm">
                                {{ $song->created_at ? $song->created_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($song->isApproved())
                                <a href="{{ route('indirimbo.show', [$song->slug, $song->UUID]) }}" class="text-green-400 hover:text-green-300 text-sm mr-3" target="_blank">
                                    View Public →
                                </a>
                                @elseif(!$song->isApproved() && !$song->isDeclined())
                                <a href="{{ route('user.songs.edit', $song->UUID) }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                                    Edit/Rename →
                                </a>
                                <span class="text-zinc-500 text-sm ml-3">Awaiting Review</span>
                                @else
                                <span class="text-zinc-500 text-sm">Cannot Edit</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">
                {{ $songs->links() }}
            </div>
            @else
            <div class="text-center py-12 bg-zinc-900 rounded-lg">
                <p class="text-zinc-400 text-lg mb-4">No songs submitted yet</p>
                <a href="{{ route('user.songs.create') }}" class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    Upload a Song
                </a>
            </div>
            @endif
        </div>

        <!-- Artists Tab -->
        <div id="tab-artists" class="tab-content hidden">
            @if($artists->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($artists as $artist)
                <div class="bg-zinc-900 rounded-lg p-6">
                    <h3 class="text-white font-semibold mb-2">{{ $artist->StageName }}</h3>
                    <div class="mb-4">
                        @if($artist->isDeclined())
                        <span class="px-2 py-1 bg-red-500/20 text-red-400 rounded text-xs font-semibold">Declined</span>
                        @if($artist->declined_reason)
                        <div class="mt-2 text-xs text-red-400">
                            <strong>Reason:</strong> {{ $artist->declined_reason }}
                        </div>
                        @endif
                        @else
                        <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Approved</span>
                        @endif
                    </div>
                    <p class="text-xs text-zinc-500">Submitted {{ $artist->created_at ? $artist->created_at->format('M d, Y') : 'N/A' }}</p>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12 bg-zinc-900 rounded-lg">
                <p class="text-zinc-400 text-lg mb-4">No artists submitted yet</p>
            </div>
            @endif
        </div>

        <!-- Orchestras Tab -->
        <div id="tab-orchestras" class="tab-content hidden">
            @if($orchestras->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($orchestras as $orchestra)
                <div class="bg-zinc-900 rounded-lg p-6">
                    <h3 class="text-white font-semibold mb-2">{{ $orchestra->OrchestreName }}</h3>
                    <div class="mb-4">
                        @if($orchestra->isDeclined())
                        <span class="px-2 py-1 bg-red-500/20 text-red-400 rounded text-xs font-semibold">Declined</span>
                        @if($orchestra->declined_reason)
                        <div class="mt-2 text-xs text-red-400">
                            <strong>Reason:</strong> {{ $orchestra->declined_reason }}
                        </div>
                        @endif
                        @else
                        <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Approved</span>
                        @endif
                    </div>
                    <p class="text-xs text-zinc-500">Submitted {{ $orchestra->created_at ? $orchestra->created_at->format('M d, Y') : 'N/A' }}</p>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12 bg-zinc-900 rounded-lg">
                <p class="text-zinc-400 text-lg mb-4">No orchestras submitted yet</p>
            </div>
            @endif
        </div>

        <!-- Itoreros Tab -->
        <div id="tab-itoreros" class="tab-content hidden">
            @if($itoreros->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($itoreros as $itorero)
                <div class="bg-zinc-900 rounded-lg p-6">
                    <h3 class="text-white font-semibold mb-2">{{ $itorero->ItoreroName }}</h3>
                    <div class="mb-4">
                        @if($itorero->isDeclined())
                        <span class="px-2 py-1 bg-red-500/20 text-red-400 rounded text-xs font-semibold">Declined</span>
                        @if($itorero->declined_reason)
                        <div class="mt-2 text-xs text-red-400">
                            <strong>Reason:</strong> {{ $itorero->declined_reason }}
                        </div>
                        @endif
                        @else
                        <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs font-semibold">Approved</span>
                        @endif
                    </div>
                    <p class="text-xs text-zinc-500">Submitted {{ $itorero->created_at ? $itorero->created_at->format('M d, Y') : 'N/A' }}</p>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12 bg-zinc-900 rounded-lg">
                <p class="text-zinc-400 text-lg mb-4">No itoreros submitted yet</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function showTab(tab) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Show selected tab content
    const content = document.getElementById('tab-' + tab);
    if (content) {
        content.classList.remove('hidden');
    }
    
    // Update tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        const buttonTab = button.getAttribute('data-tab');
        if (buttonTab === tab) {
            button.classList.remove('text-zinc-400', 'border-transparent');
            button.classList.add('text-white', 'border-green-500');
        } else {
            button.classList.remove('text-white', 'border-green-500');
            button.classList.add('text-zinc-400', 'border-transparent');
        }
    });
}
</script>
@endsection

