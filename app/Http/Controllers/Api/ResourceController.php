<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Http\Resources\ResourceResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    /**
     * Display a listing of resources.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Resource::with(['user.profile', 'category'])
            ->approved()
            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $resources = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => ResourceResource::collection($resources),
        ]);
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'type' => ['required', 'in:pdf,github,youtube,snippet,boilerplate'],
            'category_id' => ['required', 'exists:categories,id'],
            'file' => ['required_if:type,pdf', 'file', 'max:10240'], // 10MB max
            'url' => ['required_if:type,github,youtube', 'nullable', 'url', 'max:500'],
            'code' => ['required_if:type,snippet', 'nullable', 'string'],
            'language' => ['nullable', 'string', 'max:50'],
        ]);

        $resource = new Resource([
            'user_id' => $request->user()->id,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => \Str::slug($validated['title']) . '-' . \Str::random(6),
            'description' => $validated['description'],
            'type' => $validated['type'],
            'language' => $validated['language'] ?? null,
            'status' => 'pending',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('resources', 'public');
            $resource->file_path = $path;
            $resource->file_size = $request->file('file')->getSize();
        }

        // Handle URL
        if (isset($validated['url'])) {
            $resource->url = $validated['url'];
        }

        // Handle code snippet
        if (isset($validated['code'])) {
            $resource->code = $validated['code'];
        }

        $resource->save();

        return response()->json([
            'success' => true,
            'message' => 'Resource submitted successfully. It will be reviewed before publishing.',
            'data' => new ResourceResource($resource),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug): JsonResponse
    {
        $resource = Resource::where('slug', $slug)
            ->with(['user.profile', 'category'])
            ->firstOrFail();

        // Increment views
        $resource->increment('views_count');

        return response()->json([
            'success' => true,
            'data' => new ResourceResource($resource),
        ]);
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, Resource $resource): JsonResponse
    {
        $this->authorize('update', $resource);

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'max:5000'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'url' => ['nullable', 'url', 'max:500'],
            'code' => ['nullable', 'string'],
            'language' => ['nullable', 'string', 'max:50'],
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = \Str::slug($validated['title']) . '-' . \Str::random(6);
        }

        $resource->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Resource updated successfully',
            'data' => new ResourceResource($resource->fresh()),
        ]);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(Resource $resource): JsonResponse
    {
        $this->authorize('delete', $resource);

        // Delete file if exists
        if ($resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        return response()->json([
            'success' => true,
            'message' => 'Resource deleted successfully',
        ]);
    }

    /**
     * Download resource file.
     */
    public function download(Resource $resource): JsonResponse
    {
        if (!$resource->file_path) {
            return response()->json([
                'success' => false,
                'message' => 'No file available for download',
            ], 404);
        }

        $resource->increment('downloads_count');

        return response()->json([
            'success' => true,
            'data' => [
                'download_url' => Storage::disk('public')->url($resource->file_path),
            ],
        ]);
    }

    /**
     * Rate a resource.
     */
    public function rate(Request $request, Resource $resource): JsonResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        // Check if user already rated
        $existingRating = $resource->ratings()
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existingRating) {
            $existingRating->update(['rating' => $validated['rating']]);
        } else {
            $resource->ratings()->create([
                'user_id' => $request->user()->id,
                'rating' => $validated['rating'],
            ]);
        }

        // Update average rating
        $avgRating = $resource->ratings()->avg('rating');
        $resource->update(['rating' => round($avgRating, 1)]);

        return response()->json([
            'success' => true,
            'message' => 'Rating submitted successfully',
            'data' => [
                'rating' => $resource->fresh()->rating,
            ],
        ]);
    }

    /**
     * Bookmark a resource.
     */
    public function bookmark(Request $request, Resource $resource): JsonResponse
    {
        $user = $request->user();

        if ($user->bookmarkedResources()->where('resource_id', $resource->id)->exists()) {
            $user->bookmarkedResources()->detach($resource->id);

            return response()->json([
                'success' => true,
                'message' => 'Resource removed from bookmarks',
                'bookmarked' => false,
            ]);
        }

        $user->bookmarkedResources()->attach($resource->id);

        return response()->json([
            'success' => true,
            'message' => 'Resource bookmarked',
            'bookmarked' => true,
        ]);
    }
}