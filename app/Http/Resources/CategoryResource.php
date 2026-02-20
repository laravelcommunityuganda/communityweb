<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'icon' => $this->icon,
            'color' => $this->color,
            'posts_count' => $this->whenCounted('posts'),
            'jobs_count' => $this->whenCounted('jobs'),
            'events_count' => $this->whenCounted('events'),
            'resources_count' => $this->whenCounted('resources'),
        ];
    }
}