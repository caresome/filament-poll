<?php

namespace Caresome\FilamentPoll;

use Caresome\FilamentPoll\Resources\PollResource;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;

class PollPlugin implements Plugin
{
    protected bool $isActiveByDefault = true;

    protected bool $allowGuestVotingByDefault = false;

    protected bool $multipleChoiceByDefault = false;

    protected bool $showResultsBeforeVotingByDefault = false;

    protected bool $showVoteCountByDefault = true;

    protected ?string $pollingInterval = '5s';

    protected ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected ?int $navigationSort = null;

    protected int $maxPollOptions = 20;

    protected int $maxOptionTextLength = 255;

    protected ?string $authGuard = null;

    protected bool $autoDetectFilamentAuth = true;

    public function getId(): string
    {
        return 'filament-poll';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            PollResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static */
        return filament(app(static::class)->getId());
    }

    public function isActiveByDefault(bool $isActive = true): static
    {
        $this->isActiveByDefault = $isActive;

        return $this;
    }

    public function allowGuestVotingByDefault(bool $allow = true): static
    {
        $this->allowGuestVotingByDefault = $allow;

        return $this;
    }

    public function multipleChoiceByDefault(bool $multiple = true): static
    {
        $this->multipleChoiceByDefault = $multiple;

        return $this;
    }

    public function showResultsBeforeVotingByDefault(bool $show = true): static
    {
        $this->showResultsBeforeVotingByDefault = $show;

        return $this;
    }

    public function showVoteCountByDefault(bool $show = true): static
    {
        $this->showVoteCountByDefault = $show;

        return $this;
    }

    public function pollingInterval(?string $interval): static
    {
        $this->pollingInterval = $interval;

        return $this;
    }

    public function navigationIcon(?string $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function navigationSort(?int $sort): static
    {
        $this->navigationSort = $sort;

        return $this;
    }

    public function getIsActiveByDefault(): bool
    {
        return $this->isActiveByDefault;
    }

    public function getAllowGuestVotingByDefault(): bool
    {
        return $this->allowGuestVotingByDefault;
    }

    public function getMultipleChoiceByDefault(): bool
    {
        return $this->multipleChoiceByDefault;
    }

    public function getShowResultsBeforeVotingByDefault(): bool
    {
        return $this->showResultsBeforeVotingByDefault;
    }

    public function getShowVoteCountByDefault(): bool
    {
        return $this->showVoteCountByDefault;
    }

    public function getPollingInterval(): ?string
    {
        return $this->pollingInterval;
    }

    public function getNavigationIcon(): ?string
    {
        return $this->navigationIcon;
    }

    public function getNavigationSort(): ?int
    {
        return $this->navigationSort;
    }

    public function maxPollOptions(int $max): static
    {
        $this->maxPollOptions = $max;

        return $this;
    }

    public function getMaxPollOptions(): int
    {
        return $this->maxPollOptions;
    }

    public function maxOptionTextLength(int $length): static
    {
        $this->maxOptionTextLength = $length;

        return $this;
    }

    public function getMaxOptionTextLength(): int
    {
        return $this->maxOptionTextLength;
    }

    public function authGuard(?string $guard): static
    {
        $this->authGuard = $guard;

        return $this;
    }

    public function getAuthGuard(): ?string
    {
        return $this->authGuard;
    }

    public function useDefaultAuthGuard(): static
    {
        $this->autoDetectFilamentAuth = false;

        return $this;
    }

    public function shouldAutoDetectFilamentAuth(): bool
    {
        return $this->autoDetectFilamentAuth;
    }

    public function resolveAuthGuard(): ?string
    {
        if ($this->authGuard) {
            return $this->authGuard;
        }

        if ($this->autoDetectFilamentAuth && class_exists(Filament::class)) {
            try {
                $panel = Filament::getCurrentPanel();
                if ($panel) {
                    return $panel->getAuthGuard();
                }
            } catch (\Throwable $e) {
            }
        }

        return null;
    }

    public function getUserModelClass(): string
    {
        $guard = $this->resolveAuthGuard();

        if ($guard) {
            $guardConfig = config("auth.guards.{$guard}");
        } else {
            $defaultGuard = config('auth.defaults.guard');
            $guardConfig = config("auth.guards.{$defaultGuard}");
        }

        $provider = $guardConfig['provider'] ?? 'users';

        return config("auth.providers.{$provider}.model");
    }
}
