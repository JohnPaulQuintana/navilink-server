<?php

namespace App\Services\Category;

use App\Exceptions\ApiException;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    public function index()
    {
        $user_id = Auth::id();

        if (! $user_id) {
            throw new ApiException('Unauthorized', 401);
        }

        $categories = Category::where('user_id', $user_id)
            ->latest()
            ->get();

        return [
            'success' => true,
            'message' => 'Categories fetched successfully',
            'data' => $categories,
        ];
    }

    public function create(array $data)
    {
        $user_id = Auth::id();

        $name = isset($data['name'])
            ? strtolower(trim($data['name']))
            : null;

        $icon = $data['icon'] ?? null;

        $missingFields = [];

        if (! $name) {
            $missingFields[] = 'name';
        }

        if (! $icon) {
            $missingFields[] = 'icon';
        }

        if (! empty($missingFields)) {
            throw new ApiException(
                implode(', ', $missingFields).' are required',
                400
            );
        }

        // Duplicate check
        $duplicate_category = Category::where('user_id', $user_id)
            ->whereRaw('LOWER(name) = ?', [$name])
            ->first();

        if ($duplicate_category) {
            throw new ApiException(
                'Category already exists',
                409
            );
        }

        $created_category = Category::create([
            'user_id' => $user_id,
            'name' => $name,
            'icon' => $icon,
        ]);

        return [
            'message' => 'Category created successfully',
            'data' => $created_category,
        ];
    }

    public function delete($id)
    {
        $user_id = Auth::id();

        if (! $id) {
            throw new ApiException('Category id is required', 400);
        }

        $category = Category::where('id', $id)
            ->where('user_id', $user_id)
            ->first();

        if (! $category) {
            throw new ApiException('Category not found', 404);
        }

        // optional: protect system categories
        if ($category->is_system) {
            throw new ApiException('System category cannot be deleted', 403);
        }

        $category->delete();

        return [
            'success' => true,
            'message' => 'Category deleted successfully',
            'data' => $category,
        ];
    }

    public function update($id, array $data)
    {
        $user_id = Auth::id();

        $name = isset($data['name'])
            ? strtolower(trim($data['name']))
            : null;

        $icon = $data['icon'] ?? null;

        // Find category owned by user
        $category = Category::where('id', $id)
            ->where('user_id', $user_id)
            ->first();

        if (! $category) {
            throw new ApiException('Category not found', 404);
        }

        // Optional: prevent editing system categories
        if ($category->is_system) {
            throw new ApiException('System category cannot be edited', 403);
        }

        // Check duplicate name (ignore current category)
        if ($name) {
            $exists = Category::where('user_id', $user_id)
                ->whereRaw('LOWER(name) = ?', [$name])
                ->where('id', '!=', $id)
                ->first();

            if ($exists) {
                throw new ApiException('Category already exists', 409);
            }

            $category->name = $name;
        }

        if ($icon) {
            $category->icon = $icon;
        }

        $category->save();

        return [
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category,
        ];
    }

    public function getPublishedCategoryAndLink($categoryId)
    {
        $user_id = Auth::id();

        // 1. Find category owned by user
        $category = Category::where('id', $categoryId)
            ->where('user_id', $user_id)
            ->first();

        if (! $category) {
            throw new ApiException('Category not found', 404);
        }

        // 2. Get published links inside category
        // $links = Link::where('category_id', $categoryId)
        //     ->where('published', 'public')
        //     ->latest()
        //     ->get();

        // 3. Return combined response
        return [
            'category' => $category,
            // 'links' => $links,
        ];
    }

    public function updatePublishedState($categoryId, $state)
    {
        $user_id = Auth::id();

        $category = Category::where('id', $categoryId)
            ->where('user_id', $user_id)
            ->first();

        if (! $category) {
            throw new ApiException('Category not found', 404);
        }

        // Optional: prevent system category changes
        if ($category->is_system) {
            throw new ApiException('System category cannot be modified', 403);
        }

        $category->published = $state;
        $category->save();

        return $category;
    }
}
