<?php

namespace App\Services\Onboarding;

use App\Exceptions\ApiException;

class OnboardingService
{
    /**
     * =========================
     * Update Onboarding Status
     * =========================
     */
    public function update(array $data)
    {
        $status = $data['status'] ?? null;

        if (! $status) {
            throw new ApiException(
                'All fields are required',
                400
            );
        }

        $user = auth()->user();

        if (! $user) {
            throw new ApiException(
                'Unauthorized',
                401
            );
        }

        $user->onboarding_status = $status;
        $user->save();

        return [
            'success' => true,
            'message' => 'Onboarding status updated successfully',
            'user' => $user,
        ];
    }
}
