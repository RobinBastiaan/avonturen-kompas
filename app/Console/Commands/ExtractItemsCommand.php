<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ExtractItemsCommand extends Command
{
    // The last item is dynamic and not known. When an original item is either removed or not published, it is skipped and logged a failure.
    // Therefore, to stop extracting when there are no items left, there is a max consecutive failures. Note there exists known gaps of
    // hundreds consecutive items missing, but soms of them are gracefully skipped.
    const int MAX_CONSECUTIVE_FAILURES = 99;
    protected $signature = 'app:extract:items {id? : Item ID to start extracting from}';
    protected $description = 'Extract all items data from the Activiteitenbank.';

    public function handle(): int
    {
        $currentId = (int) ($this->argument('id') ?? 1);
        $failedCount = 0;

        while ($failedCount < self::MAX_CONSECUTIVE_FAILURES) {
            // Skip some large known missing item gaps to speed up extraction and reduce the need to make MAX_CONSECUTIVE_FAILURES too large.
            if (($currentId > 1251 && $currentId < 2048) || ($currentId > 2468 && $currentId < 6086) || ($currentId > 6409 && $currentId < 6918)
                || ($currentId > 7087 && $currentId < 7123) || ($currentId > 7153 && $currentId < 7190) || ($currentId > 9703 && $currentId < 10027)
                || ($currentId > 10464 && $currentId < 10625) || ($currentId > 11656 && $currentId < 11686) || ($currentId > 12977 && $currentId < 13055)) {
                $this->warn("Skipped {$currentId} because it is a known gap item.");
                $currentId++;
                continue;
            }

            try {
                $exitCode = $this->call('app:extract:item', [
                    'id' => $currentId,
                ]);

                if ($exitCode === CommandAlias::SUCCESS) {
                    $failedCount = 0;
                } else {
                    $failedCount++;
                }

                $currentId++;
            } catch (\Exception $e) {
                $this->error("Failed to extract item {$currentId}: " . $e->getMessage());
                $failedCount++;
            }
        }

        $this->info("Extraction completed after {$currentId} items with {$failedCount} consecutive failures.");
        return CommandAlias::SUCCESS;
    }
}
