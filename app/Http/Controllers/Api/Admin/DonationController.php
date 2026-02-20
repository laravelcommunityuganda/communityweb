<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DonationMilestoneResource;
use App\Http\Resources\DonationResource;
use App\Models\Donation;
use App\Models\DonationMilestone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DonationController extends Controller
{
    /**
     * Get all milestones (admin view).
     */
    public function milestones(Request $request): JsonResponse
    {
        $query = DonationMilestone::withCount(['donations as donors_count' => function ($query) {
            $query->where('payment_status', 'completed');
        }]);

        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $milestones = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'data' => DonationMilestoneResource::collection($milestones),
            'meta' => [
                'current_page' => $milestones->currentPage(),
                'last_page' => $milestones->lastPage(),
                'total' => $milestones->total(),
            ],
        ]);
    }

    /**
     * Create a new milestone.
     */
    public function storeMilestone(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'target_amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['nullable', 'string', 'size:3'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->except('image');
        $data['currency'] = $request->currency ?? 'USD';

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('donations', 'public');
        }

        $milestone = DonationMilestone::create($data);

        return response()->json([
            'message' => 'Milestone created successfully',
            'data' => new DonationMilestoneResource($milestone),
        ], 201);
    }

    /**
     * Update a milestone.
     */
    public function updateMilestone(Request $request, DonationMilestone $milestone): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'target_amount' => ['sometimes', 'numeric', 'min:1'],
            'currency' => ['nullable', 'string', 'size:3'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($milestone->image) {
                Storage::disk('public')->delete($milestone->image);
            }
            $data['image'] = $request->file('image')->store('donations', 'public');
        }

        $milestone->update($data);

        return response()->json([
            'message' => 'Milestone updated successfully',
            'data' => new DonationMilestoneResource($milestone),
        ]);
    }

    /**
     * Delete a milestone.
     */
    public function deleteMilestone(DonationMilestone $milestone): JsonResponse
    {
        if ($milestone->image) {
            Storage::disk('public')->delete($milestone->image);
        }

        $milestone->delete();

        return response()->json([
            'message' => 'Milestone deleted successfully',
        ]);
    }

    /**
     * Get all donations (admin view).
     */
    public function donations(Request $request): JsonResponse
    {
        $query = Donation::with(['user', 'milestone']);

        if ($request->has('status')) {
            $query->where('payment_status', $request->status);
        }

        if ($request->has('milestone_id')) {
            $query->where('milestone_id', $request->milestone_id);
        }

        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $donations = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'data' => DonationResource::collection($donations),
            'meta' => [
                'current_page' => $donations->currentPage(),
                'last_page' => $donations->lastPage(),
                'total' => $donations->total(),
            ],
        ]);
    }

    /**
     * Get donation statistics.
     */
    public function statistics(): JsonResponse
    {
        $totalDonations = Donation::where('payment_status', 'completed')->sum('amount');
        $totalDonors = Donation::where('payment_status', 'completed')
            ->distinct('user_id')
            ->count('user_id');
        $pendingDonations = Donation::where('payment_status', 'pending')->count();
        $completedDonations = Donation::where('payment_status', 'completed')->count();

        $recentDonations = Donation::where('payment_status', 'completed')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $milestoneStats = DonationMilestone::withCount(['donations as donors_count' => function ($query) {
            $query->where('payment_status', 'completed');
        }])->get()->map(function ($milestone) {
            return [
                'id' => $milestone->id,
                'title' => $milestone->title,
                'target_amount' => $milestone->target_amount,
                'current_amount' => $milestone->current_amount,
                'progress_percentage' => $milestone->progress_percentage,
                'donors_count' => $milestone->donors_count,
            ];
        });

        return response()->json([
            'data' => [
                'total_donations' => $totalDonations,
                'total_donors' => $totalDonors,
                'pending_donations' => $pendingDonations,
                'completed_donations' => $completedDonations,
                'recent_donations' => DonationResource::collection($recentDonations),
                'milestone_stats' => $milestoneStats,
            ],
        ]);
    }

    /**
     * Export donations to CSV.
     */
    public function export(Request $request): JsonResponse
    {
        $query = Donation::where('payment_status', 'completed')
            ->with(['user', 'milestone']);

        if ($request->has('milestone_id')) {
            $query->where('milestone_id', $request->milestone_id);
        }

        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        }

        $donations = $query->orderBy('created_at', 'desc')->get();

        $csv = "Transaction ID,Date,Donor Name,Donor Email,Amount,Currency,Payment Method,Milestone,Message\n";

        foreach ($donations as $donation) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $donation->transaction_id,
                $donation->created_at->format('Y-m-d H:i:s'),
                $donation->donor_display_name,
                $donation->is_anonymous ? 'Anonymous' : ($donation->donor_email ?? $donation->user?->email ?? ''),
                $donation->amount,
                $donation->currency,
                $donation->payment_method,
                $donation->milestone?->title ?? 'General',
                str_replace(',', ' ', $donation->message ?? '')
            );
        }

        return response()->json([
            'data' => [
                'csv' => $csv,
                'filename' => 'donations_' . date('Y-m-d') . '.csv',
            ],
        ]);
    }
}
