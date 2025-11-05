@props([
    'title' => '',
    'url' => null,
])

@php
    $shareUrl = $url ?? url()->current();
    $shareTitle = $title ?: (config('app.name', 'Karahanyuze'));
@endphp

<div class="relative">
    <button 
        id="share-btn"
        class="px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold rounded-lg transition-colors flex items-center gap-2"
        onclick="toggleShareMenu()"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
        </svg>
        Share
    </button>
    <!-- Share Menu Dropdown -->
    <div id="share-menu" class="hidden absolute right-0 mt-2 w-48 bg-zinc-800 rounded-lg shadow-xl border border-zinc-700 z-50">
        <div class="py-2">
            <a href="#" onclick="shareToFacebook('{{ $shareUrl }}'); return false;" class="flex items-center gap-3 px-4 py-3 text-white hover:bg-zinc-700 transition-colors">
                <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                <span>Share on Facebook</span>
            </a>
            <a href="#" onclick="shareToTwitter('{{ $shareUrl }}', '{{ $shareTitle }}'); return false;" class="flex items-center gap-3 px-4 py-3 text-white hover:bg-zinc-700 transition-colors">
                <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
                <span>Share on X</span>
            </a>
            <a href="#" onclick="copyLink('{{ $shareUrl }}'); return false;" class="flex items-center gap-3 px-4 py-3 text-white hover:bg-zinc-700 transition-colors border-t border-zinc-700">
                <svg class="w-5 h-5 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <span>Copy Link</span>
            </a>
        </div>
    </div>
</div>

<script>
function toggleShareMenu() {
    const shareMenu = document.getElementById('share-menu');
    if (shareMenu) {
        shareMenu.classList.toggle('hidden');
    }
}

function shareToFacebook(url) {
    const encodedUrl = encodeURIComponent(url);
    // Facebook's sharer API doesn't support quote parameter anymore, but we can try to add it for context
    // The title will come from Open Graph meta tags on the page
    const title = '{{ $shareTitle }}';
    const encodedTitle = encodeURIComponent(title);
    // Note: Facebook's basic sharer only uses u parameter, title comes from OG tags
    // But we can add quote parameter for some context (though it may not work)
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}&quote=${encodedTitle}`, '_blank', 'width=600,height=400');
    document.getElementById('share-menu').classList.add('hidden');
}

function shareToTwitter(url, title) {
    const encodedUrl = encodeURIComponent(url);
    const encodedText = encodeURIComponent(title ? `${title} - Karahanyuze` : 'Karahanyuze');
    window.open(`https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedText}`, '_blank', 'width=600,height=400');
    document.getElementById('share-menu').classList.add('hidden');
}

function copyLink(url) {
    const urlToCopy = url || window.location.href;
    navigator.clipboard.writeText(urlToCopy).then(function() {
        // Show feedback
        const shareBtn = document.getElementById('share-btn');
        const originalText = shareBtn.innerHTML;
        shareBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Copied!';
        shareBtn.classList.add('bg-green-500');
        setTimeout(function() {
            shareBtn.innerHTML = originalText;
            shareBtn.classList.remove('bg-green-500');
        }, 2000);
        document.getElementById('share-menu').classList.add('hidden');
    }).catch(function() {
        if (typeof showErrorNotification === 'function') {
            showErrorNotification('Failed to copy link. Please copy manually: ' + urlToCopy);
        }
        document.getElementById('share-menu').classList.add('hidden');
    });
}

// Close share menu when clicking outside
document.addEventListener('click', function(event) {
    const shareBtn = document.getElementById('share-btn');
    const shareMenu = document.getElementById('share-menu');
    if (shareMenu && shareBtn && !shareMenu.contains(event.target) && !shareBtn.contains(event.target)) {
        shareMenu.classList.add('hidden');
    }
});
</script>

