@extends('layouts.app')

@section('title', 'Songs - Admin')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black">
    <div class="container mx-auto px-4 py-12">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">Songs</h1>
                <p class="text-zinc-400">
                    @if($filterStatus === 'pending')
                        Pending Songs - Review and approve
                    @elseif($filterStatus === 'approved')
                        Approved Songs - Manage approved songs
                    @elseif($filterFeatured === 'yes')
                        Featured Songs - Add or remove featured status
                    @else
                        Manage all songs in the Karahanyuze collection
                    @endif
                </p>
            </div>
            <a 
                href="{{ route('admin.songs.create') }}" 
                class="bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Upload New Song
            </a>
        </div>

        <!-- Filter Tabs -->
        <div class="mb-6 flex gap-3 border-b border-zinc-700">
            <a href="{{ route('admin.songs.index') }}" class="px-4 py-2 {{ $filterStatus === 'all' && $filterFeatured === 'all' ? 'border-b-2 border-green-500 text-green-500' : 'text-zinc-400 hover:text-white' }} font-medium transition-colors">
                All Songs
            </a>
            <a href="{{ route('admin.songs.index', ['status' => 'pending']) }}" class="px-4 py-2 {{ $filterStatus === 'pending' ? 'border-b-2 border-yellow-500 text-yellow-500' : 'text-zinc-400 hover:text-white' }} font-medium transition-colors">
                Pending
            </a>
            <a href="{{ route('admin.songs.index', ['status' => 'approved']) }}" class="px-4 py-2 {{ $filterStatus === 'approved' ? 'border-b-2 border-green-500 text-green-500' : 'text-zinc-400 hover:text-white' }} font-medium transition-colors">
                Approved
            </a>
            <a href="{{ route('admin.songs.index', ['featured' => 'yes']) }}" class="px-4 py-2 {{ $filterFeatured === 'yes' ? 'border-b-2 border-purple-500 text-purple-500' : 'text-zinc-400 hover:text-white' }} font-medium transition-colors">
                Featured
            </a>
        </div>

        @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
        @endif

        @if($songs->count() > 0)
        <div class="bg-zinc-900 rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Song</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Owner</th>
                        @if($filterStatus === 'pending')
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Submitted By</th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Featured</th>
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
                                    class="w-12 h-12 rounded object-cover"
                                >
                                @else
                                <div class="w-12 h-12 rounded bg-zinc-700 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                    </svg>
                                </div>
                                @endif
                                <div>
                                    <div class="text-white font-medium">{{ $song->IndirimboName }}</div>
                                    <div class="text-sm text-zinc-400">{{ $song->created_at ? $song->created_at->format('M d, Y') : 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-white">
                                @if($song->artist)
                                    <span class="px-2 py-1 bg-purple-500/20 text-purple-400 rounded text-xs font-semibold mb-1 inline-block">Artist</span>
                                    <div class="text-sm">{{ $song->artist->StageName }}</div>
                                @elseif($song->orchestra)
                                    <span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-semibold mb-1 inline-block">Orchestra</span>
                                    <div class="text-sm">{{ $song->orchestra->OrchestreName }}</div>
                                @elseif($song->itorero)
                                    <span class="px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded text-xs font-semibold mb-1 inline-block">Itorero</span>
                                    <div class="text-sm">{{ $song->itorero->ItoreroName }}</div>
                                @else
                                    <span class="text-zinc-500">No owner</span>
                                @endif
                            </div>
                        </td>
                        @if($filterStatus === 'pending')
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-white">
                                @if($song->user)
                                    <div class="font-medium">{{ $song->user->PublicName ?? $song->user->FirstName . ' ' . $song->user->LastName }}</div>
                                    <div class="text-sm text-zinc-400">{{ $song->user->Email ?? '' }}</div>
                                @else
                                    <span class="text-zinc-500 text-sm">Unknown</span>
                                @endif
                            </div>
                        </td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($song->status)
                                <span class="px-2 py-1 text-xs rounded-full {{ $song->status->StatusID == 2 ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400' }}">
                                    {{ $song->status->StatusName }}
                                </span>
                            @else
                                <span class="text-zinc-500 text-sm">Unknown</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form action="{{ route('admin.songs.toggle-featured', $song->UUID) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="{{ $song->IsFeatured ? 'px-2 py-1 text-xs rounded-full bg-purple-500/20 text-purple-400 hover:bg-purple-500/30' : 'px-2 py-1 text-xs rounded-full bg-zinc-700/50 text-zinc-400 hover:bg-purple-500/20 hover:text-purple-400' }} transition-colors">
                                    {{ $song->IsFeatured ? 'Featured' : 'Not Featured' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-3">
                                @if($filterStatus === 'pending')
                                <a 
                                    href="{{ route('admin.songs.edit', $song->UUID) }}?status=pending" 
                                    class="text-green-400 hover:text-green-300 transition-colors"
                                >
                                    Review
                                </a>
                                @else
                                <a 
                                    href="{{ route('admin.songs.edit', $song->UUID) }}" 
                                    class="text-green-400 hover:text-green-300 transition-colors"
                                >
                                    Edit
                                </a>
                                @endif
                                <form 
                                    action="{{ route('admin.songs.destroy', $song->UUID) }}" 
                                    method="POST" 
                                    class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this song?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit" 
                                        class="text-red-400 hover:text-red-300 transition-colors"
                                    >
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $songs->links() }}
        </div>
        @else
        <div class="text-center py-12 bg-zinc-900 rounded-lg">
            <p class="text-zinc-400 text-lg mb-4">No songs found.</p>
            <a 
                href="{{ route('admin.songs.create') }}" 
                class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
            >
                Upload Your First Song
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

