<?php

namespace App\Http\Controllers\V1;

use App\Enums\UserTypeEnum;
use App\Helper\V1\ApiResponse;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Requests\V1\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
    use HasApiTokens;

    /**
     * Register a new user
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());
        $user->syncRoles(UserTypeEnum::User->value);

        return ApiResponse::success(
            [
                new UserResource($user),
                'token' => $user->createToken("access-token")->plainTextToken,
            ]
            ,
            'User registered successfully',
            201
        );
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return ApiResponse::notFound('The provided credentials are incorrect.');
        }

        $user = Auth::user();

        return ApiResponse::success(
            [
                new UserResource($user),
                'token' => $user->createToken("access-token")->plainTextToken,

            ],
            "User logged in successfully",
            200
        );
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return ApiResponse::success([], 'You are logged out successfully', 200);
    }

    /**
     * Get authenticated user profile
     */
    public function show(Request $request)
    {
        $user = $request->user();
        return ApiResponse::success([
            new UserResource($user),
            // 'user_role' => $user->getRoleNames()->first(),
        ], 'User profile retrieved successfully', 200);
    }
}
