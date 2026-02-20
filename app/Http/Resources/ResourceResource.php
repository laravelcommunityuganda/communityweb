<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceResource extends JsonResource
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
            'description' => $this->description,
            'type' => $this->type,
            'url' => $this->url,
            'language' => $this->language,
            'code' => $this->code,
            'file_path' => $this->file_path ? \Storage::url($this->file_path) : null,
            'file_size' => $this->file_size,
            'rating' => $this->rating,
            'views_count' => $this->views_count,
            'downloads_count' => $this->downloads_count,
            'status' => $this->status,
            'created_at' => $this->created_at?->toISOString(),
            'user' => new UserResource($this->whenLoaded('user')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'is_bookmarked' => $this->when(
                $request->user(),
                fn() => $request->user()->bookmarkedResources()->where('resource_id', $this->id)->exists()
            ),
        ];
    }
}