<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
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
            'company_name' => $this->company_name,
            'company_website' => $this->company_website,
            'location' => $this->location,
            'is_remote' => $this->is_remote,
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'salary_currency' => $this->salary_currency,
            'required_skills' => $this->required_skills,
            'deadline' => $this->deadline?->toISOString(),
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'views_count' => $this->views_count,
            'applications_count' => $this->whenCounted('applications'),
            'created_at' => $this->created_at?->toISOString(),
            'user' => new UserResource($this->whenLoaded('user')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'is_saved' => $this->when(
                $request->user(),
                fn() => $request->user()->savedJobs()->where('job_id', $this->id)->exists()
            ),
            'has_applied' => $this->when(
                $request->user(),
                fn() => $request->user()->jobApplications()->where('job_id', $this->id)->exists()
            ),
        ];
    }
}