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
    protected $description = 'Extract item data from the Activiteitenbank, and simply store without creating related entries.';

    public function handle(): int
    {
        $id = $this->argument('id');

        // Try to retrieve the item but note it is impossible to know whether it removed or simply unpublished.
        try {
            $response = Http::withoutVerifying()
                ->timeout(5)
                ->get('https://activiteitenbank.scouting.nl/component/k2/item/' . $id);
            $crawler = new Crawler($response->body());

            // Convert a url like 'https://example.com/123-my-slug' to 'my-slug'.
            $canonicalUrl = $crawler->filter('link[rel="canonical"]')->attr('href');
            $originalSlug = explode('-', basename($canonicalUrl), 2)[1];
            $hits = (int) $crawler->filter('span.itemHits b')->text();
            $content = trim($crawler->filter('#tc4-maincontent')->html());

            // Extract data from JSON-LD.
            $jsonLd = $crawler->filter('script[type="application/ld+json"]')->text();
            $jsonData = json_decode($jsonLd, true, 512, JSON_THROW_ON_ERROR);

            // Store content in a database table.
            $extractedItem = new ExtractedItem();
            $extractedItem->original_id = $id;
            $extractedItem->original_slug = $originalSlug;
            $extractedItem->hits = $hits;
            $extractedItem->raw_content = $content;
            $extractedItem->extracted_at = now();
            $extractedItem->published_at = $jsonData['datePublished'];
            $extractedItem->modified_at = $jsonData['dateModified'];
            // In the original data a Super User is often used as a fake author instead of null.
            $extractedItem->author_name = ($jsonData['author']['name'] === 'Super User' ? null : $jsonData['author']['name']);
            $extractedItem->save();

            $this->info("Successfully extracted item {$id}!");
        } catch (\Exception $e) {
            $this->error("Failed to extract item {$id}: " . $e->getMessage());
            return CommandAlias::FAILURE;
        }

        return CommandAlias::SUCCESS;
    }
}
