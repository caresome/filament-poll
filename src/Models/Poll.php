<?php

namespace Caresome\FilamentPoll\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property bool $multiple_choice
 * @property bool $is_active
 * @property bool $show_results_before_voting
 * @property bool $allow_guest_voting
 * @property \Illuminate\Support\Carbon|null $closes_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read int $total_votes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PollOption> $options
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PollVote> $votes
 */
class Poll extends Model
{
    use SoftDeletes;

    public function getTable()
    {
        return config('poll.table_names.polls', 'polls');
    }

    protected $fillable = [
        'title',
        'description',
        'multiple_choice',
        'is_active',
        'show_results_before_voting',
        'allow_guest_voting',
        'closes_at',
    ];

    protected $casts = [
        'multiple_choice' => 'boolean',
        'is_active' => 'boolean',
        'show_results_before_voting' => 'boolean',
        'allow_guest_voting' => 'boolean',
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
        return $this->votes()->count();
    }

    public function hasUserVoted($userId = null, $ipAddress = null, $sessionId = null): bool
    {
        $query = $this->votes();

        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($ipAddress) {
            $query->where('ip_address', $ipAddress);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        }

        return $query->exists();
    }

    public function updateTotalVotes(): void
    {
        foreach ($this->options as $option) {
            $option->update([
                'votes_count' => $option->votes()->count(),
            ]);
        }
    }
}
