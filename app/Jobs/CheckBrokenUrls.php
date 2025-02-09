<?php

namespace App\Jobs;

use App\Models\Item;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Job to check for broken URLs in item content.
 *
 * This job scans through all items' content fields (description, requirements, tips, safety)
 * for URLs and tests each one to verify it is still accessible. Any broken URLs are cached
 * for display in the item control stats page.
 */
class CheckBrokenUrls implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $totalItems = 0;
    private int $processedItems = 0;

    public function handle(): void
    {
        /** @var Collection|Item[] $items */
        $items = Item::query()
            ->select(['id', 'title', 'slug', 'hash', 'description', 'requirements', 'tips', 'safety'])
            ->get();

        $this->totalItems = $items->count();
        $brokenUrls = $this->getBrokenUrls($items);

        // Store results in a cache.
        cache()->put('broken_urls', $brokenUrls, now()->addMonth());
    }

    protected function getBrokenUrls(Collection $items): Collection
    {
        $brokenUrls = collect();

        foreach ($items as $item) {
            $this->processedItems++;
            $this->updateProgress();

            $content = implode(' ', array_filter([$item->description, $item->requirements, $item->tips, $item->safety]));

            // Find all URLs using regex.
            preg_match_all('/https?:\/\/[^\s<>"]+/i', $content, $matches);
            $urls = $matches[0] ?? [];

            foreach ($urls as $url) {
                try {
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL            => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_NOBODY         => true,
                        CURLOPT_TIMEOUT        => 5,
                        CURLOPT_SSL_VERIFYPEER => true,
                        CURLOPT_FOLLOWLOCATION => true,
                    ]);

                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    if ($httpCode >= 400 || $response === false) {
                        $brokenUrls->push([
                            'item_hash'   => $item->hash,
                            'item_title'  => $item->title,
                            'item_slug'   => $item->slug,
                            'url'         => $url,
                            'status_code' => $httpCode,
                        ]);
                    }
                } catch (\Exception $e) {
                    $brokenUrls->push([
                        'item_hash'   => $item->hash,
                        'item_title'  => $item->title,
                        'item_slug'   => $item->slug,
                        'url'         => $url,
                        'status_code' => 'Error: ' . $e->getMessage(),
                    ]);
                }
            }
        }

        return $brokenUrls;
    }

    protected function updateProgress(): void
    {
        $percentage = round(($this->processedItems / $this->totalItems) * 100);

        Log::info("Checking URLs progress: {$percentage}% ({$this->processedItems}/{$this->totalItems})");
    }
}
