<?php

namespace App\Http\Controllers\Api\Community;

use App\Http\Controllers\Controller;
use App\Services\Community\CommunityService;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function __construct(
        protected CommunityService $communityService
    ) {}

    // GET LINKS
    public function index()
    {

        $result = $this->communityService->getAllPublicCategory();

        return response()->json($result, 200);
    }

}
