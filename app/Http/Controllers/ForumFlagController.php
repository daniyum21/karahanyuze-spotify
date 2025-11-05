<?php

namespace App\Http\Controllers;

use App\Models\ForumThread;
use App\Models\ForumComment;
use App\Models\ForumFlag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumFlagController extends Controller
{
    /**
     * Flag a thread or comment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'flaggable_type' => 'required|in:thread,comment',
            'flaggable_id' => 'required|integer',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if already flagged by this user
        $existingFlag = ForumFlag::where('UserID', Auth::id())
            ->where('flaggable_type', $validated['flaggable_type'])
            ->where('flaggable_id', $validated['flaggable_id'])
            ->where('is_resolved', false)
            ->first();

        if ($existingFlag) {
            return back()->with('error', 'You have already flagged this item.');
        }

        // Verify the flaggable item exists
        if ($validated['flaggable_type'] === 'thread') {
            $item = ForumThread::findOrFail($validated['flaggable_id']);
        } else {
            $item = ForumComment::findOrFail($validated['flaggable_id']);
        }

        ForumFlag::create([
            'UserID' => Auth::id(),
            'flaggable_type' => $validated['flaggable_type'],
            'flaggable_id' => $validated['flaggable_id'],
            'reason' => $validated['reason'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Item flagged successfully. An admin will review it.');
    }
}
