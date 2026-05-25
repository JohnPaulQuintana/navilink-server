<?php

namespace App\Http\Controllers\Api\Category;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryRequest;
use App\Services\Category\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // initialize categoryService layer
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    // get all the category
    public function getCategories()
    {
        $result = $this->categoryService
            ->index();

        return response()->json($result, 201);
    }

    // register new category
    public function createCategory(CategoryRequest $request)
    {
        $result = $this->categoryService
            ->create($request->validated());

        return response()->json($result, 201);
    }

    // delete category by id

    public function deleteCategoryById($id)
    {
        $result = $this->categoryService->delete($id);

        return response()->json($result, 200);
    }

    public function updateCategory(CategoryRequest $request, $id)
    {
        $result = $this->categoryService->update(
            $id,
            $request->validated()
        );

        return response()->json($result, 200);
    }

    public function getPublishedLinksByCategory($categoryId)
    {
        $result = $this->categoryService
            ->getPublishedCategoryAndLink($categoryId);

        return response()->json([
            'success' => true,
            'message' => 'Published category and links fetched successfully',
            'data' => $result,
        ], 200);
    }

    public function togglePublishedCategory(Request $request)
    {
        $categoryId = $request->input('categoryId');
        $state = $request->input('state');

        if (! $categoryId || ! $state) {
            throw new ApiException('Category id and state are required', 400);
        }

        if (! in_array($state, ['public', 'private'])) {
            throw new ApiException('Invalid state value', 400);
        }

        $result = $this->categoryService->updatePublishedState(
            $categoryId,
            $state
        );

        return response()->json([
            'success' => true,
            'message' => 'Category publish state updated successfully',
            'data' => $result,
        ], 200);
    }
}
