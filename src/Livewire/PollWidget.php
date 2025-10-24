<?php

namespace Caresome\FilamentPoll\Livewire;

use Caresome\FilamentPoll\Models\Poll;
use Caresome\FilamentPoll\Models\PollVote;
use Caresome\FilamentPoll\PollPlugin;
use Caresome\FilamentPoll\Services\VotingService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PollWidget extends Component
{
    public Poll $poll;

    public $selectedOptions = [];

    public bool $hasVoted = false;

    public bool $showResults = false;

    protected function getAuthGuard(): ?string
    {
        return PollPlugin::get()->resolveAuthGuard();
    }

    protected function isAuthenticated(): bool
    {
        return auth($this->getAuthGuard())->check();
    }

    protected function getAuthUserId(): ?int
    {
        return auth($this->getAuthGuard())->id();
    }

    public function mount(Poll $poll): void
    {
        $this->poll = $poll->load(['options', 'votes']);
        $this->checkIfUserHasVoted();
        $this->showResults = $this->hasVoted || $this->poll->show_results_before_voting;
    }

    public function canVote(): bool
    {
        return app(VotingService::class)->canUserVote(
            $this->poll,
            $this->isAuthenticated(),
            $this->poll->allow_guest_voting
        );
    }

    public function requiresLogin(): bool
    {
        return ! $this->isAuthenticated() && ! $this->poll->allow_guest_voting;
    }

    public function vote(): void
    {
        if (! $this->canVote()) {
            $this->addError('poll', __('filament-poll::messages.errors.login_required'));

            return;
        }

        if ($this->poll->isClosed()) {
            $this->addError('poll', __('filament-poll::messages.errors.poll_closed'));

            return;
        }

        if ($this->hasVoted) {
            $this->addError('poll', __('filament-poll::messages.errors.already_voted'));

            return;
        }

        $options = is_array($this->selectedOptions) ? $this->selectedOptions : [$this->selectedOptions];
        $options = array_filter($options);

        if (empty($options)) {
            $this->addError('poll', __('filament-poll::messages.errors.select_at_least_one'));

            return;
        }

        if (! $this->poll->multiple_choice && count($options) > 1) {
            $this->addError('poll', __('filament-poll::messages.errors.select_only_one'));

            return;
        }

        try {
            DB::transaction(function () use ($options) {
                foreach ($options as $optionId) {
                    PollVote::create([
                        'poll_id' => $this->poll->id,
                        'poll_option_id' => $optionId,
                        'user_id' => $this->getAuthUserId(),
                        'ip_address' => request()->ip(),
                        'session_id' => session()->getId(),
                    ]);
                }
            });

            $this->hasVoted = true;
            $this->showResults = true;
            $this->poll->refresh();

            $this->dispatch('poll-voted', pollId: $this->poll->id);
        } catch (UniqueConstraintViolationException $e) {
            $this->addError('poll', __('filament-poll::messages.errors.already_voted'));

            return;
        }
    }

    public function showResultsOnly(): void
    {
        $this->showResults = true;
    }

    protected function checkIfUserHasVoted(): void
    {
        $this->hasVoted = $this->poll->hasUserVoted(
            $this->getAuthUserId(),
            request()->ip(),
            session()->getId()
        );
    }

    public function render()
    {
        return view('filament-poll::livewire.poll-widget');
    }
}
