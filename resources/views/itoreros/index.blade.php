@extends('layouts.spotify')

@section('title', 'Itorero - Karahanyuze')

@section('content')
<div class="px-6 py-8 pb-24">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">Itorero</h1>
        <p class="text-zinc-400">All itorero in the Karahanyuze collection</p>
    </div>

    @if($itoreros->count() > 0)
    <div class="overflow-x-auto scrollbar-hide -mx-6 px-6">
        <div class="flex gap-4" style="width: max-content;">
            @foreach($itoreros as $itorero)
            @php
                $slug = $itorero->slug ?? \Illuminate\Support\Str::slug($itorero->ItoreroName);
            @endphp
            <div class="group flex-shrink-0 w-[180px]">
                <a href="{{ route('itorero.show', $slug) }}" class="block p-4 bg-zinc-900/50 hover:bg-zinc-800 rounded-lg transition-all duration-200 cursor-pointer">
                    <div class="relative mb-4">
                        <img 
                            src="{{ \App\Helpers\ImageHelper::getImageUrl($itorero->ProfilePicture) }}" 
                            alt="{{ $itorero->ItoreroName }}"
                            class="w-full aspect-square object-cover rounded-lg shadow-lg"
                            onerror="this.src='{{ asset('placeholder.svg') }}'"
                        />
                        <button 
                            onclick="event.preventDefault(); window.location.href='{{ route('itorero.show', $slug) }}';"
                            class="absolute bottom-2 right-2 w-12 h-12 bg-[#1db954] rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-lg hover:scale-110 z-10"
                        >
                            <svg class="w-6 h-6 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </button>
                    </div>
                    <h3 class="font-semibold text-white mb-1 truncate text-sm group-hover:underline">{{ $itorero->ItoreroName }}</h3>
                    <p class="text-xs text-zinc-400">{{ $itorero->songs_count ?? 0 }} songs</p>
                </a>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Pagination -->
    @if($itoreros->hasPages())
    <div class="mt-8">
        {{ $itoreros->links() }}
    </div>
    @endif
    @else
    <div class="text-center py-12 bg-zinc-900/50 rounded-lg">
        <p class="text-zinc-400 text-lg">No itorero found.</p>
    </div>
    @endif
</div>

<style>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
@endsection
