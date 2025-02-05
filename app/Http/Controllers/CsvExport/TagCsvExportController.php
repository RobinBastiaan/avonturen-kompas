<?php

namespace App\Http\Controllers\CsvExport;

use App\Models\Tag;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

/**
 * Class TagCsvExportController.
 *
 * Export an overview of tag data including the related items.
 */
class TagCsvExportController extends AbstractCsvExportController
{
    use CsvExportTrait;

    /**
     * Handle the incoming request.
     *
     * Export as comma separated values (CSV) for easy paste in spreadsheets.
     */
    public function export(): Response
    {
        $headings = ['Tag', 'Omschrijving', 'Aantal', 'Items'];
        $tags = Tag::with('items')->orderBy('name')->get();

        $csv = $this->formatData($headings, $this->mapData($tags));

        return $this->getCsvResponse('tags', $csv);
    }

    /**
     * Prepare the data rows for CSV export.
     */
    protected function mapData(Collection $data): Collection
    {
        return $data->map(function (Tag $tag) {
            return [
                $tag->name,
                $tag->description ?? '',
                $tag->use_count,
                $tag->items->pluck('title')->join(', '),
            ];
        });
    }
}
