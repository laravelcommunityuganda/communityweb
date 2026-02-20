<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    /**
     * Report content.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reportable_type' => ['required', 'in:App\Models\Post,App\Models\Comment,App\Models\User,App\Models\Resource,App\Models\Job,App\Models\Event'],
            'reportable_id' => ['required', 'integer'],
            'reason' => ['required', 'in:spam,harassment,inappropriate,off_topic,other'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();

        // Check if already reported
        $existingReport = Report::where('user_id', $user->id)
            ->where('reportable_type', $validated['reportable_type'])
            ->where('reportable_id', $validated['reportable_id'])
            ->where('status', 'pending')
            ->first();

        if ($existingReport) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reported this content',
            ], 400);
        }

        $report = Report::create([
            'user_id' => $user->id,
            'reportable_type' => $validated['reportable_type'],
            'reportable_id' => $validated['reportable_id'],
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report submitted successfully. Thank you for helping keep our community safe.',
            'data' => $report,
        ], 201);
    }

    /**
     * Get reports (admin/moderator only).
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('view reports');

        $query = Report::with(['user.profile', 'reportable'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by reason
        if ($request->has('reason')) {
            $query->where('reason', $request->reason);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('reportable_type', 'App\Models\\' . ucfirst($request->type));
        }

        $reports = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $reports,
        ]);
    }

    /**
     * Resolve a report (admin/moderator only).
     */
    public function resolve(Request $request, Report $report): JsonResponse
    {
        $this->authorize('resolve reports');

        $validated = $request->validate([
            'action' => ['required', 'in:dismissed,warning,content_removed,user_banned'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $report->update([
            'status' => 'resolved',
            'resolved_by' => $request->user()->id,
            'resolved_at' => now(),
            'resolution_notes' => $validated['notes'] ?? null,
        ]);

        // Take action based on resolution
        switch ($validated['action']) {
            case 'content_removed':
                $report->reportable->delete();
                break;
            case 'user_banned':
                if (method_exists($report->reportable, 'user')) {
                    $report->reportable->user->update(['is_banned' => true]);
                } elseif ($report->reportable_type === 'App\Models\User') {
                    $report->reportable->update(['is_banned' => true]);
                }
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Report resolved successfully',
        ]);
    }

    /**
     * Dismiss a report (admin/moderator only).
     */
    public function dismiss(Request $request, Report $report): JsonResponse
    {
        $this->authorize('resolve reports');

        $report->update([
            'status' => 'dismissed',
            'resolved_by' => $request->user()->id,
            'resolved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report dismissed',
        ]);
    }

    /**
     * Get report statistics (admin/moderator only).
     */
    public function statistics(): JsonResponse
    {
        $this->authorize('view reports');

        $stats = [
            'total' => Report::count(),
            'pending' => Report::pending()->count(),
            'resolved' => Report::where('status', 'resolved')->count(),
            'dismissed' => Report::where('status', 'dismissed')->count(),
            'by_reason' => Report::selectRaw('reason, count(*) as count')
                ->groupBy('reason')
                ->pluck('count', 'reason'),
            'by_type' => Report::selectRaw('reportable_type, count(*) as count')
                ->groupBy('reportable_type')
                ->pluck('count', 'reportable_type'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}