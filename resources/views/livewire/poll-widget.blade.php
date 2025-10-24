@use('Caresome\FilamentPoll\PollPlugin')

@php
    $authGuard = PollPlugin::get()->resolveAuthGuard();
@endphp

<div class="fi-poll-widget" @if ($pollingInterval = PollPlugin::get()->getPollingInterval()) wire:poll.{{ $pollingInterval }} @endif
    role="region" aria-labelledby="poll-title-{{ $poll->id }}">
    <x-filament::section compact>
        <x-slot name="heading">
            <span id="poll-title-{{ $poll->id }}">{{ $poll->title }}</span>
        </x-slot>

        @if ($poll->description)
            <x-slot name="description">
                {{ $poll->description }}
            </x-slot>
        @endif

        <x-slot name="headerEnd">
            @if ($poll->isClosed())
                <x-filament::badge color="danger">
                    {{ __('filament-poll::badges.closed') }}
                </x-filament::badge>
            @elseif($poll->closes_at?->isFuture())
                <x-filament::badge color="warning">
                    {{ __('filament-poll::badges.closes') }} {{ $poll->closes_at->diffForHumans() }}
                </x-filament::badge>
            @endif
        </x-slot>

        <div class="space-y-4">
            @error('poll')
                <div class="fi-fo-field-wrp-error-message fi-poll-error" role="alert" aria-live="polite" id="poll-error-{{ $poll->id }}">
                    <div class="fi-poll-error-container">
                        <x-filament::icon icon="heroicon-o-exclamation-circle" class="fi-poll-error-icon" aria-hidden="true" />
                        <span>{{ $message }}</span>
                    </div>
                </div>
            @enderror

            @if ($showResults)
                <x-filament-poll::poll-results-display
                    :poll="$poll"
                    :has-voted="$hasVoted"
                    :auth-guard="$authGuard"
                    :show-vote-count="$poll->show_vote_count"
                />

                @if (!$hasVoted && !$poll->isClosed())
                    <div class="fi-poll-footer mt-4">
                        <x-filament::button wire:click="$set('showResults', false)" color="gray" size="sm"
                            outlined>
                            {{ __('filament-poll::actions.back_to_voting') }}
                        </x-filament::button>
                    </div>
                @endif
            @else
                @if ($this->requiresLogin())
                    <div class="fi-poll-login-required" role="status" aria-live="polite">
                        <x-filament::badge icon="heroicon-o-lock-closed" color="warning" size="lg"
                            class="w-full justify-center p-3"
                            role="alert">
                            {{ __('filament-poll::badges.login_required') }}
                        </x-filament::badge>

                        <div class="fi-poll-options-disabled" aria-disabled="true">
                            @foreach ($poll->options as $option)
                                <div class="fi-poll-option-disabled">
                                    @if ($poll->multiple_choice)
                                        <x-filament::input.checkbox disabled :value="$option->id" aria-label="{{ $option->text }} ({{ __('filament-poll::badges.login_required') }})" />
                                    @else
                                        <x-filament::input.radio disabled :value="$option->id" aria-label="{{ $option->text }} ({{ __('filament-poll::badges.login_required') }})" />
                                    @endif

                                    <span class="fi-poll-option-disabled-text">
                                        {{ $option->text }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        @if ($poll->show_results_before_voting)
                            <x-filament::button icon="heroicon-o-chart-bar" size="sm" type="button" wire:click="showResultsOnly" color="gray" outlined>
                                {{ __('filament-poll::actions.view_results') }}
                            </x-filament::button>
                        @endif
                    </div>
                @else
                    <form wire:submit="vote" class="fi-poll-voting-form" role="form"
                        aria-labelledby="poll-title-{{ $poll->id }}"
                        @error('poll') aria-describedby="poll-error-{{ $poll->id }}" @enderror>
                        <fieldset class="fi-poll-options-list" role="group"
                            aria-label="{{ $poll->multiple_choice ? __('filament-poll::messages.info.multiple_selection') : __('filament-poll::forms.select_option') }}">
                            <legend class="sr-only">{{ __('filament-poll::forms.select_option') }}</legend>
                            @foreach ($poll->options as $option)
                                @php
                                    $isSelected =
                                        (is_array($selectedOptions) && in_array($option->id, $selectedOptions)) ||
                                        $selectedOptions == $option->id;
                                @endphp

                                <label class="fi-poll-option-choice {{ $isSelected ? 'selected' : '' }}" for="poll-option-{{ $option->id }}">
                                    @if ($poll->multiple_choice)
                                        <x-filament::input.checkbox
                                            wire:model.live="selectedOptions"
                                            :value="$option->id"
                                            id="poll-option-{{ $option->id }}"
                                            aria-label="{{ $option->text }}" />
                                    @else
                                        <x-filament::input.radio
                                            wire:model.live="selectedOptions"
                                            :value="$option->id"
                                            id="poll-option-{{ $option->id }}"
                                            name="poll-{{ $poll->id }}-option"
                                            aria-label="{{ $option->text }}" />
                                    @endif

                                    <span class="fi-poll-option-choice-text">
                                        {{ $option->text }}
                                    </span>
                                </label>
                            @endforeach
                        </fieldset>

                        @if ($poll->multiple_choice)
                            <div class="fi-poll-info" role="status" aria-live="polite">
                                <x-filament::icon icon="heroicon-o-information-circle" class="fi-poll-info-icon" aria-hidden="true" />
                                <span>{{ __('filament-poll::messages.info.multiple_selection') }}</span>
                            </div>
                        @endif

                        <div class="fi-poll-actions">
                            @if (!$poll->isClosed())
                                <x-filament::button
                                    icon="heroicon-o-check"
                                    size="sm"
                                    type="submit"
                                    :disabled="empty($selectedOptions) || (is_array($selectedOptions) && count($selectedOptions) == 0)"
                                    aria-label="{{ __('filament-poll::actions.submit_vote') }}">
                                    {{ __('filament-poll::actions.submit_vote') }}
                                </x-filament::button>
                            @endif

                            @if ($poll->show_results_before_voting)
                                <x-filament::button
                                    icon="heroicon-o-chart-bar"
                                    size="sm"
                                    type="button"
                                    wire:click="showResultsOnly"
                                    color="gray"
                                    outlined
                                    aria-label="{{ __('filament-poll::actions.view_results') }}">
                                    {{ __('filament-poll::actions.view_results') }}
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
                            {{ __('filament-poll::badges.guest_voting') }}
                        </x-filament::badge>
                    @endif

                    @if ($poll->multiple_choice)
                        <x-filament::badge color="warning" icon="heroicon-o-list-bullet">
                            {{ __('filament-poll::badges.multiple_choice') }}
                        </x-filament::badge>
                    @endif

                    @if ($poll->show_results_before_voting)
                        <x-filament::badge color="gray" icon="heroicon-o-eye">
                            {{ __('filament-poll::badges.results_visible') }}
                        </x-filament::badge>
                    @endif

                    @if ($poll->closes_at)
                        <div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                            <x-filament::icon icon="heroicon-o-clock" class="h-3 w-3" />
                            <span>
                                @if ($poll->closes_at?->isPast())
                                    {{ __('filament-poll::badges.ended') }} {{ $poll->closes_at?->diffForHumans() }}
                                @else
                                    {{ __('filament-poll::badges.ends') }} {{ $poll->closes_at?->diffForHumans() }}
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
            </x-slot name="footerActions">
        @endif
    </x-filament::section>
</div>
