<?php

namespace App\Services\Auth;

use App\Exceptions\ApiException;
use App\Models\DeviceSession;
use App\Models\Otp;
use App\Models\PasswordReset;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function __construct(protected OtpService $otpService) {}

    //  me
    public function me()
    {
        $user = User::get();
        if (! $user) {
            throw new ApiException(
                'Account does not exist',
                404
            );
        }

        return $user;
    }

    // Registration Service
    public function register(array $data)
    {
        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'full_name' => $data['fullname'],
        ]);

        // assign starter plan
        $plan = SubscriptionPlan::where('slug', 'starter')->first();

        Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
        ]);

        // SEND OTP AFTER REGISTER
        $this->otpService->sendOtp($user->email);

        return [
            'message' => 'Verification code sent to email',
            'user' => $user,
        ];
    }

    public function login(array $data)
    {
        $email = $data['email'];
        $password = $data['password'];
        $meta = $data['meta'] ?? [];

        // 1. Find user
        $user = User::where('email', $email)->first();

        if (! $user) {
            throw new ApiException(
                'Account does not exist',
                404
            );
        }

        // 2. If NOT verified → auto send OTP and block login
        if (! $user->is_verified) {

            app(OtpService::class)->sendOtp($user->email);

            return [
                'requiresVerification' => true,
                'email' => $user->email,
                'message' => 'Verification required. We sent a new code to your email.',
            ];
        }

        // 3. Check password
        if (! Hash::check($password, $user->password)) {
            throw new ApiException(
                'Incorrect password',
                401
            );
        }

        // 4. Generate JWT access token
        $accessToken = auth('api')->login($user);

        // 5. Generate refresh token (simple version)
        $refreshToken = Str::random(80);

        // 6. Store device session
        DeviceSession::create([
            'user_id' => $user->id,
            'refresh_token' => $refreshToken,
            'device_name' => $meta['deviceName'] ?? null,
            'ip_address' => $meta['ipAddress'] ?? request()->ip(),
            'user_agent' => $meta['userAgent'] ?? request()->userAgent(),
            'expires_at' => now()->addDays(30),
            'last_used_at' => now(),
        ]);

        // 7. Return safe user data
        $safeUser = $user->only([
            'id',
            'email',
            'full_name',
            'is_verified',
            'created_at',
        ]);

        return [
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken,
            'user' => $safeUser,
        ];
    }

    public function refreshToken(array $data)
    {
        $token = $data['refreshToken'] ?? null;

        if (! $token) {
            throw new ApiException('Refresh token required', 400);
        }

        // 1. Find session (single source of truth)
        $session = DeviceSession::where('refresh_token', $token)->first();

        if (! $session) {
            throw new ApiException('Session not found', 401);
        }

        // 2. Check expiry
        if (now()->greaterThan($session->expires_at)) {
            $session->delete();

            throw new ApiException('Session expired', 401);
        }

        // 3. Get user
        $user = User::find($session->user_id);

        if (! $user) {
            throw new ApiException('User not found', 404);
        }

        // 4. Generate new tokens
        $newAccessToken = auth('api')->login($user);

        $newRefreshToken = Str::random(80);

        // 5. Rotate session (IMPORTANT SECURITY STEP)
        $session->update([
            'refresh_token' => $newRefreshToken,
            'expires_at' => now()->addDays(30),
            'last_used_at' => now(),
        ]);

        // 6. Return new tokens
        return [
            'accessToken' => $newAccessToken,
            'refreshToken' => $newRefreshToken,
        ];
    }

    public function logout(array $data)
    {
        $refreshToken = $data['refreshToken'] ?? null;

        if (! $refreshToken) {
            throw new ApiException('Refresh token required', 400);
        }

        // 1. Find session
        $session = DeviceSession::where('refresh_token', $refreshToken)->first();

        // 2. If session doesn't exist → still return success (idempotent logout)
        if (! $session) {
            return [
                'message' => 'Logged out successfully',
            ];
        }

        // 3. Delete session
        $session->delete();

        return [
            'message' => 'Logged out successfully',
        ];
    }

    public function forgotPassword(array $data)
    {
        $email = $data['email'];

        if (! $email) {
            throw new ApiException('Email is required', 400);
        }

        // 1. Check user exists
        $user = User::where('email', $email)->first();

        if (! $user) {
            throw new ApiException('Account not found', 404);
        }

        // 2. Cooldown check (60 seconds)
        $lastReset = PasswordReset::where('user_id', $user->id)
            ->latest('created_at')
            ->first();

        if ($lastReset) {
            $secondsPassed = $lastReset->created_at->diffInSeconds(now());

            if ($secondsPassed < 60) {
                $remaining = max(0, (int) ceil(60 - $secondsPassed));

                throw new ApiException(
                    "Please wait {$remaining} seconds before requesting another code",
                    429
                );
            }
        }

        // 3. Delete old reset requests
        PasswordReset::where('user_id', $user->id)->delete();

        // 4. Generate OTP
        $otp = random_int(100000, 999999);

        // 5. Hash OTP
        $hashedOtp = Hash::make($otp);

        // 6. Save reset record
        PasswordReset::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'otp' => $hashedOtp,
            'expires_at' => now()->addMinutes(5),
            // 'created_at' => now(),
        ]);

        // 7. Send email
        app(OtpService::class)->sendForgotPasswordOtp(
            $user->email,
            $otp
        );

        return [
            'message' => 'Password reset code sent in your registered email.',
        ];
    }

    public function resetPassword(array $data)
    {
        $email = $data['email'];
        $otp = $data['otp'];
        $newPassword = $data['newPassword'];

        // 1. Validate input
        if (! $email || ! $otp || ! $newPassword) {
            throw new ApiException('All fields are required', 400);
        }

        // 2. Find user
        $user = User::where('email', $email)->first();

        if (! $user) {
            throw new ApiException('Account does not exist', 404);
        }

        // 3. Get latest valid OTP
        $record = PasswordReset::where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $record) {
            throw new ApiException('OTP expired', 400);
        }

        // 4. Verify OTP
        if (! Hash::check($otp, $record->otp)) {
            throw new ApiException('Invalid OTP', 400);
        }

        // 5. Hash new password
        $hashedPassword = Hash::make($newPassword);

        // 6. Update password
        $user->update([
            'password' => $hashedPassword,
        ]);

        // 7. Delete reset OTPs
        PasswordReset::where('user_id', $user->id)->delete();

        // 8. Logout ALL sessions (security step)
        DeviceSession::where('user_id', $user->id)->delete();

        return [
            'message' => 'Password reset successful',
        ];
    }
}
