<?php

namespace App\Models;

use App\Models\Scopes\PublishedScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Category.
 *
 * Category are a way to indicate properties of an item. They can be nested and have an image.
 * For example: 'Welpen' as a 'Leeftijdsgroep', and 'Buitenleven' as an 'Activiteitengebied'.
 *
 * @property int                   $id
 * @property boolean               $is_published
 * @property string                $name // Unique
 * @property string|null           $description
 * @property int|null              $category_id
 * @property int                   $use_count
 *
 * @property Carbon|null           $created_at
 * @property Carbon|null           $updated_at
 * @property Carbon|null           $deleted_at
 * @property User|null             $created_by
 * @property User|null             $updated_by
 *
 * @property User|null             $createdBy
 * @property User|null             $updatedBy
 * @property Collection|Item[]     $items
 * @property Category|null         $parentCategory
 * @property Collection|Category[] $childCategories
 */
#[ScopedBy([PublishedScope::class])]
class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'category_id',
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

    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function childCategories(): HasMany
    {
        return $this->hasMany(Category::class);
    }
}
