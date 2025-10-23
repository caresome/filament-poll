<?php

namespace Caresome\FilamentPoll;

use Caresome\FilamentPoll\Resources\PollResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class PollPlugin implements Plugin
{
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
}
