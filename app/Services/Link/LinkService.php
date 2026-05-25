<?php

namespace App\Services\Link;

use App\Exceptions\ApiException;
use App\Jobs\ScrapeLinkJob;
use App\Models\Category;
use App\Models\Link;
use Illuminate\Support\Facades\Auth;

class LinkService
{
    public function createLink(array $data)
    {
        $user_id = Auth::id();

        $url = $data['url'] ?? null;
        $category_id = $data['category_id'] ?? null;

        if (! $url) {
            throw new ApiException(
                'Please submit a valid url',
                400
            );
        }

        if (! $category_id) {
            throw new ApiException(
                'Category not found',
                404
            );
        }

        if (! $user_id) {
            throw new ApiException(
                'Unauthorized request',
                401
            );
        }

        $category = Category::where('id', $category_id)
            ->where('user_id', $user_id)
            ->first();

        if (! $category) {
            throw new ApiException(
                'Category does not exist',
                404
            );
        }

        $normalizedUrl = strtolower(trim($url));

        $existingLink = Link::where('user_id', $user_id)
            ->whereRaw('LOWER(url) = ?', [$normalizedUrl])
            ->first();

        if ($existingLink) {
            throw new ApiException(
                'Link already exists',
                409
            );
        }

        // CREATE PLACEHOLDER LINK
        $link = Link::create([
            'user_id' => $user_id,
            'category_id' => $category_id,

            'title' => 'Fetching preview...',
            'url' => $url,

            'description' => null,
            'image' => null,
            'favicon' => null,
            'domain' => parse_url($url, PHP_URL_HOST),

            'platform' => null,
            'safety_status' => 'unknown',

            'issynced' => false,
            'visited_date' => now(),
        ]);

        // DISPATCH SCRAPER JOB
        ScrapeLinkJob::dispatch($link->id);

        return [
            'success' => true,
            'message' => 'Link created successfully',
            'data' => $link,
        ];
    }

    public function getLink($categoryId = null)
    {
        $user_id = Auth::id();

        $query = Link::where('user_id', $user_id);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $links = $query->latest()->get();

        return [
            'success' => true,
            'message' => 'Links fetched successfully',
            'data' => $links,
        ];
    }

    public function deleteLink($id)
    {
        $user_id = Auth::id();

        if (! $id) {
            throw new ApiException('Link id is required', 400);
        }

        $link = Link::where('id', $id)
            ->where('user_id', $user_id)
            ->first();

        if (! $link) {
            throw new ApiException('Link not found', 404);
        }

        $link->delete();

        return [
            'success' => true,
            'message' => 'Link deleted successfully',
            'data' => $link,
        ];
    }
}
