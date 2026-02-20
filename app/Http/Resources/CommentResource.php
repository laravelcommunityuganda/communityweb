<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'votes_count' => $this->votes_count,
            'is_best_answer' => $this->is_best_answer,
            'is_edited' => $this->is_edited,
            'edited_at' => $this->edited_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'user' => new UserResource($this->whenLoaded('user')),
            'replies' => CommentResource::collection($this->whenLoaded('replies')),
            'replies_count' => $this->whenCounted('replies'),
        ];
    }
}