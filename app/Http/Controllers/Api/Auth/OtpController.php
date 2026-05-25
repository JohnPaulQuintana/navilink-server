<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Services\Auth\OtpService;

class OtpController extends Controller
{
    // initialize authService layer
    public function __construct(
        protected OtpService $otpService
    ) {}

    // OTP Vierifcation Process
    public function verifyOtp(VerifyOtpRequest $request)
    {
        $result = $this->otpService->verifyOtp(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully',
            'data' => $result,
        ]);
    }

    // Resend Otp
    public function resendOtp(ResendOtpRequest $request)
    {
        $result = $this->otpService->resendOtp(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'A new verification code has been sent',
            'data' => $result,
        ]);
    }
}
