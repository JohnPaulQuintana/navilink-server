<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Link;
class ScrapeLinkJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $linkId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $link = Link::find($this->linkId);

        if (! $link) {
            return;
        }

        $response = Http::timeout(120)
            ->connectTimeout(30)
            ->retry(2, 5000)
            ->post(
                env('SCRAPER_API').'/link',
                [
                    'url' => $link->url,
                ]
            );

        if (! $response->successful()) {
            return;
        }

        $scraped = $response->json('data');

        if (! empty($scraped['image']) &&
            str_contains($scraped['image'], 'base64')) {

            $image = $scraped['image'];

            $image = str_replace(
                'data:image/png;base64,',
                '',
                $image
            );

            $image = str_replace(
                'data:image/jpeg;base64,',
                '',
                $image
            );

            $image = str_replace(' ', '+', $image);

            $imageName = 'links/'.uniqid().'.png';

            Storage::disk('public')->put(
                $imageName,
                base64_decode($image)
            );

            $imagePath = $imageName;

        } else {
            $imagePath = $scraped['image'] ?? null;
        }

        $link->update([
            'title' => $scraped['title'] ?? 'Untitled',
            'url' => $scraped['url'] ?? $link->url,

            'description' => $scraped['description'] ?? null,
            'image' => $imagePath,
            'favicon' => $scraped['favicon'] ?? null,
            'domain' => $scraped['domain'] ?? null,

            'platform' => $scraped['platform'] ?? null,
            'safety_status' => $scraped['safety_status'] ?? 'unknown',

            'issynced' => true,
        ]);
    }
}
