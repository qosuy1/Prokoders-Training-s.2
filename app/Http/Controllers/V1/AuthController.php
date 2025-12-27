<?php

namespace App\Http\Controllers\V1;

use App\Enums\UserTypeEnum;
use App\Helper\V1\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Support\ValidatedData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
    use HasApiTokens;
    public function register(Request $request)
    {
        $validated = Validator::make(
            $request->all(),
            [
                'name' => "required|max:255",
                'email' => "required|unique:users|email",
                'password' => "required|confirmed",
            ],
        );
        // dd($validated->getData());
        if ($validated->fails())
            return ApiResponse::validationError($validated->errors()->all());

        // $data = $validated->getData();
        $user = User::create($validated->getData());
        $user->syncRoles(UserTypeEnum::User->value);

        $request = [];
        $request['token'] = $user->createToken("access-token")->plainTextToken;
        $request['name'] = $user->name;
        $request['email'] = $user->email;
        $request['user_role'] = $user->getRoleNames()->first();

        return ApiResponse::success($request, 'user registerd', 201);
    }

    public function login(Request $request)
    {
        $request->validate(
            [
                'email' => "required|exists:users|email",
                'password' => "required",
            ]
        );
        // $user = User::where('email', $request->email)->first();
        // if (!$user || !Hash::check($request->password, $user->password)) {
        // }
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return ApiResponse::notFound('The provided credentials are incorrect .');
        }
        $user = Auth::user();
        $request = [];
        $request['token'] = $user->createToken("access-token")->plainTextToken;
        $request['name'] = $user->name;
        $request['email'] = $user->email;
        $request['user_role'] = $user->getRoleNames()->first();

        return ApiResponse::success($request, "user logged in successfully", 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return ApiResponse::success([], 'you are logged out', 200);
    }

    public function show(Request $request)
    {
        $user = $request->user();
        return ApiResponse::success([
            'user' => $user,
            'user_type' => $user->getRoleNames()->first()
        ]);

    }
}
