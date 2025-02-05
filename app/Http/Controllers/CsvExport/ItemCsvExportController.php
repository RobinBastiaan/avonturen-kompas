<?php

namespace App\Http\Controllers\CsvExport;

use App\Models\Category;
use App\Models\Hits;
use App\Models\Item;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

/**
 * Class ItemCsvExportController.
 *
 * Export an overview of item data with the age group category as "x" and tags as a list,
 * including as much hit columns as needed for all historical hits.
 */
class ItemCsvExportController extends AbstractCsvExportController
{
    use CsvExportTrait;

    /**
     * Handle the incoming request.
     *
     * Export as comma separated values (CSV) for easy paste in spreadsheets.
     */
    public function export(): Response
    {
        /** @var Collection|Hits[] $ageGroups */
        $hitsDates = Hits::distinct()->orderBy('extracted_at', 'asc')->pluck('extracted_at');
        /** @var Collection|Category[] $ageGroups */
        $ageGroups = Category::where('category_group_id', 1)->pluck('name');

        $headings = $this->buildHeadingsWithDynamicHitColumns($hitsDates, $ageGroups);
        $items = $this->getItems();

        $csv = $this->formatData($headings, $this->mapData($items, $hitsDates, $ageGroups));

        return $this->getCsvResponse('items', $csv);
    }

    protected function buildHeadingsWithDynamicHitColumns(Collection $hitsDates, Collection $ageGroups): array
    {
        $headings = ['Gemaakt op', 'Activiteit'];

        foreach ($hitsDates as $date) {
            $headings[] = 'Hits ' . $date->translatedFormat('M Y');
        }

        // The age group column labels can to be strongly abbreviated since the value will map to only an "x".
        $ageGroupLabels = [
            '5-7 Bevers'            => 'BEV',
            '7-11 Welpen'           => 'WEL',
            '11-15 Scouts'          => 'SCO',
            '15-18 Explorers'       => 'EXP',
            '18-21 Roverscouts'     => 'ROV',
            'Kader'                 => 'KAD',
            'Alle leeftijden samen' => 'ALL',
        ];
        foreach ($ageGroups as $ageGroup) {
            $headings[] = $ageGroupLabels[$ageGroup] ?? $ageGroup;
        }

        return array_merge($headings, ['Tags']);
    }

    protected function getItems(): Collection
    {
        return Item::query()
            ->with('historicalHits')
            ->with('categories', function ($query) {
                $query->where('category_group_id', 1); // Only select age groups.
            })
            ->with('tags')
            ->orderBy('original_id')
            ->get();
    }

    /**
     * Prepare the data rows for CSV export.
     */
    protected function mapData(Collection $data, Collection $hitsDates, Collection $ageGroups): Collection
    {
        return $data->map(function (Item $item) use ($hitsDates, $ageGroups) {
            // Start with first columns.
            $row = [
                $item->created_at->format('Y-m-d'),
                '=HYPERLINK("https://activiteitenbank.scouting.nl/component/k2/item/' . $item->original_id . '-' . $item->slug . '";"' . $item->title . '")',
            ];

            // Add hits for each date.
            foreach ($hitsDates as $date) {
                $hit = $item->historicalHits->where('extracted_at', $date)->first();
                $row[] = $hit ? $hit->hits : 0;
            }

            // Add age group columns.
            foreach ($ageGroups as $ageGroup) {
                $hasAgeGroup = $item->categories->where('name', $ageGroup)->isNotEmpty();
                $row[] = $hasAgeGroup ? 'x' : '';
            }

            // Add remaining column(s).
            return array_merge($row, [
                $item->tags->pluck('name')->join(', '),
            ]);
        });
    }
}
