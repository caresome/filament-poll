<?php

namespace Caresome\FilamentPoll\Models;

use Caresome\FilamentPoll\Database\Factories\PollOptionFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $poll_id
 * @property string $text
 * @property int $votes_count
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read float $percentage
 * @property-read Poll|null $poll
 * @property-read Collection<int, PollVote> $votes
 */
class PollOption extends Model
{
    use HasFactory;

    public function getTable()
    {
        return config('filament-poll.table_names.poll_options', 'poll_options');
    }

    protected $fillable = [
        'poll_id',
        'text',
        'votes_count',
        'order',
    ];

    protected $casts = [
        'votes_count' => 'integer',
        'order' => 'integer',
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    public function getPercentageAttribute(): float
    {
        if (! $this->poll) {
            return 0;
        }

        $totalVotes = $this->poll->total_votes;

        if ($totalVotes === 0) {
            return 0;
        }

        return round(($this->votes_count / $totalVotes) * 100, 2);
    }

    public function incrementVotes(): void
    {
        $this->increment('votes_count');
    }

    public function decrementVotes(): void
    {
        $this->decrement('votes_count');
    }

    protected static function newFactory()
    {
        return PollOptionFactory::new();
    }
}
