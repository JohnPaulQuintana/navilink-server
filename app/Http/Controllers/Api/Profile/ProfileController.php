<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ProfileRequest;
use App\Services\Profile\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        protected ProfileService $profileService
    ) {}

    public function update(ProfileRequest $request)
    {
        $result = $this->profileService->updateProfile($request->validated());

        return response()->json($result);
    }
}
