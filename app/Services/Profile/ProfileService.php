<?php

namespace App\Services\Profile;

use App\Exceptions\ApiException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileService
{
    public function updateProfile(array $data)
    {
        $user_id = Auth::id();

        if (! $user_id) {
            throw new ApiException('Unauthorized request', 401);
        }

        $full_name = $data['full_name'] ?? null;
        $email = $data['email'] ?? null;

        // Validation
        if (! $full_name && ! $email) {
            throw new ApiException(
                'Nothing to update',
                400
            );
        }

        $user = User::find($user_id);

        if (! $user) {
            throw new ApiException('User not found', 404);
        }

        // Check email uniqueness if updating email
        if ($email && $email !== $user->email) {
            $exists = User::where('email', $email)->first();

            if ($exists) {
                throw new ApiException('Email already in use', 409);
            }

            $user->email = $email;
        }

        if ($full_name) {
            $user->full_name = $full_name;
        }

        $user->save();

        return [
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ];
    }
}