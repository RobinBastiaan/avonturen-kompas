<?php

namespace App\Models;

use App\Models\Scopes\PublishedScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ExtractedItem.
 *
 * ExtractedItems are quickly extracted from Activiteitenbank and stored here for later processing.
 *
 * @property int         $id
 * @property int         $original_id
 * @property string      $original_slug
 * @property int         $hits
 * @property string      $raw_content // The raw HTML from #tc4-maincontent containing all data.
 *
 * @property Carbon|null $extracted_at
 * @property Carbon|null $published_at
 * @property Carbon|null $modified_at // This model is read-only, but contains the date the original was last modified.
 * @property string      $author_name
 *
 * @property int|null    $applied_to
 *
 * @property Item|null   $appliedTo
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
        'author_name',
        'applied_to',
    ];

    protected $casts = [
        'extracted_at' => 'datetime',
        'published_at' => 'datetime',
        'modified_at'  => 'datetime',
    ];

    public function appliedTo(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'applied_to')
            ->withoutGlobalScope(PublishedScope::class);
    }
}
