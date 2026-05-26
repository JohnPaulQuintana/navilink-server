<?php

namespace App\Jobs;

use App\Models\Link;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class ScrapeLinkJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 30;

    public $tries = 2;

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

        $response = Http::timeout(20)
            ->connectTimeout(10)
            ->retry(1, 3000)
            ->post(
                'https://linkvault-api-tuna.onrender.com/api/v1/scrapper/link',
                [
                    'url' => $link->url,
                ]
            );

        if (! $response->successful()) {
            return;
        }

        $scraped = $response->json('data');

        $link->update([
            'title' => $scraped['title'] ?? 'Untitled',
            'url' => $scraped['url'] ?? $link->url,

            'description' => $scraped['description'] ?? null,
            'image' => $scraped['image'] ?? null,
            'favicon' => $scraped['favicon'] ?? null,
            'domain' => $scraped['domain'] ?? null,

            'platform' => $scraped['platform'] ?? null,
            'safety_status' => $scraped['safety_status'] ?? 'unknown',

            'issynced' => true,
        ]);
    }
}