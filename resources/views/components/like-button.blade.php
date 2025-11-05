@props([
    'entityType' => 'song', // song, playlist, artist, orchestra, itorero
    'entityId' => null,
    'isLiked' => false,
])

@auth
<button 
    id="like-btn"
    class="px-6 py-3 {{ $isLiked ? 'bg-pink-500 hover:bg-pink-600' : 'bg-zinc-800 hover:bg-zinc-700' }} text-white font-semibold rounded-lg transition-colors flex items-center gap-2"
    onclick="toggleLike()"
    data-entity-type="{{ $entityType }}"
    data-entity-id="{{ $entityId }}"
>
    <svg class="w-5 h-5" fill="{{ $isLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
    </svg>
    {{ $isLiked ? 'Liked' : 'Like' }}
</button>

<script>
function toggleLike() {
    @guest
    // Redirect to login if not authenticated
    window.location.href = '{{ route("login") }}';
    return;
    @endguest

    const likeBtn = document.getElementById('like-btn');
    if (!likeBtn) {
        console.error('Like button not found');
        return;
    }
    
    const svg = likeBtn.querySelector('svg');
    const entityType = likeBtn.getAttribute('data-entity-type');
    const entityId = likeBtn.getAttribute('data-entity-id');
    
    // Build the route URL based on entity type
    const toggleUrl = `{{ url('/favorites') }}/${entityType}/${entityId}/toggle`;
    
    // Disable button to prevent double-clicks
    likeBtn.disabled = true;
    const originalText = likeBtn.innerHTML;
    
    // Optimistic UI update - update immediately before server responds
    const currentIsLiked = likeBtn.classList.contains('bg-pink-500');
    const newIsLiked = !currentIsLiked;
    
    if (newIsLiked) {
        // Optimistically show as liked
        likeBtn.classList.add('bg-pink-500', 'hover:bg-pink-600');
        likeBtn.classList.remove('bg-zinc-800', 'hover:bg-zinc-700');
        svg.setAttribute('fill', 'currentColor');
        likeBtn.innerHTML = '<svg class="w-5 h-5" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg> Liked';
    } else {
        // Optimistically show as not liked
        likeBtn.classList.remove('bg-pink-500', 'hover:bg-pink-600');
        likeBtn.classList.add('bg-zinc-800', 'hover:bg-zinc-700');
        svg.setAttribute('fill', 'none');
        likeBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg> Like';
    }
    
    // Store original state in case we need to revert
    const originalIsLiked = currentIsLiked;
    
    // Send AJAX request to toggle favorite
    fetch(toggleUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        redirect: 'manual'
    })
    .then(async response => {
        // Handle redirects (302, 301, etc.)
        if (response.type === 'opaqueredirect' || response.status === 302 || response.status === 301) {
            if (typeof showErrorNotification === 'function') {
                showErrorNotification('Please log in to like items.');
            }
            setTimeout(() => {
                window.location.href = '{{ route("login") }}';
            }, 1500);
            return null;
        }
        
        // Handle authentication errors
        if (response.status === 401) {
            let message = 'Please log in to like items.';
            try {
                const errorData = await response.json();
                if (errorData.message) {
                    message = errorData.message;
                }
            } catch (e) {
                // Ignore JSON parse errors
            }
            if (typeof showErrorNotification === 'function') {
                showErrorNotification(message);
            }
            setTimeout(() => {
                window.location.href = '{{ route("login") }}';
            }, 1500);
            return null;
        }
        
        // Handle email verification errors
        if (response.status === 403) {
            let message = 'Please verify your email address.';
            let redirectUrl = '{{ route("verification.notice") }}';
            try {
                const errorData = await response.json();
                if (errorData.message) {
                    message = errorData.message;
                }
                if (errorData.redirect) {
                    redirectUrl = errorData.redirect;
                }
            } catch (e) {
                // Ignore JSON parse errors
            }
            if (typeof showErrorNotification === 'function') {
                showErrorNotification(message);
            }
            setTimeout(() => {
                window.location.href = redirectUrl;
            }, 1500);
            return null;
        }
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            // If not JSON, likely a redirect or HTML error page
            if (typeof showErrorNotification === 'function') {
                showErrorNotification('Please log in to like items.');
            }
            setTimeout(() => {
                window.location.href = '{{ route("login") }}';
            }, 1500);
            return null;
        }
        
        if (!response.ok) {
            // Try to get error message from response
            try {
                const errorData = await response.json();
                throw new Error(errorData.message || errorData.error || 'An error occurred');
            } catch (e) {
                if (e.message && e.message !== 'Unexpected token < in JSON at position 0') {
                    throw e;
                }
                throw new Error('An error occurred. Please try again.');
            }
        }
        
        return response.json();
    })
    .then(data => {
        if (!data) return; // Already handled redirect
        
        if (data.success) {
            // Server confirmed the state - update UI to match server response
            // (This should match our optimistic update, but we sync anyway)
            const isLiked = data.isFavorited;
            
            if (isLiked) {
                likeBtn.classList.add('bg-pink-500', 'hover:bg-pink-600');
                likeBtn.classList.remove('bg-zinc-800', 'hover:bg-zinc-700');
                svg.setAttribute('fill', 'currentColor');
                likeBtn.innerHTML = '<svg class="w-5 h-5" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg> Liked';
            } else {
                likeBtn.classList.remove('bg-pink-500', 'hover:bg-pink-600');
                likeBtn.classList.add('bg-zinc-800', 'hover:bg-zinc-700');
                svg.setAttribute('fill', 'none');
                likeBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg> Like';
            }
        } else if (data.error) {
            // Revert optimistic update on error
            if (originalIsLiked) {
                likeBtn.classList.add('bg-pink-500', 'hover:bg-pink-600');
                likeBtn.classList.remove('bg-zinc-800', 'hover:bg-zinc-700');
                svg.setAttribute('fill', 'currentColor');
                likeBtn.innerHTML = '<svg class="w-5 h-5" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg> Liked';
            } else {
                likeBtn.classList.remove('bg-pink-500', 'hover:bg-pink-600');
                likeBtn.classList.add('bg-zinc-800', 'hover:bg-zinc-700');
                svg.setAttribute('fill', 'none');
                likeBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg> Like';
            }
            
            if (typeof showErrorNotification === 'function') {
                showErrorNotification(data.message || data.error || 'An error occurred. Please try again.');
            }
        }
    })
    .catch(error => {
        console.error('Error toggling like:', error);
        
        // Revert optimistic update on error
        if (originalIsLiked) {
            likeBtn.classList.add('bg-pink-500', 'hover:bg-pink-600');
            likeBtn.classList.remove('bg-zinc-800', 'hover:bg-zinc-700');
            svg.setAttribute('fill', 'currentColor');
            likeBtn.innerHTML = '<svg class="w-5 h-5" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg> Liked';
        } else {
            likeBtn.classList.remove('bg-pink-500', 'hover:bg-pink-600');
            likeBtn.classList.add('bg-zinc-800', 'hover:bg-zinc-700');
            svg.setAttribute('fill', 'none');
            likeBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg> Like';
        }
        
        // Show user-friendly error message
        const message = error.message || 'An error occurred. Please try again.';
        if (typeof showErrorNotification === 'function') {
            showErrorNotification(message);
        }
        if (message.includes('log in') || message.includes('Unauthorized') || message.includes('login')) {
            setTimeout(() => {
                window.location.href = '{{ route("login") }}';
            }, 1500);
        }
    })
    .finally(() => {
        // Re-enable button
        likeBtn.disabled = false;
    });
}
</script>
@endauth

