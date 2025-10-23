@php
    $poll = $getRecord();
    $maxVotes = $poll->options->max('votes_count') ?? 0;
    $totalVotes = $poll->total_votes ?: 1;
@endphp

<div class="fi-poll-results">
    @foreach ($poll->options as $option)
        @php
            $percentage = $poll->total_votes > 0 ? round(($option->votes_count / $totalVotes) * 100) : 0;
            $isWinning = $maxVotes > 0 && $option->votes_count == $maxVotes;
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

    <div class="fi-poll-footer">
        <div class="fi-poll-total-votes">
            <x-filament::icon icon="heroicon-o-users" class="fi-poll-total-votes-icon" />
            <span>{{ number_format($poll->total_votes) }} {{ __('filament-poll::filament-poll.total_text') }}
                {{ trans_choice('filament-poll::filament-poll.vote_count', $poll->total_votes, ['count' => '']) }}</span>
        </div>
    </div>
</div>
