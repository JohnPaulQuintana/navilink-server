<?php

namespace App\Http\Controllers\Api\Feedback;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feedback\FeedbackRequest;
use App\Services\Feedback\FeedbackService;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function __construct(
        protected FeedbackService $feedbackService
    ) {}

    /**
     * CREATE FEEDBACK
     */
    public function create(FeedbackRequest $request)
    {
        $result = $this->feedbackService->createFeedback(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Feedback submitted successfully',
            'data' => $result
        ], 201);
    }

    /**
     * GET FEEDBACK (list with filters)
     */
    public function index(Request $request)
    {
        $result = $this->feedbackService->getFeedback([
            'category' => $request->query('category'),
            'limit' => $request->query('limit', 20),
            'offset' => $request->query('offset', 0),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feedback fetched successfully',
            'data' => $result,
        ]);
    }

    /**
     * FEEDBACK STATS (total + avg rating)
     */
    public function stats()
    {
        $result = $this->feedbackService->getFeedbackStats();

        return response()->json([
            'success' => true,
            'message' => 'Feedback stats fetched successfully',
            'data' => $result,
        ]);
    }
}