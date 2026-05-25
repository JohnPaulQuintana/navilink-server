<?php

namespace App\Services\Auth;

use App\Exceptions\ApiException;
use App\Mail\ForgotPasswordOtpMail;
use App\Mail\VerifyOtpMail;
use App\Models\DeviceSession;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OtpService
{
    const RESEND_COOLDOWN = 60; // seconds

    public function sendOtp($email)
    {
        // 1. check last OTP
        $lastOtp = Otp::where('email', $email)
            ->latest()
            ->first();

        if ($lastOtp) {
            $diff = now()->diffInSeconds($lastOtp->created_at);

            if ($diff < self::RESEND_COOLDOWN) {
                throw new \Exception('Please wait 60 seconds before requesting another OTP');
            }
        }

        // 2. delete old OTPs
        Otp::where('email', $email)->delete();

        // 3. generate OTP
        $otp = rand(100000, 999999);

        // 4. save hashed OTP
        Otp::create([
            'email' => $email,
            'code_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
        ]);

        // 5. send email
        // Mail::raw("Your NaviLink OTP is: $otp", function ($message) use ($email) {
        //     $message->to($email)
        //         ->subject('Verify your NaviLink account');
        // });
        Mail::to($email)->send(new VerifyOtpMail($otp));

        return true;
    }

    public function verifyOtp(array $data)
    {
        $email = $data['email'];
        $otp = $data['otp'];

        // 1. Get latest valid OTP (not expired)
        $record = Otp::where('email', $email)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $record) {
            throw new ApiException(
                'Your verification code has expired. Please request a new one.',
                400
            );
        }

        // 2. Validate OTP
        $isValid = Hash::check($otp, $record->code_hash);

        if (! $isValid) {
            throw new ApiException(
                'The code you entered is incorrect',
                400
            );
        }

        // 3. Get user
        $user = User::where('email', $email)->first();

        if (! $user) {
            throw new ApiException(
                'User not found',
                404
            );
        }

        // 4. Mark user as verified
        $user->update([
            'is_verified' => true,
        ]);

        // 5. Delete OTP
        Otp::where('email', $email)->delete();

        // 6. Create tokens (JWT example placeholder)
        $accessToken = auth('api')->login($user);
        $refreshToken = Str::random(60); // or JWT refresh if you use refresh system

        // 7. Store session (optional but recommended)
        DeviceSession::create([
            'user_id' => $user->id,
            'refresh_token' => $refreshToken,
            'expires_at' => now()->addDays(7),
        ]);

        // 8. Clean user data
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

    public function resendOtp(array $data)
    {
        $email = $data['email'];

        if (! $email) {
            throw new ApiException('Email is required', 400);
        }

        // 1. Check user exists
        $user = User::where('email', $email)->first();

        if (! $user) {
            throw new ApiException(
                'We could not find an account with this email',
                404
            );
        }

        // 2. Check if already verified
        if ($user->is_verified) {
            throw new ApiException(
                'This account is already verified',
                400
            );
        }

        // 3. Delete old OTPs
        Otp::where('email', $email)->delete();

        // 4. Send new OTP
        app(OtpService::class)->sendOtp($email);

        return [
            'message' => 'A new verification code has been sent to your $email',
        ];
    }

    public function sendForgotPasswordOtp($email, $otp)
    {
        
        if (! $email) {
            throw new ApiException('Email is required', 400);
        }
        if (! $otp) {
            throw new ApiException('Email is required', 400);
        }

        Mail::to($email)->send(new ForgotPasswordOtpMail($email, $otp));

        return [
            'message' => 'Password reset code sent',
        ];
    }
}
