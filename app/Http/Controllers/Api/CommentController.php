<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Http\Resources\CommentResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    /**
     * Display comments for a post.
     */
    public function index(Post $post): JsonResponse
    {
        $comments = $post->comments()
            ->with(['user.profile', 'replies.user.profile'])
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => CommentResource::collection($comments),
        ]);
    }

    /**
     * Store a newly created comment.
     */
    public function store(Request $request, Post $post): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
            'parent_id' => ['nullable', 'exists:comments,id'],
        ]);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        // Update comments count
        $post->increment('comments_count');

        // Notify post author and mentioned users
        // TODO: Implement notification

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully',
            'data' => new CommentResource($comment->load('user.profile')),
        ], 201);
    }

    /**
     * Display the specified comment.
     */
    public function show(Comment $comment): JsonResponse
    {
        $comment->load(['user.profile', 'replies.user.profile']);

        return response()->json([
            'success' => true,
            'data' => new CommentResource($comment),
        ]);
    }

    /**
     * Update the specified comment.
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
        ]);

        $comment->update([
            'content' => $validated['content'],
            'is_edited' => true,
            'edited_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment updated successfully',
            'data' => new CommentResource($comment->fresh()),
        ]);
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $post = $comment->post;
        $comment->delete();

        $post->decrement('comments_count');

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully',
        ]);
    }

    /**
     * Accept comment as best answer.
     */
    public function acceptAsAnswer(Request $request, Comment $comment): JsonResponse
    {
        $post = $comment->post;

        $this->authorize('acceptAnswer', $post);

        // Remove previous best answer
        $post->comments()->where('is_best_answer', true)->update(['is_best_answer' => false]);

        // Set new best answer
        $comment->update(['is_best_answer' => true]);

        // Mark post as solved
        $post->update([
            'is_solved' => true,
            'solved_at' => now(),
        ]);

        // Award reputation points
        $comment->user->increment('reputation', 15);

        return response()->json([
            'success' => true,
            'message' => 'Answer accepted successfully',
            'data' => new CommentResource($comment->fresh()),
        ]);
    }

    /**
     * Get replies for a comment.
     */
    public function replies(Comment $comment): JsonResponse
    {
        $replies = $comment->replies()
            ->with('user.profile')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => CommentResource::collection($replies),
        ]);
    }
}
