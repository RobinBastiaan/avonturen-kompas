<?php

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

class TagController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * Export as comma separated values (CSV) for easy paste in spreadsheets.
     */
    public function export(): Response
    {
        $tags = Tag::with('items')->orderBy('name')->get();

        $csv = $this->buildCsv($tags);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="tags.csv"');
    }

    /**
     * Build the CSV data including headings.
     */
    protected function buildCsv(Collection $tags): string
    {
        $headings = ['Tag,Omschrijving,Aantal,Items'];

        return collect($headings)->concat(
            $tags->map(function (Tag $tag) {
                $items = $tag->items->pluck('title')->join(', ');

                return collect([$tag->name, $tag->description ?? '', $tag->use_count, $items])
                    ->map(fn($value) => $this->formatCsvString($value))
                    ->join(',');
            })->all()
        )->join("\n");
    }

    /**
     * Escape characters and add a wrapper for correct output of each cell.
     */
    protected function formatCsvString(string $value): string
    {
        // To escape double quotes (") in CSV data, use another double quote as an escape character.
        $value = str_replace('"', '""', $value);

        // Surround each value with quotes to they get recognised as a single field. This enables using comma's in the value.
        return "\"{$value}\"";
    }
}
