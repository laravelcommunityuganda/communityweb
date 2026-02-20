<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'excerpt' => $this->excerpt ?? \Str::limit(strip_tags($this->content), 200),
            'type' => $this->type,
            'status' => $this->status,
            'is_solved' => $this->is_solved,
            'views_count' => $this->views_count,
            'upvotes_count' => $this->upvotes_count,
            'downvotes_count' => $this->downvotes_count,
            'comments_count' => $this->comments_count,
            'bookmarks_count' => $this->bookmarks_count,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'user' => new UserResource($this->whenLoaded('user')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'is_bookmarked' => $this->when(
                $request->user(),
                fn() => $request->user()->bookmarkedPosts()->where('post_id', $this->id)->exists()
            ),
        ];
    }
}