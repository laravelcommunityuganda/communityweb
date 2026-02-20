<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'bio' => $this->bio,
            'title' => $this->title,
            'company' => $this->company,
            'location' => $this->location,
            'website_url' => $this->website_url,
            'github_url' => $this->github_url,
            'twitter_url' => $this->twitter_url,
            'linkedin_url' => $this->linkedin_url,
            'skills' => $this->skills,
            'is_available_for_work' => $this->is_available_for_work,
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}