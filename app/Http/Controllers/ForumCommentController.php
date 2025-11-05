<?php

namespace App\Http\Controllers;

use App\Models\ForumThread;
use App\Models\ForumComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumCommentController extends Controller
{
    /**
     * Store a newly created comment
     */
    public function store(Request $request, $threadSlug)
    {
        $thread = ForumThread::where('slug', $threadSlug)->firstOrFail();
        
        // Check if thread is locked
        if ($thread->is_locked) {
            return back()->with('error', 'This thread is locked and cannot receive new comments.');
        }
        
        $validated = $request->validate([
            'body' => 'required|string|max:5000',
            'parent_id' => 'nullable|exists:forum_comments,CommentID',
        ]);

        $comment = ForumComment::create([
            'ThreadID' => $thread->ThreadID,
            'UserID' => Auth::id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'body' => $validated['body'],
            'is_approved' => true, // Comments are live by default
        ]);

        // Update thread comment count
        $thread->updateCommentCount();

        return back()->with('success', 'Comment posted successfully!')
            ->with('scroll_to', 'comment-' . $comment->CommentID);
    }

    /**
     * Show the form for editing a comment
     */
    public function edit($commentId)
    {
        $comment = ForumComment::with('thread')->findOrFail($commentId);
        
        // Only allow comment author or admin to edit
        if ($comment->UserID !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('forum.edit-comment', compact('comment'));
    }

    /**
     * Update the specified comment
     */
    public function update(Request $request, $commentId)
    {
        $comment = ForumComment::with('thread')->findOrFail($commentId);
        
        // Only allow comment author or admin to update
        if ($comment->UserID !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $comment->update([
            'body' => $validated['body'],
        ]);
        
        $comment->markAsEdited();

        return redirect()->route('forum.show', $comment->thread->slug)
            ->with('success', 'Comment updated successfully!')
            ->with('scroll_to', 'comment-' . $comment->CommentID);
    }

    /**
     * Remove the specified comment
     */
    public function destroy($commentId)
    {
        $comment = ForumComment::with('thread')->findOrFail($commentId);
        
        // Only allow comment author or admin to delete
        if ($comment->UserID !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $threadSlug = $comment->thread->slug;
        $comment->delete();
        
        // Update thread comment count
        $comment->thread->updateCommentCount();

        return redirect()->route('forum.show', $threadSlug)
            ->with('success', 'Comment deleted successfully!');
    }
}
