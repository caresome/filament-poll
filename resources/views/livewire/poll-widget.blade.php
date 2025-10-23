<div class="fi-poll-widget" wire:poll.5s>
    <x-filament::section compact>
        <x-slot name="heading">
            {{ $poll->title }}
        </x-slot>

        @if ($poll->description)
            <x-slot name="description">
                {{ $poll->description }}
            </x-slot>
        @endif

        <x-slot name="headerEnd">
            @if ($poll->isClosed())
                <x-filament::badge color="danger">
                    {{ __('filament-poll::filament-poll.badges.closed') }}
                </x-filament::badge>
            @elseif($poll->closes_at && $poll->closes_at->isFuture())
                <x-filament::badge color="warning">
                    {{ __('filament-poll::filament-poll.badges.closes') }} {{ $poll->closes_at->diffForHumans() }}
                </x-filament::badge>
            @endif
        </x-slot>

        <div class="space-y-4">
            @error('poll')
                <div class="fi-fo-field-wrp-error-message fi-poll-error">
                    <div class="fi-poll-error-container">
                        <x-filament::icon icon="heroicon-o-exclamation-circle" class="fi-poll-error-icon" />
                        <span>{{ $message }}</span>
                    </div>
                </div>
            @enderror

            @if ($showResults)
                <div class="fi-poll-results">
                    @php
                        $maxVotes = $poll->options->max('votes_count') ?? 0;
                        $totalVotes = $poll->total_votes ?: 1;
                    @endphp

                    @foreach ($poll->options as $option)
                        @php
                            $percentage =
                                $poll->total_votes > 0 ? round(($option->votes_count / $totalVotes) * 100) : 0;
                            $isWinning = $maxVotes > 0 && $option->votes_count == $maxVotes;
                            $userVotedThis = false;

                            if ($hasVoted) {
                                $userVotes = $poll
                                    ->votes()
                                    ->where(function ($q) {
                                        $q->where('user_id', Auth::id())
                                            ->orWhere('session_id', session()->getId())
                                            ->orWhere('ip_address', request()->ip());
                                    })
                                    ->pluck('poll_option_id')
                                    ->toArray();
                                $userVotedThis = in_array($option->id, $userVotes);
                            }
                        @endphp

                        <div class="fi-poll-option">
                            <div class="fi-poll-option-container">
                                <div class="fi-poll-option-progress {{ $isWinning ? 'winning' : 'regular' }}"
                                    style="width: {{ $percentage }}%;">
                                </div>

                                <div class="fi-poll-option-content">
                                    <div class="fi-poll-option-label">
                                        @if ($isWinning && $option->votes_count > 0)
                                            <x-filament::icon icon="heroicon-o-check-circle" class="fi-poll-option-icon" />
                                        @endif

                                        <span class="fi-poll-option-text">
                                            {{ $option->text }}
                                        </span>

                                        @if ($userVotedThis)
                                            <x-filament::badge color="success" size="xs">
                                                {{ __('filament-poll::filament-poll.badges.your_vote') }}
                                            </x-filament::badge>
                                        @endif
                                    </div>

                                    <div class="fi-poll-option-stats">
                                        <span class="fi-poll-option-votes">
                                            {{ number_format($option->votes_count) }}
                                        </span>
                                        <span class="fi-poll-option-percentage {{ $isWinning ? 'winning' : '' }}">
                                            {{ $percentage }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="fi-poll-footer">
                    <div class="fi-poll-total-votes">
                        <x-filament::icon icon="heroicon-o-users" class="fi-poll-total-votes-icon" />
                        <span>{{ number_format($poll->total_votes) }} {{ __('filament-poll::filament-poll.total_text') }}
                            {{ trans_choice('filament-poll::filament-poll.vote_count', $poll->total_votes, ['count' => '']) }}</span>
                    </div>

                    @if (!$hasVoted && !$poll->isClosed())
                        <x-filament::button wire:click="$set('showResults', false)" color="gray" size="sm"
                            outlined>
                            {{ __('filament-poll::filament-poll.actions.back_to_voting') }}
                        </x-filament::button>
                    @endif
                </div>
            @else
                @if ($this->requiresLogin())
                    <div class="fi-poll-login-required">
                        <x-filament::badge icon="heroicon-o-lock-closed" color="warning" size="lg"
                            class="w-full justify-center p-3">
                            {{ __('filament-poll::filament-poll.badges.login_required') }}
                        </x-filament::badge>

                        <div class="fi-poll-options-disabled">
                            @foreach ($poll->options as $option)
                                <div class="fi-poll-option-disabled">
                                    @if ($poll->multiple_choice)
                                        <x-filament::input.checkbox disabled :value="$option->id" />
                                    @else
                                        <x-filament::input.radio disabled :value="$option->id" />
                                    @endif

                                    <span class="fi-poll-option-disabled-text">
                                        {{ $option->text }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        @if ($poll->show_results_before_voting)
                            <x-filament::button type="button" wire:click="showResultsOnly" color="gray" outlined>
                                <x-filament::icon icon="heroicon-o-chart-bar" class="h-4 w-4 mr-1" />
                                {{ __('filament-poll::filament-poll.actions.view_results') }}
                            </x-filament::button>
                        @endif
                    </div>
                @else
                    <form wire:submit="vote" class="fi-poll-voting-form">
                        <div class="fi-poll-options-list">
                            @foreach ($poll->options as $option)
                                @php
                                    $isSelected =
                                        (is_array($selectedOptions) && in_array($option->id, $selectedOptions)) ||
                                        $selectedOptions == $option->id;
                                @endphp

                                <label class="fi-poll-option-choice {{ $isSelected ? 'selected' : '' }}">
                                    @if ($poll->multiple_choice)
                                        <x-filament::input.checkbox wire:model.live="selectedOptions"
                                            :value="$option->id" />
                                    @else
                                        <x-filament::input.radio wire:model.live="selectedOptions" :value="$option->id" />
                                    @endif

                                    <span class="fi-poll-option-choice-text">
                                        {{ $option->text }}
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        @if ($poll->multiple_choice)
                            <div class="fi-poll-info">
                                <x-filament::icon icon="heroicon-o-information-circle" class="fi-poll-info-icon" />
                                <span>{{ __('filament-poll::filament-poll.messages.info.multiple_selection') }}</span>
                            </div>
                        @endif

                        <div class="fi-poll-actions">
                            @if (!$poll->isClosed())
                                <x-filament::button type="submit" :disabled="empty($selectedOptions) ||
                                    (is_array($selectedOptions) && count($selectedOptions) == 0)">
                                    <x-filament::icon icon="heroicon-o-check" class="h-4 w-4 mr-1" />
                                    {{ __('filament-poll::filament-poll.actions.submit_vote') }}
                                </x-filament::button>
                            @endif

                            @if ($poll->show_results_before_voting)
                                <x-filament::button type="button" wire:click="showResultsOnly" color="gray" outlined>
                                    <x-filament::icon icon="heroicon-o-chart-bar" class="h-4 w-4 mr-1" />
                                    {{ __('filament-poll::filament-poll.actions.view_results') }}
                                </x-filament::button>
                            @endif
                        </div>
                    </form>
                @endif
            @endif
        </div>

        @if ($poll->multiple_choice || $poll->allow_guest_voting || $poll->closes_at || $poll->show_results_before_voting)
            <x-slot name="footerActions">
                <div class="flex flex-wrap items-center gap-2">
                    @if ($poll->allow_guest_voting)
                        <x-filament::badge color="info" icon="heroicon-o-users">
                            {{ __('filament-poll::filament-poll.badges.guest_voting') }}
                        </x-filament::badge>
                    @endif

                    @if ($poll->multiple_choice)
                        <x-filament::badge color="warning" icon="heroicon-o-list-bullet">
                            {{ __('filament-poll::filament-poll.badges.multiple_choice') }}
                        </x-filament::badge>
                    @endif

                    @if ($poll->show_results_before_voting)
                        <x-filament::badge color="gray" icon="heroicon-o-eye">
                            {{ __('filament-poll::filament-poll.badges.results_visible') }}
                        </x-filament::badge>
                    @endif

                    @if ($poll->closes_at)
                        <div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                            <x-filament::icon icon="heroicon-o-clock" class="h-3 w-3" />
                            <span>
                                @if ($poll->closes_at->isPast())
                                    {{ __('filament-poll::filament-poll.badges.ended') }} {{ $poll->closes_at->diffForHumans() }}
                                @else
                                    {{ __('filament-poll::filament-poll.badges.ends') }} {{ $poll->closes_at->diffForHumans() }}
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
            </x-slot name="footerActions">
        @endif
    </x-filament::section>
</div>
