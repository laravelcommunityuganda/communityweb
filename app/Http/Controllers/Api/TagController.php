<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    /**
     * Display a listing of tags.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Tag::orderBy('usage_count', 'desc');

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $tags = $query->paginate($request->get('per_page', 30));

        return response()->json([
            'success' => true,
            'data' => $tags,
        ]);
    }

    /**
     * Store a newly created tag.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:tags'],
        ]);

        $tag = Tag::create([
            'name' => $validated['name'],
            'slug' => \Str::slug($validated['name']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tag created successfully',
            'data' => $tag,
        ], 201);
    }

    /**
     * Display the specified tag.
     */
    public function show(string $slug): JsonResponse
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $tag,
        ]);
    }

    /**
     * Remove the specified tag.
     */
    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tag deleted successfully',
        ]);
    }

    /**
     * Get popular tags.
     */
    public function popular(): JsonResponse
    {
        $tags = Tag::orderBy('usage_count', 'desc')
            ->take(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tags,
        ]);
    }
}