<?php

namespace App\Services\Community;

use App\Models\Category;
use App\Exceptions\ApiException;

class CommunityService
{
    public function getAllPublicCategory()
    {
        $categories = Category::with([
            'author:id,full_name',

            'links' => function ($query) {
                $query->select(
                        'id',
                        'category_id',
                        'title',
                        'url',
                        'description',
                        'image',
                        'domain',
                        'safety_status',
                        'created_at'
                    )
                    ->where('safety_status', 'safe')
                    ->latest();
            }
        ])
        ->select(
            'id',
            'user_id',
            'name',
            'icon',
            'published',
            'created_at'
        )
        ->where('published', 'public')
        ->latest()
        ->get();

        return [
            'success' => true,
            'data' => $categories
        ];
    }
}