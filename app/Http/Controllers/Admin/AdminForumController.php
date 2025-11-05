<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumThread;
use App\Models\ForumComment;
use App\Models\ForumFlag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminForumController extends Controller
{
    /**
     * Display admin forum dashboard
     */
    public function index()
    {
        $threads = ForumThread::with(['user', 'lastCommentUser'])
            ->withCount('allComments')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $pendingThreads = ForumThread::where('is_approved', false)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Load flags and manually attach flaggable relationships
        // (Cannot use eager loading with custom flaggable() method)
        $unresolvedFlags = ForumFlag::where('is_resolved', false)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Manually load flaggable relationships
        foreach ($unresolvedFlags as $flag) {
            $flag->flaggable = $flag->getFlaggableAttribute();
        }
        
        return view('admin.forum.index', compact('threads', 'pendingThreads', 'unresolvedFlags'));
    }

    /**
     * Delete a thread
     */
    public function destroyThread($threadId)
    {
        $thread = ForumThread::findOrFail($threadId);
        $thread->delete();

        return back()->with('success', 'Thread deleted successfully!');
    }

    /**
     * Delete a comment
     */
    public function destroyComment($commentId)
    {
        $comment = ForumComment::with('thread')->findOrFail($commentId);
        $thread = $comment->thread;
        $comment->delete();
        
        // Update thread comment count
        $thread->updateCommentCount();

        return back()->with('success', 'Comment deleted successfully!');
    }

    /**
     * Lock/unlock a thread
     */
    public function toggleLock($threadId)
    {
        $thread = ForumThread::findOrFail($threadId);
        $thread->is_locked = !$thread->is_locked;
        $thread->save();

        $status = $thread->is_locked ? 'locked' : 'unlocked';
        return back()->with('success', "Thread {$status} successfully!");
    }

    /**
     * Pin/unpin a thread
     */
    public function togglePin($threadId)
    {
        $thread = ForumThread::findOrFail($threadId);
        $thread->is_pinned = !$thread->is_pinned;
        $thread->save();

        $status = $thread->is_pinned ? 'pinned' : 'unpinned';
        return back()->with('success', "Thread {$status} successfully!");
    }

    /**
     * Resolve a flag
     */
    public function resolveFlag($flagId)
    {
        $flag = ForumFlag::findOrFail($flagId);
        $flag->resolve(Auth::id());

        return back()->with('success', 'Flag resolved successfully!');
    }

    /**
     * Approve a thread
     */
    public function approveThread($threadId)
    {
        $thread = ForumThread::findOrFail($threadId);
        $thread->is_approved = true;
        $thread->approved_by = Auth::id();
        $thread->approved_at = now();
        $thread->save();

        return back()->with('success', 'Thread approved successfully!');
    }

    /**
     * Reject/Delete a pending thread
     */
    public function rejectThread($threadId)
    {
        $thread = ForumThread::findOrFail($threadId);
        $thread->delete();

        return back()->with('success', 'Thread rejected and deleted successfully!');
    }

    /**
     * Approve a comment
     */
    public function approveComment($commentId)
    {
        $comment = ForumComment::findOrFail($commentId);
        $comment->is_approved = true;
        $comment->approved_by = Auth::id();
        $comment->approved_at = now();
        $comment->save();

        // Update thread comment count
        $comment->thread->updateCommentCount();

        return back()->with('success', 'Comment approved successfully!');
    }

    /**
     * Reject/Delete a flagged comment
     */
    public function rejectComment($commentId)
    {
        $comment = ForumComment::with('thread')->findOrFail($commentId);
        $thread = $comment->thread;
        $comment->delete();

        // Update thread comment count
        $thread->updateCommentCount();

        return back()->with('success', 'Comment rejected and deleted successfully!');
    }

    /**
     * View all flags
     */
    public function flags()
    {
        $flags = ForumFlag::with(['user', 'resolvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Manually load flaggable relationships with their related data
        foreach ($flags as $flag) {
            if ($flag->flaggable_type === 'thread') {
                $flag->flaggable = ForumThread::find($flag->flaggable_id);
            } elseif ($flag->flaggable_type === 'comment') {
                // Load comment with its thread relationship
                $flag->flaggable = ForumComment::with('thread')->find($flag->flaggable_id);
            }
        }

        return view('admin.forum.flags', compact('flags'));
    }
}
