<?php

namespace App\Console\Commands;

use App\Models\ExtractedItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Symfony\Component\DomCrawler\Crawler;

class ExtractItemCommand extends Command
{
    protected $signature = 'app:extract:item {id : The item ID to extract}';
    protected $description = 'Extract item data from the Activiteitenbank.';

    public function handle(): int
    {
        // Try to retrieve the item but note it is impossible to know whether it removed or simply not published.
        try {
            $id = $this->argument('id');

            $response = Http::withoutVerifying()->get('https://activiteitenbank.scouting.nl/component/k2/item/' . $id);
            $crawler = new Crawler($response->body());

            $content = trim($crawler->filter('#tc4-maincontent')->html());
            $canonicalUrl = $crawler->filter('link[rel="canonical"]')->attr('href');
            $originalSlug = basename($canonicalUrl);
            $originalSlug = substr($originalSlug, strpos($originalSlug, '-') + 1); // Remove the ID prefix
            $hits = (int) $crawler->filter('span.itemHits b')->text();

            // Extract data from JSON-LD.
            $jsonLd = $crawler->filter('script[type="application/ld+json"]')->text();
            $jsonData = json_decode($jsonLd, true, 512, JSON_THROW_ON_ERROR);
            $publishedAt = $jsonData['datePublished'];
            $modifiedAt = $jsonData['dateModified'];

            // Store content in a database table.
            $extractedItem = new ExtractedItem();
            $extractedItem->original_id = $id;
            $extractedItem->original_slug = $originalSlug;
            $extractedItem->hits = $hits;
            $extractedItem->raw_content = $content;
            $extractedItem->extracted_at = now();
            $extractedItem->published_at = $publishedAt;
            $extractedItem->modified_at = $modifiedAt;
            $extractedItem->save();

            $this->info("Successfully extracted item {$id}!");
        } catch (\Exception $e) {
            $this->error("Failed to extract item {$id}: " . $e->getMessage());
            return CommandAlias::FAILURE;
        }

        return CommandAlias::SUCCESS;
    }
}
