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
 * Class Item.
 *
 * Items are either activities or camps and are central for all content.
 *
 * @property int                        $id
 * @property boolean                    $is_published
 * @property string                     $title
 * @property string                     $slug
 * @property string                     $hash
 * @property boolean                    $is_camp
 * @property int|null                   $camp_length
 * @property string                     $summary
 * @property string                     $description
 * @property string|null                $requirements
 * @property string|null                $tips
 * @property string|null                $safety
 * @property int                        $hits // Current hits. Use Hits relationship for historical context.
 *
 * @property Carbon|null                $created_at
 * @property Carbon|null                $updated_at
 * @property Carbon|null                $deleted_at
 * @property User|null                  $created_by
 * @property User|null                  $updated_by
 *
 * @property User|null                  $createdBy
 * @property User|null                  $updatedBy
 * @property Collection|Category[]      $categories
 * @property Collection|Tag[]           $tags
 * @property Collection|Comment[]       $comments
 * @property Collection|Item[]          $camps
 * @property Collection|Item[]          $activities
 * @property Collection|User[]          $favoritedBy
 * @property Collection|ExtractedItem[] $appliedFrom
 * @property Collection|Hits[]          $historicalHits
 */
#[ScopedBy([PublishedScope::class])]
class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'is_camp',
        'camp_length',
        'summary',
        'description',
        'requirements',
        'tips',
        'safety',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_camp'      => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(static function (Item $item) {
            // Generate a slug from the title by default, but allow a manual overwrite.
            if (empty($item->slug)) {
                $item->slug = str()->slug($item->title);
            }

            $item->hash = str()->random(8);
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function camps(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'camp_activities', 'activity_id', 'camp_id')
            ->withPivot('day_number', 'sort_order');
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'camp_activities', 'camp_id', 'activity_id')
            ->withPivot('day_number', 'sort_order');
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function appliedFrom(): HasMany
    {
        return $this->hasMany(ExtractedItem::class, 'applied_to');
    }

    public function historicalHits(): HasMany
    {
        return $this->hasMany(Hits::class);
    }
}
