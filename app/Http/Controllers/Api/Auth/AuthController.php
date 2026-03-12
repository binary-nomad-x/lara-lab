<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Generate an auth token for the newly registered user
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully',
                'user' => new UserResource($user),
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            Log::error('User registration failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Registration failed. Please try again later.',
            ], 500);
        }
    }

    /**
     * Authenticate a user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            // Find the user by email
            $user = User::where('email', $request->email)->first();

            // Validate credentials
            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            // Check if the user is active (custom logic for tenant admin approval)
            if ($user->status !== 'active') {
                return response()->json([
                    'message' => 'Your account is not active. Please contact support.',
                ], 403);
            }

            // Generate a new token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => new UserResource($user),
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            Log::error('User login failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Login failed. Please try again later.',
            ], 500);
        }
    }

    /**
     * Logout the authenticated user.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Revoke the current access token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logged out successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('User logout failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Logout failed. Please try again later.',
            ], 500);
        }
    }

    /**
     * Fetch the authenticated user's profile.
     */
    public function profile(Request $request): JsonResponse
    {
        try {
            // Ensure the user is authenticated
            if (!$request->user()) {
                return response()->json([
                    'message' => 'Unauthenticated',
                ], 401);
            }

            return response()->json([
                'user' => new UserResource($request->user()),
            ]);
        } catch (\Exception $e) {
            Log::error('Profile fetch failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch profile. Please try again later.',
            ], 500);
        }
    }

    /**
     * Refresh the authentication token.
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            // Revoke the current token
            $request->user()->currentAccessToken()->delete();

            // Generate a new token
            $token = $request->user()->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Token refreshed successfully',
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            Log::error('Token refresh failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Token refresh failed. Please try again later.',
            ], 500);
        }
    }

    /**
     * Handle GDPR-compliant data export.
     */
    public function exportData(Request $request): JsonResponse {
        try {
            // Ensure the user is authenticated
            if (!$request->user()) {
                return response()->json([
                    'message' => 'Unauthenticated',
                ], 401);
            }

            // Export user data (GDPR compliance)
            $userData = $request->user()->only(['name', 'email', 'created_at', 'updated_at']);

            return response()->json([
                'message' => 'Data exported successfully',
                'data' => $userData,
            ]);
        } catch (\Exception $e) {
            Log::error('Data export failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to export data. Please try again later.',
            ], 500);
        }
    }

    /**
     * Handle "Right to be Forgotten" request.
     */
    public function deleteAccount(Request $request): JsonResponse {
        try {
            // Ensure the user is authenticated
            if (!$request->user()) {
                return response()->json([
                    'message' => 'Unauthenticated',
                ], 401);
            }

            // Soft delete or permanently delete the user account
            $request->user()->delete();

            return response()->json([
                'message' => 'Account deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Account deletion failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete account. Please try again later.',
            ], 500);
        }
    }
}