<?php

namespace App\Services\Feedback;

use App\Exceptions\ApiException;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;

class FeedbackService
{
    /**
     * CREATE FEEDBACK
     */
    public function createFeedback(array $data)
    {
        $user_id = Auth::id();

        if (! $user_id) {
            throw new ApiException('Unauthorized request', 401);
        }

        $category = isset($data['category']) ? strtolower(trim($data['category'])) : null;
        $rating = $data['rating'] ?? null;
        $message = isset($data['message']) ? trim($data['message']) : null;

        // Validation
        if (! $category) {
            throw new ApiException('Category is required', 400);
        }

        if (! $rating) {
            throw new ApiException('Rating is required', 400);
        }

        if ($rating < 1 || $rating > 5) {
            throw new ApiException('Rating must be between 1 and 5', 422);
        }

        /**
         * Duplicate check
         */
        $exists = Feedback::where('user_id', $user_id)
            ->where('category', $category)
            ->where('rating', $rating)
            ->where('message', $message)
            ->first();

        if ($exists) {
            throw new ApiException('You already submitted this feedback', 409);
        }

        /**
         * Cooldown (anti-spam)
         */
        $recent = Feedback::where('user_id', $user_id)
            ->where('created_at', '>=', now()->subSeconds(30))
            ->first();

        if ($recent) {
            throw new ApiException('You are submitting too fast. Please slow down.', 429);
        }

        return Feedback::create([
            'user_id' => $user_id,
            'category' => $category,
            'rating' => $rating,
            'message' => $message ?: null,
        ]);
    }

    /**
     * GET FEEDBACK (Supabase-style)
     */
    public function getFeedback(array $params = [])
    {
        $category = $params['category'] ?? null;
        $limit = $params['limit'] ?? 20;
        $offset = $params['offset'] ?? 0;

        $query = Feedback::with([
            'user:id,full_name,email,is_verified',
        ])
            ->select([
                'id',
                'user_id',
                'category',
                'rating',
                'message',
                'created_at',
            ])
            ->orderBy('created_at', 'desc');

        // Filter by category
        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        return $query
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    public function getFeedbackStats()
    {
        // total feedback count
        $total = Feedback::count();

        // average rating (SQL-level aggregation = FAST ⚡)
        $avgRating = Feedback::avg('rating') ?? 0;

        return [
            'total' => $total,
            'avgRating' => round((float) $avgRating, 2),
        ];
    }
}
