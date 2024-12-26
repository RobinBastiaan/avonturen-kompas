<?php

namespace App\Models;

use App\Models\Scopes\PublishedScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Comment.
 *
 * As expected, comments allow users to give a text to an item.
 *
 * @property int                  $id
 * @property boolean              $is_published
 * @property int                  $item_id
 * @property int|null             $comment_id
 * @property string               $text
 *
 * @property Carbon|null          $created_at
 * @property Carbon|null          $updated_at
 * @property Carbon|null          $deleted_at
 * @property User|null            $created_by
 * @property User|null            $updated_by
 *
 * @property User|null            $createdBy
 * @property User|null            $updatedBy
 * @property Item                 $item
 * @property Comment|null         $repliedTo
 * @property Collection|Comment[] $replies
 */
#[ScopedBy([PublishedScope::class])]
class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_id',
        'comment_id',
        'text',
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

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function repliedTo(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
