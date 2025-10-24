<?php

namespace Caresome\FilamentPoll\Events;

use Caresome\FilamentPoll\Models\Poll;
use Caresome\FilamentPoll\Models\PollVote;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PollVoted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public PollVote $vote,
        public Poll $poll
    ) {}
}
