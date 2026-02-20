<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users', 'alpha_dash'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'member',
        ]);

        // Create profile
        Profile::create([
            'user_id' => $user->id,
        ]);

        // Assign default role
        $user->assignRole('member');

        // Send email verification notification
        $user->sendEmailVerificationNotification();

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful. Please check your email for verification.',
            'user' => $user->load('profile'),
            'token' => $token,
        ], 201);
    }

    /**
     * Login user.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        if ($user->isBanned()) {
            return response()->json([
                'message' => 'Your account has been banned.',
                'reason' => $user->ban_reason,
            ], 403);
        }

        // Revoke previous tokens
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user->load('profile', 'roles'),
            'token' => $token,
        ]);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get authenticated user.
     */
    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('profile', 'roles', 'badges'),
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'username' => ['sometimes', 'string', 'max:255', 'unique:users,username,' . $request->user()->id, 'alpha_dash'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'title' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['string', 'max:50'],
            'is_available_for_work' => ['boolean'],
            'is_available_for_mentoring' => ['boolean'],
            'show_email' => ['boolean'],
            'show_location' => ['boolean'],
        ]);

        $user = $request->user();

        // Update user data
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('username')) {
            $user->username = $request->username;
        }
        $user->save();

        // Update profile
        $profile = $user->profile;
        $profile->fill($request->only([
            'bio', 'title', 'company', 'location', 'github_url', 'portfolio_url',
            'linkedin_url', 'twitter_url', 'website_url', 'skills',
            'is_available_for_work', 'is_available_for_mentoring',
            'show_email', 'show_location',
        ]));
        $profile->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->load('profile'),
        ]);
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
            ], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Revoke all tokens
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Password updated successfully. Please login again.',
        ]);
    }

    /**
     * Send password reset link.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 400);
    }

    /**
     * Reset password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
            $user->password = Hash::make($password);
            $user->setRememberToken(Str::random(60));
            $user->save();
        });

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 400);
    }

    /**
     * Verify email.
     */
    public function verifyEmail(Request $request, $token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid verification token',
            ], 400);
        }

        $user->email_verified_at = now();
        $user->email_verification_token = null;
        $user->save();

        return response()->json([
            'message' => 'Email verified successfully',
        ]);
    }

    /**
     * Resend email verification.
     */
    public function resendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified',
            ], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification email sent',
        ]);
    }

    /**
     * Redirect to social provider.
     */
    public function redirectToProvider($provider)
    {
        $url = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();

        return response()->json([
            'url' => $url,
        ]);
    }

    /**
     * Handle social provider callback.
     */
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Authentication failed',
            ], 400);
        }

        // Find or create user
        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (!$user) {
            // Check if email already exists
            $existingUser = User::where('email', $socialUser->getEmail())->first();

            if ($existingUser) {
                // Link social account
                $existingUser->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                ]);
                $user = $existingUser;
            } else {
                // Create new user
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'username' => Str::slug($socialUser->getName()) . Str::random(6),
                    'email' => $socialUser->getEmail(),
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'email_verified_at' => now(),
                    'avatar' => $socialUser->getAvatar(),
                ]);

                Profile::create([
                    'user_id' => $user->id,
                ]);

                $user->assignRole('member');
            }
        }

        if ($user->isBanned()) {
            return response()->json([
                'message' => 'Your account has been banned.',
            ], 403);
        }

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user->load('profile', 'roles'),
            'token' => $token,
        ]);
    }
}