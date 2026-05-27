<?php

namespace App\Http\Controllers\Api\Onboarding;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOnboardingRequest;
use App\Services\Onboarding\OnboardingService;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function __construct(
        protected OnboardingService $onboardingService
    ) {}

    // update
    public function update(UpdateOnboardingRequest $request)
    {
        $result = $this->onboardingService
            ->update($request->validated());

        return response()->json($result, 201);
    }
}
