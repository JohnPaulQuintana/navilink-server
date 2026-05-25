<?php

namespace App\Http\Controllers\Api\Link;

use App\Http\Controllers\Controller;
use App\Http\Requests\Link\LinkRequest;
use App\Services\Link\LinkService;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function __construct(
        protected LinkService $linkService
    ) {}

    // CREATE LINK
    public function create(LinkRequest $request)
    {
        $result = $this->linkService
            ->createLink($request->validated());

        return response()->json($result, 201);
    }

    // GET LINKS
    public function index(Request $request)
    {
        $categoryId = $request->query('categoryId');

        $result = $this->linkService->getLink($categoryId);

        return response()->json($result, 200);
    }

    // DELETE LINK
    public function delete($id)
    {
        $result = $this->linkService->deleteLink($id);

        return response()->json($result, 200);
    }
}