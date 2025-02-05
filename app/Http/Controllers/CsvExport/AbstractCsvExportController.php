<?php

namespace App\Http\Controllers\CsvExport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Class AbstractCsvExportController.
 *
 * Export various data as a CSV file. Especially useful for use in a spreadsheet.
 */
abstract class AbstractCsvExportController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * Export as comma separated values (CSV) for easy paste in spreadsheets.
     */
    abstract public function export(): Response;
}
