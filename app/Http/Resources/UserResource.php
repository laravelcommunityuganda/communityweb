<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->when($request->user()?->id === $this->id, $this->email),
            'avatar' => $this->avatar_url,
            'role' => $this->role,
            'reputation' => $this->reputation,
            'is_verified' => $this->is_verified,
            'is_banned' => $this->is_banned,
            'created_at' => $this->created_at?->toISOString(),
            'profile' => new ProfileResource($this->whenLoaded('profile')),
            'posts_count' => $this->whenCounted('posts'),
            'followers_count' => $this->whenCounted('followers'),
            'following_count' => $this->whenCounted('following'),
            'badges_count' => $this->whenCounted('badges'),
            'roles' => $this->whenLoaded('roles', fn() => $this->roles->pluck('name')),
        ];
    }
}