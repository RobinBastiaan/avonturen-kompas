<?php

namespace App\Models;

use App\Models\Scopes\PublishedScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Hits.
 *
 * Tracks the amount of views/hits for items over time. The data is stored in a compact table structure
 * optimized for fast querying. These hits can be populated either through automated extraction or manual entry.
 *
 * @property int    $id
 * @property int    $hits
 * @property int    $item_id
 * @property Carbon $extracted_at
 *
 * @property Item   $item
 */
class Hits extends Model
{
    public $timestamps = false;

    protected $guarded = [''];

    protected $casts = [
        'extracted_at' => 'date',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class)
            ->withoutGlobalScope(PublishedScope::class);
    }
}
