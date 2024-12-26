<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExtractedItem.
 *
 * Items are extracted from Activiteitenbank and stored here for later processing.
 *
 * @property int    $id
 * @property int    $original_id
 * @property string $original_slug
 * @property int    $hits
 * @property string $raw_content // The raw HTML from #tc4-maincontent containing all data.
 *
 * @property Carbon $extracted_at
 * @property Carbon $published_at
 * @property Carbon $modified_at // This model is read-only, but contains the date the original was last modified.
 *
 * @property int    $applied_to
 */
class ExtractedItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'original_id',
        'original_slug',
        'hits',
        'raw_content',
        'extracted_at',
        'published_at',
        'modified_at',
        'applied_to',
    ];

    protected $casts = [
        'extracted_at' => 'datetime',
        'published_at' => 'datetime',
        'modified_at'  => 'datetime',
    ];
}
