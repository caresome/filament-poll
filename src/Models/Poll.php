<?php

namespace Caresome\FilamentPoll\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property bool $multiple_choice
 * @property bool $is_active
 * @property bool $show_results_before_voting
 * @property bool $allow_guest_voting
 * @property bool $show_vote_count
 * @property Carbon|null $closes_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read int $total_votes
 * @property-read Collection<int, PollOption> $options
 * @property-read Collection<int, PollVote> $votes
 */
class Poll extends Model
{
    use SoftDeletes;

    public function getTable()
    {
        return config('filament-poll.table_names.polls', 'polls');
    }

    protected $fillable = [
        'title',
        'description',
        'multiple_choice',
        'is_active',
        'show_results_before_voting',
        'allow_guest_voting',
        'show_vote_count',
        'closes_at',
    ];

    protected $casts = [
        'multiple_choice' => 'boolean',
        'is_active' => 'boolean',
        'show_results_before_voting' => 'boolean',
        'allow_guest_voting' => 'boolean',
        'show_vote_count' => 'boolean',
        'closes_at' => 'datetime',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class)->orderBy('order');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    public function isClosed(): bool
    {
        if (! $this->is_active) {
            return true;
        }

        if ($this->closes_at && $this->closes_at->isPast()) {
            return true;
        }

        return false;
    }

    public function getTotalVotesAttribute(): int
    {
        if (isset($this->attributes['votes_count'])) {
            return (int) $this->attributes['votes_count'];
        }

        return $this->votes()->count();
    }

    public function hasUserVoted($userId = null, $ipAddress = null, $sessionId = null): bool
    {
        return app(\Caresome\FilamentPoll\Services\VotingService::class)
            ->hasUserVoted($this, $userId, $ipAddress, $sessionId);
    }
}
