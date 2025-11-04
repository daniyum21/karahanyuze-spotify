@extends('layouts.app')

@section('title', 'Artists - Admin')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black">
    <div class="container mx-auto px-4 py-12">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">Artists</h1>
                <p class="text-zinc-400">Manage all artists in the Karahanyuze collection</p>
            </div>
            <a 
                href="{{ route('admin.artists.create') }}" 
                class="bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Artist
            </a>
        </div>

        @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
        @endif

        @if($artists->count() > 0)
        <div class="bg-zinc-900 rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Artist</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Stage Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Songs</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Featured</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @foreach($artists as $artist)
                    <tr class="hover:bg-zinc-800/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-4">
                                @if($artist->ProfilePicture)
                                <img 
                                    src="{{ \App\Helpers\ImageHelper::getImageUrl($artist->ProfilePicture) }}" 
                                    alt="{{ $artist->StageName }}"
                                    class="w-12 h-12 rounded-full object-cover"
                                >
                                @else
                                <div class="w-12 h-12 rounded-full bg-zinc-700 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                @endif
                                <div>
                                    <div class="text-white font-medium">{{ $artist->FirstName }} {{ $artist->LastName }}</div>
                                    <div class="text-sm text-zinc-400">{{ $artist->created_at->format('M d, Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-white font-medium">{{ $artist->StageName }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-white">{{ $artist->Email ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-white">{{ $artist->songs_count ?? 0 }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($artist->IsFeatured)
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-500/20 text-blue-400">Featured</span>
                            @else
                                <span class="text-zinc-500 text-sm">No</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-3">
                                <a 
                                    href="{{ route('admin.artists.edit', $artist->UUID) }}" 
                                    class="text-green-400 hover:text-green-300 transition-colors"
                                >
                                    Edit
                                </a>
                                <form 
                                    action="{{ route('admin.artists.destroy', $artist->UUID) }}" 
                                    method="POST" 
                                    class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this artist?');"
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
            {{ $artists->links() }}
        </div>
        @else
        <div class="text-center py-12 bg-zinc-900 rounded-lg">
            <p class="text-zinc-400 text-lg mb-4">No artists found.</p>
            <a 
                href="{{ route('admin.artists.create') }}" 
                class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
            >
                Add Your First Artist
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

