@props([
    'songId' => null,
    'route' => null,
    'size' => 'default', // 'default' or 'icon'
])

@php
    if ($route) {
        $downloadUrl = route($route, $songId);
    } else {
        $downloadUrl = route('indirimbo.download', $songId);
    }
@endphp

@if($size === 'icon')
<button onclick="window.location.href='{{ $downloadUrl }}'" class="p-3 hover:bg-zinc-600 rounded-lg transition-colors min-w-[44px] min-h-[44px] flex items-center justify-center" title="Download">
    <svg class="w-6 h-6 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
    </svg>
</button>
@else
<button 
    class="px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold rounded-lg transition-colors flex items-center gap-2"
    onclick="window.location.href='{{ $downloadUrl }}'"
>
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
    </svg>
    Download
</button>
@endif

