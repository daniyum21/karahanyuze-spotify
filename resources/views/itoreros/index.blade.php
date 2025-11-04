@extends('layouts.app')

@section('title', 'Itorero - Karahanyuze')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-black via-blue-950 to-black">
    <div class="container mx-auto px-4 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Itorero</h1>
            <p class="text-zinc-400">All itorero in the Karahanyuze collection</p>
        </div>

        @if($itoreros->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($itoreros as $itorero)
            <a href="{{ route('itorero.show', $itorero->slug) }}" class="bg-zinc-800 rounded-lg p-4 hover:bg-zinc-700 transition-colors block group">
                <div class="mb-4">
                    <img 
                        src="{{ \App\Helpers\ImageHelper::getImageUrl($itorero->ProfilePicture) }}" 
                        alt="{{ $itorero->ItoreroName }}"
                        class="w-full aspect-square object-cover rounded-lg"
                    />
                </div>
                <h3 class="font-semibold text-white mb-1">{{ $itorero->ItoreroName }}</h3>
                <p class="text-sm text-zinc-400">{{ $itorero->songs_count ?? 0 }} {{ $itorero->songs_count == 1 ? 'song' : 'songs' }}</p>
            </a>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $itoreros->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-zinc-400 text-lg">No itorero found.</p>
        </div>
        @endif
    </div>
</div>
@endsection

