<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\Auth\AuthService;

class AuthController extends Controller
{
    // initialize authService layer
    public function __construct(
        protected AuthService $authService
    ) {}

    // register new users with default plan and email otp codes
    public function register(RegisterRequest $request)
    {
        $result = $this->authService
            ->register($request->validated());

        return response()->json($result, 201);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService
            ->login($request->validated());

        return response()->json($result);
    }

    public function refreshToken(RefreshTokenRequest $request)
    {
        $result = $this->authService->refreshToken($request->validated());
        return response()->json($result, 201);
    }

    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function logout(RefreshTokenRequest $request)
    {
         $result = $this->authService->logout($request->validated());
        return response()->json($result, 201);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
         $result = $this->authService->forgotPassword($request->validated());
        return response()->json($result, 201);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
         $result = $this->authService->resetPassword($request->validated());
        return response()->json($result, 201);
    }

    
}
