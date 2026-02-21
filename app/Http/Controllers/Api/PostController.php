<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Tag;
use App\Models\PostBookmark;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
  /**
   * Display a listing of posts.
   */
  public function index(Request $request)
  {
    $query = Post::with(['user', 'category', 'tags'])
      ->withCount(['comments', 'votes'])
      ->published();

    // Filter by category
    if ($request->has('category')) {
      $query->whereHas('category', function ($q) use ($request) {
        $q->where('slug', $request->category);
      });
    }

    // Filter by tag
    if ($request->has('tag')) {
      $query->whereHas('tags', function ($q) use ($request) {
        $q->where('slug', $request->tag);
      });
    }

    // Filter by type
    if ($request->has('type')) {
      $query->where('type', $request->type);
    }

    // Filter solved/unsolved
    if ($request->has('solved')) {
      if ($request->solved) {
        $query->whereNotNull('solved_at');
      } else {
        $query->whereNull('solved_at');
      }
    }

    // Sort
    $sort = $request->get('sort', 'recent');
    switch ($sort) {
      case 'popular':
        $query->popular();
        break;
      case 'most_discussed':
        $query->orderBy('comments_count', 'desc');
        break;
      default:
        $query->latest();
    }

    $posts = $query->paginate($request->get('per_page', 15));

    return $this->paginatedResponse($posts);
  }

  /**
   * Store a newly created post.
   */
  public function store(Request $request)
  {
    $request->validate([
      'title' => ['required', 'string', 'max:255'],
      'content' => ['required', 'string', 'min:20'],
      'category_id' => ['required', 'exists:categories,id'],
      'type' => ['required', 'in:question,discussion,announcement,tutorial'],
      'tags' => ['nullable', 'array'],
      'tags.*' => ['string', 'max:50'],
    ]);

    $post = Post::create([
      'user_id' => $request->user()->id,
      'category_id' => $request->category_id,
      'title' => $request->title,
      'slug' => Str::slug($request->title) . '-' . Str::random(6),
      'content' => $request->content,
      'excerpt' => Str::limit(strip_tags($request->content), 200),
      'type' => $request->type,
      'status' => 'published',
    ]);

    // Attach tags
    if ($request->has('tags')) {
      $tagIds = [];
      foreach ($request->tags as $tagName) {
        $tag = Tag::firstOrCreate(
          ['slug' => Str::slug($tagName)],
          ['name' => $tagName]
        );
        $tagIds[] = $tag->id;
        $tag->incrementUsage();
      }
      $post->tags()->attach($tagIds);
    }

    // Log activity
    ActivityLog::log('post', 'created', $post);

    // Award reputation
    $request->user()->addReputation(5, 'post_created', $post);

    return response()->json([
      'message' => 'Post created successfully',
      'post' => $post->load(['user', 'category', 'tags']),
    ], 201);
  }

  /**
   * Display the specified post.
   */
  public function show(Request $request, Post $post)
  {
    // Increment views
    $post->incrementViews();

    $post->load([
      'user.profile',
      'category',
      'tags',
      'comments.user.profile',
      'comments.replies.user.profile',
      'comments' => function ($query) {
        $query->topLevel()->withCount('votes')->orderBy('is_best_answer', 'desc')->orderBy('created_at');
      },
    ]);

    // Check if user has bookmarked
    $isBookmarked = false;
    $userVote = null;

    if ($request->user()) {
      $isBookmarked = $request->user()->savedPosts()->where('post_id', $post->id)->exists();
      $userVote = $post->votes()->where('user_id', $request->user()->id)->first()?->type;
    }

    return response()->json([
      'post' => $post,
      'is_bookmarked' => $isBookmarked,
      'user_vote' => $userVote,
    ]);
  }

  /**
   * Update the specified post.
   */
  public function update(Request $request, Post $post)
  {
    $this->authorize('update', $post);

    $request->validate([
      'title' => ['sometimes', 'string', 'max:255'],
      'content' => ['sometimes', 'string', 'min:20'],
      'category_id' => ['sometimes', 'exists:categories,id'],
      'type' => ['sometimes', 'in:question,discussion,announcement,tutorial'],
      'tags' => ['nullable', 'array'],
      'tags.*' => ['string', 'max:50'],
    ]);

    if ($request->has('title')) {
      $post->title = $request->title;
      $post->slug = Str::slug($request->title) . '-' . Str::random(6);
    }

    if ($request->has('content')) {
      $post->content = $request->content;
      $post->excerpt = Str::limit(strip_tags($request->content), 200);
    }

    $post->fill($request->only(['category_id', 'type']));
    $post->save();

    // Update tags
    if ($request->has('tags')) {
      // Decrement old tags
      foreach ($post->tags as $tag) {
        $tag->decrementUsage();
      }
      $post->tags()->detach();

      // Attach new tags
      $tagIds = [];
      foreach ($request->tags as $tagName) {
        $tag = Tag::firstOrCreate(
          ['slug' => Str::slug($tagName)],
          ['name' => $tagName]
        );
        $tagIds[] = $tag->id;
        $tag->incrementUsage();
      }
      $post->tags()->attach($tagIds);
    }

    ActivityLog::log('post', 'updated', $post);

    return response()->json([
      'message' => 'Post updated successfully',
      'post' => $post->load(['user', 'category', 'tags']),
    ]);
  }

  /**
   * Remove the specified post.
   */
  public function destroy(Request $request, Post $post)
  {
    $this->authorize('delete', $post);

    // Decrement tags usage
    foreach ($post->tags as $tag) {
      $tag->decrementUsage();
    }

    ActivityLog::log('post', 'deleted', $post);

    $post->delete();

    return response()->json([
      'message' => 'Post deleted successfully',
    ]);
  }

  /**
   * Bookmark a post.
   */
  public function bookmark(Request $request, Post $post)
  {
    $user = $request->user();

    if ($user->savedPosts()->where('post_id', $post->id)->exists()) {
      return response()->json([
        'message' => 'Post already bookmarked',
      ], 400);
    }

    $user->savedPosts()->attach($post->id);
    $post->increment('bookmarks_count');

    return response()->json([
      'message' => 'Post bookmarked successfully',
    ]);
  }

  /**
   * Remove bookmark from a post.
   */
  public function unbookmark(Request $request, Post $post)
  {
    $user = $request->user();

    if (!$user->savedPosts()->where('post_id', $post->id)->exists()) {
      return response()->json([
        'message' => 'Post not bookmarked',
      ], 400);
    }

    $user->savedPosts()->detach($post->id);
    $post->decrement('bookmarks_count');

    return response()->json([
      'message' => 'Bookmark removed successfully',
    ]);
  }

  /**
   * Mark a post as solved.
   */
  public function markSolved(Request $request, Post $post, $commentId)
  {
    $this->authorize('update', $post);

    if ($post->isSolved()) {
      return response()->json([
        'message' => 'Post is already marked as solved',
      ], 400);
    }

    $comment = $post->comments()->findOrFail($commentId);

    $post->markAsSolved($comment);

    // Award reputation to comment author
    $comment->user->addReputation(15, 'answer_accepted', $comment);

    // Award reputation to post author
    $request->user()->addReputation(2, 'accepted_answer', $post);

    ActivityLog::log('post', 'solved', $post);

    return response()->json([
      'message' => 'Post marked as solved',
      'post' => $post->fresh(['bestAnswer']),
    ]);
  }
}
