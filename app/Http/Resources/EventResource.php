<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'format' => $this->format,
            'venue_name' => $this->venue_name,
            'venue_address' => $this->venue_address,
            'venue_city' => $this->venue_city,
            'venue_map_url' => $this->venue_map_url,
            'online_url' => $this->online_url,
            'start_date' => $this->start_date?->toISOString(),
            'end_date' => $this->end_date?->toISOString(),
            'capacity' => $this->capacity,
            'is_free' => $this->is_free,
            'price' => $this->price,
            'cover_image' => $this->cover_image ? \Storage::url($this->cover_image) : null,
            'status' => $this->status,
            'views_count' => $this->views_count,
            'attendees_count' => $this->whenCounted('attendees'),
            'created_at' => $this->created_at?->toISOString(),
            'user' => new UserResource($this->whenLoaded('user')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'is_attending' => $this->when(
                $request->user(),
                fn() => $this->attendees()->where('user_id', $request->user()->id)->exists()
            ),
        ];
    }
}