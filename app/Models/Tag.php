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
 * Class Tag.
 *
 * Tags provide a way to group items with similar content together to be able to find related items.
 * A tag should be a topic or activity type that we want to promote and that fits scouting, and should have items for every age group.
 * For example: 'Scout Scarf Day', 'Sinterklaas' and 'JOTA-JOTI'.
 *
 * @property int               $id
 * @property boolean           $is_published
 * @property string            $name // Unique
 * @property string|null       $description
 * @property Carbon|null       $special_interest_at
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
 *
 * @method withUpcomingSpecialInterest() Builder
 */
#[ScopedBy([PublishedScope::class])]
class Tag extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $hidden = ['pivot'];

    protected $casts = [
        'is_published'        => 'boolean',
        'special_interest_at' => 'date',
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

    public function teams(): MorphToMany
    {
        return $this->morphToMany(Team::class, 'teamable');
    }

    /**
     * Mark the tag as having special interest if the current date is within some time before the special interest.
     */
    public function hasSpecialInterest(): bool
    {
        if ($this->special_interest_at === null) {
            return false;
        }

        $today = Carbon::now()->startOfDay();
        $specialDay = Carbon::create($this->special_interest_at)->year((int) now()->format('Y'));
        if ($today->gt($specialDay)) {
            $specialDay->addYear();
        }

        return $today->between($specialDay, $specialDay->copy()->subWeeks(2));
    }

    /**
     * Scope to get tags with special interest dates some time from now.
     */
    public function scopeWithUpcomingSpecialInterest($query, int $monthsInAdvance)
    {
        $now = now()->startOfDay();
        $dateFromNow = now()->addMonths($monthsInAdvance)->startOfDay();

        return $query->whereNotNull('special_interest_at')
            ->where(function ($query) use ($now, $dateFromNow) {
                $query->whereRaw('DATE_FORMAT(special_interest_at, "%m-%d") BETWEEN ? AND ?', [
                    $now->format('m-d'),
                    $dateFromNow->format('m-d'),
                ]);
            });
    }
}
