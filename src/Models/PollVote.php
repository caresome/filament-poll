<?php

namespace Caresome\FilamentPoll\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $poll_id
 * @property int $poll_option_id
 * @property int|null $user_id
 * @property string|null $ip_address
 * @property string|null $session_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Poll $poll
 * @property-read PollOption $option
 * @property-read Model|null $user
 */
class PollVote extends Model
{
    public function getTable()
    {
        return config('filament-poll.table_names.poll_votes', 'poll_votes');
    }

    protected $fillable = [
        'poll_id',
        'poll_option_id',
        'user_id',
        'ip_address',
        'session_id',
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(PollOption::class, 'poll_option_id');
    }

    public function user(): BelongsTo
    {
        $model = \Caresome\FilamentPoll\PollPlugin::get()->getUserModelClass();

        return $this->belongsTo($model);
    }

    protected static function booted(): void
    {
        static::created(function (PollVote $vote) {
            $vote->option->incrementVotes();
        });

        static::deleted(function (PollVote $vote) {
            $vote->option->decrementVotes();
        });
    }
}
