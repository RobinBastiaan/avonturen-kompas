<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AtScout.
 *
 * When, which Items were published in what @-scout magazine edition.
 *
 * @property int       $id
 * @property Carbon    $published_at // A biweekly unique date.
 * @property string    $name
 * @property int|null  $bevers_item_id
 * @property int|null  $welpen_item_id
 * @property int|null  $scouts_item_id
 * @property int|null  $explorers_item_id
 * @property int|null  $roverscouts_item_id
 * @property int|null  $extra_item_id
 *
 * @property Item|null $beversItem
 * @property Item|null $welpenItem
 * @property Item|null $scoutsItem
 * @property Item|null $explorersItem
 * @property Item|null $roverscoutsItem
 * @property Item|null $extraItem // Sometimes a general or additional item is added.
 */
class AtScout extends Model
{
    public $timestamps = false;

    protected $guarded = [''];

    protected $casts = [
        'published_at' => 'date',
    ];

    public function beversItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'bevers_item_id');
    }

    public function welpenItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'welpen_item_id');
    }

    public function scoutsItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'scouts_item_id');
    }

    public function explorersItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'explorers_item_id');
    }

    public function roverscoutsItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'roverscouts_item_id');
    }

    public function extraItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'extra_item_id');
    }
}
