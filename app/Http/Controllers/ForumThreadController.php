<?php

namespace App\Http\Controllers;

use App\Models\ForumThread;
use App\Models\ForumComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumThreadController extends Controller
{
    /**
     * Display a listing of forum threads
     */
    public function index(Request $request)
    {
        $query = ForumThread::with(['user', 'lastCommentUser'])
            ->withCount('allComments')
            ->where('is_approved', true); // Only show approved threads
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('body', 'like', $searchTerm);
            });
        }
        
        // Order by pinned first, then by last comment date
        $threads = $query->orderBy('is_pinned', 'desc')
            ->orderBy('last_comment_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();
        
        // Get pending threads for logged-in users (their own threads)
        $pendingThreads = collect();
        if (Auth::check()) {
            $pendingThreads = ForumThread::where('UserID', Auth::id())
                ->where('is_approved', false)
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return view('forum.index', compact('threads', 'pendingThreads'));
    }

    /**
     * Show the form for creating a new thread
     */
    public function create()
    {
        // Ensure user is verified
        if (!Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('error', 'Please verify your email address before creating a thread.');
        }
        
        return view('forum.create');
    }

    /**
     * Store a newly created thread
     */
    public function store(Request $request)
    {
        // Ensure user is verified
        if (!Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('error', 'Please verify your email address before creating a thread.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'nullable|string|max:10000',
        ]);

        $thread = ForumThread::create([
            'title' => $validated['title'],
            'body' => $validated['body'] ?? null,
            'UserID' => Auth::id(),
            'is_approved' => false, // Threads require admin approval
        ]);

        return redirect()->route('forum.index')
            ->with('success', 'Your thread has been submitted and is pending admin approval. You will be notified once it is approved.');
    }

    /**
     * Display the specified thread
     */
    public function show($slug)
    {
        $thread = ForumThread::where('slug', $slug)
            ->with(['user', 'lastCommentUser'])
            ->firstOrFail();
        
        // Only show approved threads to public, or allow author/admin to see their own pending threads
        if (!$thread->is_approved) {
            if (!Auth::check() || ($thread->UserID !== Auth::id() && !Auth::user()->isAdmin())) {
                abort(404, 'Thread not found');
            }
        }
        
        // Increment view count only for approved threads
        if ($thread->is_approved) {
            $thread->incrementViews();
        }
        
        // Load comments with nested replies (only approved comments)
        $comments = ForumComment::where('ThreadID', $thread->ThreadID)
            ->whereNull('parent_id')
            ->where('is_approved', true) // Only show approved comments
            ->with(['user', 'replies' => function($query) {
                $query->where('is_approved', true)->with('user');
            }, 'replies.replies' => function($query) {
                $query->where('is_approved', true)->with('user');
            }])
            ->orderBy('created_at', 'asc')
            ->get();
        
        return view('forum.show', compact('thread', 'comments'));
    }

    /**
     * Show the form for editing a thread
     */
    public function edit($slug)
    {
        $thread = ForumThread::where('slug', $slug)->firstOrFail();
        
        // Only allow thread author or admin to edit
        if ($thread->UserID !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('forum.edit', compact('thread'));
    }

    /**
     * Update the specified thread
     */
    public function update(Request $request, $slug)
    {
        $thread = ForumThread::where('slug', $slug)->firstOrFail();
        
        // Only allow thread author or admin to update
        if ($thread->UserID !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'nullable|string|max:10000',
        ]);

        $thread->update([
            'title' => $validated['title'],
            'body' => $validated['body'] ?? null,
        ]);

        return redirect()->route('forum.show', $thread->slug)
            ->with('success', 'Thread updated successfully!');
    }

    /**
     * Remove the specified thread
     */
    public function destroy($slug)
    {
        $thread = ForumThread::where('slug', $slug)->firstOrFail();
        
        // Only allow thread author or admin to delete
        if ($thread->UserID !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $thread->delete();

        return redirect()->route('forum.index')
            ->with('success', 'Thread deleted successfully!');
    }
}
