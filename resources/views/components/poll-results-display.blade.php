@props([
    'poll',
    'hasVoted' => false,
    'authGuard' => null,
    'showVoteCount' => true,
])

@php
    $maxVotes = $poll->options->max('votes_count') ?? 0;
    $totalVotes = $poll->total_votes ?: 1;
    $userVotes = [];

    if ($hasVoted && $authGuard !== null) {
        $userVotes = $poll
            ->votes()
            ->where(function ($q) use ($authGuard) {
                $userId = auth($authGuard)->id();
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('session_id', session()->getId())
                        ->where('ip_address', request()->ip());
                }
            })
            ->pluck('poll_option_id')
            ->toArray();
    }
@endphp

<div class="fi-poll-results" role="region" aria-label="{{ __('filament-poll::general.poll_results') }}">
    @foreach ($poll->options as $option)
        @php
            $percentage = $poll->total_votes > 0 ? round(($option->votes_count / $totalVotes) * 100) : 0;
            $isWinning = $maxVotes > 0 && $option->votes_count == $maxVotes;
            $userVotedThis = $hasVoted && in_array($option->id, $userVotes);
        @endphp

        <div class="fi-poll-option" role="group" aria-label="{{ $option->text }} - {{ $percentage }}%">
            <div class="fi-poll-option-container">
                <div class="fi-poll-option-progress {{ $isWinning ? 'winning' : 'regular' }}"
                    style="width: {{ $percentage }}%;"
                    role="progressbar"
                    aria-valuenow="{{ $percentage }}"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    aria-label="{{ $option->text }}: {{ $percentage }}% {{ $showVoteCount ? '(' . number_format($option->votes_count) . ' ' . trans_choice('filament-poll::general.vote_count', $option->votes_count) . ')' : '' }}">
                </div>

                <div class="fi-poll-option-content">
                    <div class="fi-poll-option-label">
                        @if ($isWinning && $option->votes_count > 0)
                            <x-filament::icon icon="heroicon-o-check-circle" class="fi-poll-option-icon"
                                aria-label="{{ __('filament-poll::general.winning_option') }}" />
                        @endif

                        <span class="fi-poll-option-text">
                            {{ $option->text }}
                        </span>

                        @if ($userVotedThis)
                            <x-filament::badge color="success" size="xs" role="status">
                                {{ __('filament-poll::badges.your_vote') }}
                            </x-filament::badge>
                        @endif
                    </div>

                    <div class="fi-poll-option-stats">
                        @if ($showVoteCount)
                            <span class="fi-poll-option-votes">
                                {{ number_format($option->votes_count) }}
                            </span>
                        @endif
                        <span class="fi-poll-option-percentage {{ $isWinning ? 'winning' : '' }}">
                            {{ $percentage }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="fi-poll-footer">
        @if ($showVoteCount)
            <div class="fi-poll-total-votes" role="status" aria-live="polite">
                <x-filament::icon icon="heroicon-o-users" class="fi-poll-total-votes-icon" aria-hidden="true" />
                <span>{{ number_format($poll->total_votes) }} {{ __('filament-poll::general.total_text') }}
                    {{ trans_choice('filament-poll::general.vote_count', $poll->total_votes) }}</span>
            </div>
        @endif
    </div>
</div>
