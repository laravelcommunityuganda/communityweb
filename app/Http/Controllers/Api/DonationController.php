<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DonationMilestoneResource;
use App\Http\Resources\DonationResource;
use App\Models\Donation;
use App\Models\DonationMilestone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    /**
     * Get all active donation milestones.
     */
    public function milestones(Request $request): JsonResponse
    {
        $milestones = DonationMilestone::active()
            ->withCount(['donations as donors_count' => function ($query) {
                $query->where('payment_status', 'completed');
            }])
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => DonationMilestoneResource::collection($milestones),
        ]);
    }

    /**
     * Get a specific milestone.
     */
    public function milestone(DonationMilestone $milestone): JsonResponse
    {
        $milestone->load(['donations' => function ($query) {
            $query->where('payment_status', 'completed')
                ->where('is_anonymous', false)
                ->orderBy('created_at', 'desc')
                ->limit(10);
        }]);

        return response()->json([
            'data' => new DonationMilestoneResource($milestone),
        ]);
    }

    /**
     * Get recent donations for a milestone.
     */
    public function recentDonations(DonationMilestone $milestone): JsonResponse
    {
        $donations = $milestone->donations()
            ->where('payment_status', 'completed')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

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
     * Initialize a donation.
     */
    public function initialize(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'milestone_id' => ['nullable', 'exists:donation_milestones,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['nullable', 'string', 'size:3'],
            'payment_method' => ['required', 'string', 'in:paypal,stripe,mpesa'],
            'donor_name' => ['nullable', 'string', 'max:255'],
            'donor_email' => ['nullable', 'email', 'max:255'],
            'message' => ['nullable', 'string', 'max:500'],
            'is_anonymous' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $milestone = $request->milestone_id ? DonationMilestone::find($request->milestone_id) : null;

        // Create donation record
        $donation = Donation::create([
            'user_id' => $user?->id,
            'milestone_id' => $milestone?->id,
            'amount' => $request->amount,
            'currency' => $request->currency ?? 'USD',
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'donor_name' => $request->donor_name ?? $user?->name,
            'donor_email' => $request->donor_email ?? $user?->email,
            'message' => $request->message,
            'is_anonymous' => $request->is_anonymous ?? false,
        ]);

        // Initialize payment based on method
        $paymentData = match ($request->payment_method) {
            'paypal' => $this->initializePaypal($donation),
            'stripe' => $this->initializeStripe($donation),
            'mpesa' => $this->initializeMpesa($donation),
            default => null,
        };

        if ($paymentData === null) {
            return response()->json([
                'message' => 'Invalid payment method',
            ], 400);
        }

        return response()->json([
            'message' => 'Donation initialized',
            'data' => [
                'donation' => new DonationResource($donation),
                'payment' => $paymentData,
            ],
        ]);
    }

    /**
     * Handle payment callback/webhook.
     */
    public function callback(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => ['required', 'string', 'exists:donations,transaction_id'],
            'payment_method' => ['required', 'string'],
            'status' => ['required', 'string', 'in:success,failed,pending'],
            'payment_reference' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $donation = Donation::where('transaction_id', $request->transaction_id)->first();

        if (!$donation) {
            return response()->json([
                'message' => 'Donation not found',
            ], 404);
        }

        if ($request->status === 'success') {
            $donation->markAsCompleted([
                'payment_reference' => $request->payment_reference,
                'payment_method' => $request->payment_method,
            ]);

            return response()->json([
                'message' => 'Donation completed successfully',
                'data' => new DonationResource($donation),
            ]);
        }

        if ($request->status === 'failed') {
            $donation->markAsFailed('Payment failed');

            return response()->json([
                'message' => 'Payment failed',
                'data' => new DonationResource($donation),
            ], 400);
        }

        return response()->json([
            'message' => 'Payment is still pending',
            'data' => new DonationResource($donation),
        ]);
    }

    /**
     * Get user's donation history.
     */
    public function history(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $donations = $user->donations()
            ->with('milestone')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

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
     * Initialize PayPal payment.
     */
    protected function initializePaypal(Donation $donation): array
    {
        // In production, integrate with PayPal API
        return [
            'method' => 'paypal',
            'checkout_url' => config('services.paypal.checkout_url') . '?token=' . $donation->transaction_id,
            'transaction_id' => $donation->transaction_id,
            'amount' => $donation->amount,
            'currency' => $donation->currency,
        ];
    }

    /**
     * Initialize Stripe payment.
     */
    protected function initializeStripe(Donation $donation): array
    {
        // In production, integrate with Stripe API
        return [
            'method' => 'stripe',
            'publishable_key' => config('services.stripe.publishable_key'),
            'transaction_id' => $donation->transaction_id,
            'amount' => $donation->amount * 100, // Stripe uses cents
            'currency' => strtolower($donation->currency),
        ];
    }

    /**
     * Initialize M-Pesa payment.
     */
    protected function initializeMpesa(Donation $donation): array
    {
        // In production, integrate with M-Pesa API
        return [
            'method' => 'mpesa',
            'transaction_id' => $donation->transaction_id,
            'amount' => $donation->amount,
            'currency' => 'KES',
            'phone_number_required' => true,
        ];
    }
}
