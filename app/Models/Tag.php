<?php

namespace App\Models;

use App\Models\Scopes\PublishedScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Tag.
 *
 * Tags provide a way to group items with similar content together to be able to find related items.
 * For example: 'Scout Scarf Day', 'Sinterklaas' and 'JOTA JOTI'.
 *
 * @property int               $id
 * @property boolean           $is_published
 * @property string            $name // Unique
 * @property string|null       $description
 * @property int               $use_count
 *
 * @property Carbon|null       $created_at
 * @property Carbon|null       $updated_at
 * @property Carbon|null       $deleted_at
 * @property User|null         $created_by
 * @property User|null         $updated_by
 *
 * @property User|null         $createdBy
 * @property User|null         $updatedBy
 * @property Collection|Item[] $items
 */
#[ScopedBy([PublishedScope::class])]
class Tag extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class);
    }
}
