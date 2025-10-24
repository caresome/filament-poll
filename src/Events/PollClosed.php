<?php

namespace Caresome\FilamentPoll\Events;

use Caresome\FilamentPoll\Models\Poll;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PollClosed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Poll $poll
    ) {}
}
