<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mentorship;
use App\Models\MentorshipSession;
use App\Http\Resources\MentorshipResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MentorshipController extends Controller
{
    /**
     * Display a listing of mentors.
     */
    public function mentors(Request $request): JsonResponse
    {
        $query = Mentorship::with(['user.profile'])
            ->where('is_available', true)
            ->where('status', 'approved');

        // Filter by expertise
        if ($request->has('expertise')) {
            $query->whereJsonContains('expertise', $request->expertise);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $mentors = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => MentorshipResource::collection($mentors),
        ]);
    }

    /**
     * Become a mentor.
     */
    public function becomeMentor(Request $request): JsonResponse
    {
        $user = $request->user();

        // Check if already a mentor
        if ($user->mentorProfile) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a mentor profile',
            ], 400);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'expertise' => ['required', 'array', 'min:1'],
            'expertise.*' => ['string', 'max:50'],
            'experience_years' => ['required', 'integer', 'min:1'],
            'bio' => ['required', 'string', 'max:2000'],
            'availability' => ['nullable', 'string', 'max:255'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
        ]);

        $mentorship = Mentorship::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'company' => $validated['company'],
            'expertise' => $validated['expertise'],
            'experience_years' => $validated['experience_years'],
            'bio' => $validated['bio'],
            'availability' => $validated['availability'] ?? null,
            'hourly_rate' => $validated['hourly_rate'] ?? null,
            'is_available' => true,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mentor application submitted. It will be reviewed shortly.',
            'data' => new MentorshipResource($mentorship),
        ], 201);
    }

    /**
     * Update mentor profile.
     */
    public function updateMentorProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->mentorProfile) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have a mentor profile',
            ], 404);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'company' => ['sometimes', 'string', 'max:255'],
            'expertise' => ['sometimes', 'array', 'min:1'],
            'expertise.*' => ['string', 'max:50'],
            'experience_years' => ['sometimes', 'integer', 'min:1'],
            'bio' => ['sometimes', 'string', 'max:2000'],
            'availability' => ['nullable', 'string', 'max:255'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'is_available' => ['boolean'],
        ]);

        $user->mentorProfile->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Mentor profile updated successfully',
            'data' => new MentorshipResource($user->mentorProfile->fresh()),
        ]);
    }

    /**
     * Request mentorship.
     */
    public function requestMentorship(Request $request, Mentorship $mentorship): JsonResponse
    {
        $mentee = $request->user();

        // Check if mentor is available
        if (!$mentorship->is_available || $mentorship->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'This mentor is not currently available',
            ], 400);
        }

        // Check if already requested
        $existingRequest = $mentorship->requests()
            ->where('mentee_id', $mentee->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending or active mentorship with this mentor',
            ], 400);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'goals' => ['required', 'array', 'min:1'],
            'goals.*' => ['string', 'max:255'],
            'preferred_schedule' => ['nullable', 'string', 'max:255'],
        ]);

        $request = $mentorship->requests()->create([
            'mentee_id' => $mentee->id,
            'message' => $validated['message'],
            'goals' => $validated['goals'],
            'preferred_schedule' => $validated['preferred_schedule'] ?? null,
            'status' => 'pending',
        ]);

        // TODO: Notify mentor

        return response()->json([
            'success' => true,
            'message' => 'Mentorship request sent successfully',
            'data' => $request,
        ], 201);
    }

    /**
     * Accept mentorship request.
     */
    public function acceptRequest(Request $request, $requestId): JsonResponse
    {
        $mentorRequest = \App\Models\MentorshipRequest::findOrFail($requestId);

        // Check if user is the mentor
        if ($request->user()->id !== $mentorRequest->mentorship->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($mentorRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This request has already been processed',
            ], 400);
        }

        $mentorRequest->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // TODO: Notify mentee

        return response()->json([
            'success' => true,
            'message' => 'Mentorship request accepted',
        ]);
    }

    /**
     * Decline mentorship request.
     */
    public function declineRequest(Request $request, $requestId): JsonResponse
    {
        $mentorRequest = \App\Models\MentorshipRequest::findOrFail($requestId);

        // Check if user is the mentor
        if ($request->user()->id !== $mentorRequest->mentorship->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $mentorRequest->update(['status' => 'declined']);

        return response()->json([
            'success' => true,
            'message' => 'Mentorship request declined',
        ]);
    }

    /**
     * Schedule a session.
     */
    public function scheduleSession(Request $request, $requestId): JsonResponse
    {
        $mentorRequest = \App\Models\MentorshipRequest::findOrFail($requestId);

        // Check if user is mentor or mentee
        $userId = $request->user()->id;
        if ($userId !== $mentorRequest->mentorship->user_id && $userId !== $mentorRequest->mentee_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($mentorRequest->status !== 'accepted') {
            return response()->json([
                'success' => false,
                'message' => 'Mentorship must be accepted first',
            ], 400);
        }

        $validated = $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:180'],
            'notes' => ['nullable', 'string', 'max:500'],
            'meeting_url' => ['nullable', 'url', 'max:500'],
        ]);

        $session = MentorshipSession::create([
            'mentorship_request_id' => $mentorRequest->id,
            'scheduled_at' => $validated['scheduled_at'],
            'duration_minutes' => $validated['duration_minutes'],
            'notes' => $validated['notes'] ?? null,
            'meeting_url' => $validated['meeting_url'] ?? null,
            'status' => 'scheduled',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Session scheduled successfully',
            'data' => $session,
        ], 201);
    }

    /**
     * Complete a session.
     */
    public function completeSession(Request $request, MentorshipSession $session): JsonResponse
    {
        // Check if user is mentor
        if ($request->user()->id !== $session->mentorshipRequest->mentorship->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $session->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Update session count
        $session->mentorshipRequest->increment('sessions_count');

        return response()->json([
            'success' => true,
            'message' => 'Session marked as completed',
        ]);
    }

    /**
     * Rate a session.
     */
    public function rateSession(Request $request, MentorshipSession $session): JsonResponse
    {
        // Check if user is mentee
        if ($request->user()->id !== $session->mentorshipRequest->mentee_id) {
            return response()->json([
                'success' => false,
                'message' => 'Only the mentee can rate the session',
            ], 403);
        }

        if ($session->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Session must be completed before rating',
            ], 400);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:500'],
        ]);

        $session->update([
            'rating' => $validated['rating'],
            'review' => $validated['review'] ?? null,
        ]);

        // Update mentor average rating
        $mentorship = $session->mentorshipRequest->mentorship;
        $avgRating = MentorshipSession::whereHas('mentorshipRequest', function ($q) use ($mentorship) {
            $q->where('mentorship_id', $mentorship->id);
        })->whereNotNull('rating')->avg('rating');

        $mentorship->update(['rating' => round($avgRating, 1)]);

        return response()->json([
            'success' => true,
            'message' => 'Session rated successfully',
        ]);
    }

    /**
     * Get my mentorship requests (as mentor).
     */
    public function myRequests(Request $request): JsonResponse
    {
        $mentorProfile = $request->user()->mentorProfile;

        if (!$mentorProfile) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have a mentor profile',
            ], 404);
        }

        $requests = $mentorProfile->requests()
            ->with(['mentee.profile'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $requests,
        ]);
    }

    /**
     * Get my mentorships (as mentee).
     */
    public function myMentorships(Request $request): JsonResponse
    {
        $mentorships = $request->user()->mentorshipsAsMentee()
            ->with(['mentorship.user.profile'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $mentorships,
        ]);
    }
}