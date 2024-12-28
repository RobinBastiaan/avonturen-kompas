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
 * Class CategoryGroup.
 *
 * Category groups are a way to group categories together.
 * For example: 'Welpen' as a 'Leeftijdsgroep', and 'Buitenleven' as an 'Activiteitengebied'.
 *
 * @property int                   $id
 * @property boolean               $is_published
 * @property string                $name // Unique
 * @property string|null           $description
 * @property boolean               $is_available_for_activities
 * @property boolean               $is_available_for_camps
 * @property int                   $use_count // Sum of related categories use_count.
 *
 * @property Carbon|null           $created_at
 * @property Carbon|null           $updated_at
 * @property Carbon|null           $deleted_at
 * @property User|null             $created_by
 * @property User|null             $updated_by
 *
 * @property User|null             $createdBy
 * @property User|null             $updatedBy
 * @property Collection|Category[] $categories
 */
#[ScopedBy([PublishedScope::class])]
class CategoryGroup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_available_for_activities',
        'is_available_for_camps',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_available_for_activities' => 'boolean',
        'is_available_for_camps' => 'boolean',
    ];


    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }
}
