<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    /**
     * Display a listing of events.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Event::with(['user.profile', 'category'])
            ->published()
            ->orderBy('start_date', 'asc');

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by format
        if ($request->has('format')) {
            $query->where('format', $request->format);
        }

        // Filter by location
        if ($request->has('location')) {
            $query->where('venue_city', $request->location);
        }

        // Filter upcoming only
        if ($request->has('upcoming') && $request->upcoming) {
            $query->where('start_date', '>=', now());
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $events = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => EventResource::collection($events),
        ]);
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'type' => ['required', 'in:meetup,workshop,conference,webinar,hackathon'],
            'format' => ['required', 'in:physical,online,hybrid'],
            'category_id' => ['required', 'exists:categories,id'],
            'venue_name' => ['required_if:format,physical,hybrid', 'nullable', 'string', 'max:255'],
            'venue_address' => ['nullable', 'string', 'max:500'],
            'venue_city' => ['required_if:format,physical,hybrid', 'nullable', 'string', 'max:255'],
            'venue_map_url' => ['nullable', 'url', 'max:500'],
            'online_url' => ['required_if:format,online,hybrid', 'nullable', 'url', 'max:500'],
            'start_date' => ['required', 'date', 'after:now'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'is_free' => ['boolean'],
            'price' => ['required_if:is_free,false', 'nullable', 'numeric', 'min:0'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $event = new Event([
            'user_id' => $request->user()->id,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => \Str::slug($validated['title']) . '-' . \Str::random(6),
            'description' => $validated['description'],
            'type' => $validated['type'],
            'format' => $validated['format'],
            'venue_name' => $validated['venue_name'] ?? null,
            'venue_address' => $validated['venue_address'] ?? null,
            'venue_city' => $validated['venue_city'] ?? null,
            'venue_map_url' => $validated['venue_map_url'] ?? null,
            'online_url' => $validated['online_url'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'capacity' => $validated['capacity'] ?? null,
            'is_free' => $validated['is_free'] ?? true,
            'price' => $validated['price'] ?? 0,
            'status' => 'published',
        ]);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('events', 'public');
            $event->cover_image = $path;
        }

        $event->save();

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'data' => new EventResource($event->load(['user.profile', 'category'])),
        ], 201);
    }

    /**
     * Display the specified event.
     */
    public function show(string $slug): JsonResponse
    {
        $event = Event::where('slug', $slug)
            ->with(['user.profile', 'category'])
            ->withCount('attendees')
            ->firstOrFail();

        // Increment views
        $event->increment('views_count');

        return response()->json([
            'success' => true,
            'data' => new EventResource($event),
        ]);
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, Event $event): JsonResponse
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'max:10000'],
            'type' => ['sometimes', 'in:meetup,workshop,conference,webinar,hackathon'],
            'format' => ['sometimes', 'in:physical,online,hybrid'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'venue_name' => ['nullable', 'string', 'max:255'],
            'venue_address' => ['nullable', 'string', 'max:500'],
            'venue_city' => ['nullable', 'string', 'max:255'],
            'venue_map_url' => ['nullable', 'url', 'max:500'],
            'online_url' => ['nullable', 'url', 'max:500'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'is_free' => ['boolean'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = \Str::slug($validated['title']) . '-' . \Str::random(6);
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            if ($event->cover_image) {
                \Storage::disk('public')->delete($event->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('events', 'public');
        }

        $event->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data' => new EventResource($event->fresh()),
        ]);
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event): JsonResponse
    {
        $this->authorize('delete', $event);

        if ($event->cover_image) {
            \Storage::disk('public')->delete($event->cover_image);
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully',
        ]);
    }

    /**
     * RSVP to an event.
     */
    public function rsvp(Request $request, Event $event): JsonResponse
    {
        $user = $request->user();

        // Check if event is in the future
        if ($event->start_date < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot RSVP to past events',
            ], 400);
        }

        // Check capacity
        if ($event->capacity && $event->attendees()->count() >= $event->capacity) {
            return response()->json([
                'success' => false,
                'message' => 'Event is at full capacity',
            ], 400);
        }

        // Check if already RSVP'd
        $existingRsvp = $event->attendees()->where('user_id', $user->id)->first();

        if ($existingRsvp) {
            $event->attendees()->detach($user->id);

            return response()->json([
                'success' => true,
                'message' => 'RSVP cancelled',
                'attending' => false,
            ]);
        }

        $event->attendees()->attach($user->id, [
            'status' => 'attending',
            'registered_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'RSVP successful',
            'attending' => true,
        ]);
    }

    /**
     * Get event attendees.
     */
    public function attendees(Event $event): JsonResponse
    {
        $attendees = $event->attendees()
            ->with('profile')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\UserResource::collection($attendees),
        ]);
    }

    /**
     * Get upcoming events.
     */
    public function upcoming(): JsonResponse
    {
        $events = Event::with(['user.profile', 'category'])
            ->published()
            ->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => EventResource::collection($events),
        ]);
    }
}