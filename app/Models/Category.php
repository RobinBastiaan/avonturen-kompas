<?php

namespace App\Models;

use App\Models\Scopes\PublishedScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Category.
 *
 * Categories are a way to indicate the nature of an item. They are nested in a category group and have an image.
 * For example: 'Welpen' as a 'Leeftijdsgroep', and 'Buitenleven' as an 'Activiteitengebied'.
 *
 * @property int               $id
 * @property boolean           $is_published
 * @property string            $name // Unique per category group
 * @property string|null       $description
 * @property int               $category_group_id
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
 * @property CategoryGroup     $categoryGroup
 * @property Collection|Item[] $items
 */
#[ScopedBy([PublishedScope::class])]
class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'category_group_id',
    ];

    protected $hidden = ['pivot'];

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

    public function categoryGroup(): BelongsTo
    {
        return $this->belongsTo(CategoryGroup::class, 'category_group_id');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class);
    }

    public function teams(): MorphToMany
    {
        return $this->morphToMany(Team::class, 'teamable');
    }
}
