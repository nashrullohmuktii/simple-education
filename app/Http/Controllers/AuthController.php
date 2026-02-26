<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ResponseHelper;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign 'user' role to the newly registered user
        $user->assignRole('user');

        $token = $user->createToken('auth_token')->accessToken;

        return ResponseHelper::success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 'Registration successful', 201);
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ResponseHelper::error('The provided credentials do not match our records.', null, 401);
        }

        $token = $user->createToken('auth_token')->accessToken;

        return ResponseHelper::success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 'Login successful');
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return ResponseHelper::success([], 'Successfully logged out');
    }
}
