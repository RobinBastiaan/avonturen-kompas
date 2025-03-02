<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Team.
 *
 * By using a Team a User can find Items via Categories or Tags that they have expertise over.
 *
 * @property int         $id
 * @property string      $name // Unique
 *
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property User|null   $created_by
 * @property User|null   $updated_by
 *
 * @property User|null   $createdBy
 * @property User|null   $updatedBy
 */
class Team extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * The users that belong to this team.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function tags(): MorphToMany
    {
        return $this->morphedByMany(Tag::class, 'teamable');
    }

    public function categories(): MorphToMany
    {
        return $this->morphedByMany(Category::class, 'teamable');
    }

    /**
     * Get all tags and categories associated with this team in a single collection.
     */
    public function teamables(): Collection
    {
        return $this->tags->merge($this->categories);
    }
}
