<?php

namespace Caresome\FilamentPoll\Livewire;

use Caresome\FilamentPoll\Models\Poll;
use Caresome\FilamentPoll\Models\PollVote;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PollWidget extends Component
{
    public Poll $poll;

    public $selectedOptions = [];

    public bool $hasVoted = false;

    public bool $showResults = false;

    public function mount(Poll $poll): void
    {
        $this->poll = $poll->load(['options', 'votes']);
        $this->checkIfUserHasVoted();
        $this->showResults = $this->hasVoted || $this->poll->show_results_before_voting;
    }

    public function canVote(): bool
    {
        if (Auth::check()) {
            return true;
        }

        return $this->poll->allow_guest_voting;
    }

    public function requiresLogin(): bool
    {
        return ! Auth::check() && ! $this->poll->allow_guest_voting;
    }

    public function vote(): void
    {
        if (! $this->canVote()) {
            $this->addError('poll', __('filament-poll::filament-poll.messages.errors.login_required'));

            return;
        }

        if ($this->poll->isClosed()) {
            $this->addError('poll', __('filament-poll::filament-poll.messages.errors.poll_closed'));

            return;
        }

        if ($this->hasVoted) {
            $this->addError('poll', __('filament-poll::filament-poll.messages.errors.already_voted'));

            return;
        }

        $options = is_array($this->selectedOptions) ? $this->selectedOptions : [$this->selectedOptions];
        $options = array_filter($options);

        if (empty($options)) {
            $this->addError('poll', __('filament-poll::filament-poll.messages.errors.select_at_least_one'));

            return;
        }

        if (! $this->poll->multiple_choice && count($options) > 1) {
            $this->addError('poll', __('filament-poll::filament-poll.messages.errors.select_only_one'));

            return;
        }

        foreach ($options as $optionId) {
            PollVote::create([
                'poll_id' => $this->poll->id,
                'poll_option_id' => $optionId,
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
                'session_id' => session()->getId(),
            ]);
        }

        $this->hasVoted = true;
        $this->showResults = true;
        $this->poll->refresh();

        $this->dispatch('poll-voted', pollId: $this->poll->id);
    }

    public function showResultsOnly(): void
    {
        $this->showResults = true;
    }

    protected function checkIfUserHasVoted(): void
    {
        $this->hasVoted = $this->poll->hasUserVoted(
            Auth::id(),
            request()->ip(),
            session()->getId()
        );
    }

    public function render()
    {
        return view('filament-poll::livewire.poll-widget');
    }
}
