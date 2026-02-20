<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VoteController extends Controller
{
    /**
     * Vote on a post.
     */
    public function votePost(Request $request, Post $post): JsonResponse
    {
        $validated = $request->validate([
            'vote' => ['required', 'in:up,down'],
        ]);

        $user = $request->user();
        $voteType = $validated['vote'] === 'up' ? 1 : -1;

        // Check for existing vote
        $existingVote = $post->votes()->where('user_id', $user->id)->first();

        if ($existingVote) {
            if ($existingVote->vote === $voteType) {
                // Remove vote if same
                $existingVote->delete();
                $post->decrement($voteType === 1 ? 'upvotes_count' : 'downvotes_count');
                $voted = false;
            } else {
                // Change vote
                $existingVote->update(['vote' => $voteType]);
                $post->decrement($voteType === 1 ? 'downvotes_count' : 'upvotes_count');
                $post->increment($voteType === 1 ? 'upvotes_count' : 'downvotes_count');
                $voted = true;
            }
        } else {
            // Create new vote
            $post->votes()->create([
                'user_id' => $user->id,
                'vote' => $voteType,
            ]);
            $post->increment($voteType === 1 ? 'upvotes_count' : 'downvotes_count');
            $voted = true;

            // Award reputation to post author
            if ($voteType === 1) {
                $post->user->increment('reputation', 1);
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'voted' => $voted,
                'vote' => $voted ? $voteType : null,
                'upvotes_count' => $post->fresh()->upvotes_count,
                'downvotes_count' => $post->fresh()->downvotes_count,
            ],
        ]);
    }

    /**
     * Vote on a comment.
     */
    public function voteComment(Request $request, Comment $comment): JsonResponse
    {
        $validated = $request->validate([
            'vote' => ['required', 'in:up,down'],
        ]);

        $user = $request->user();
        $voteType = $validated['vote'] === 'up' ? 1 : -1;

        // Check for existing vote
        $existingVote = $comment->votes()->where('user_id', $user->id)->first();

        if ($existingVote) {
            if ($existingVote->vote === $voteType) {
                // Remove vote if same
                $existingVote->delete();
                $comment->decrement('votes_count');
                $voted = false;
            } else {
                // Change vote
                $existingVote->update(['vote' => $voteType]);
                $voted = true;
            }
        } else {
            // Create new vote
            $comment->votes()->create([
                'user_id' => $user->id,
                'vote' => $voteType,
            ]);
            $comment->increment('votes_count');
            $voted = true;

            // Award reputation to comment author
            if ($voteType === 1) {
                $comment->user->increment('reputation', 1);
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'voted' => $voted,
                'vote' => $voted ? $voteType : null,
                'votes_count' => $comment->fresh()->votes_count,
            ],
        ]);
    }

    /**
     * Get user's vote on a post.
     */
    public function getPostVote(Request $request, Post $post): JsonResponse
    {
        $vote = $post->votes()->where('user_id', $request->user()->id)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'vote' => $vote ? $vote->vote : null,
            ],
        ]);
    }

    /**
     * Get user's vote on a comment.
     */
    public function getCommentVote(Request $request, Comment $comment): JsonResponse
    {
        $vote = $comment->votes()->where('user_id', $request->user()->id)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'vote' => $vote ? $vote->vote : null,
            ],
        ]);
    }
}