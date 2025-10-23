<?php

namespace Caresome\FilamentPoll\Components;

use Caresome\FilamentPoll\Models\Poll as PollModel;
use Illuminate\View\Component;

class FilamentPoll extends Component
{
    public PollModel $poll;

    public function __construct($pollId = null, ?PollModel $poll = null)
    {
        if ($poll) {
            $this->poll = $poll;
        } elseif ($pollId) {
            $this->poll = PollModel::findOrFail($pollId);
        }
    }

    public function render()
    {
        return view('filament-poll::components.filament-poll');
    }
}
