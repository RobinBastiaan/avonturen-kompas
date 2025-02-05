<?php

namespace App\Http\Controllers\CsvExport;

use Illuminate\Support\Collection;
use Illuminate\Http\Response;

/**
 * Convenient generic formatting and response building methods useful during the CSV export.
 */
trait CsvExportTrait
{
    /**
     * Format the data including headings for CSV output.
     */
    protected function formatData(array $headings, Collection $data): string
    {
        return collect([$this->formatRow($headings)])
            ->concat($data->map(fn($row) => $this->formatRow($row))->all())
            ->join("\n");
    }

    /**
     * Format a row for CSV output.
     */
    protected function formatRow(array $row): string
    {
        return collect($row)
            ->map(fn($value) => $this->formatString($value))
            ->join(',');
    }

    /**
     * Escape characters and add a wrapper for correct output of each cell.
     */
    protected function formatString(string $value): string
    {
        // To escape double quotes (") in CSV data, use another double quote as an escape character.
        $value = str_replace('"', '""', $value);

        // Surround each value with quotes to they get recognised as a single field. This enables using comma's in the value.
        return "\"{$value}\"";
    }

    /**
     * Build the response and set the headers to be for csv.
     */
    protected function getCsvResponse(string $fileName, string $csv): Response
    {
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '.csv"')
            ->header('Content-Title', 'CVS export van ' . ucfirst($fileName));
    }
}
