<?php

namespace Caresome\FilamentPoll;

use Filament\Contracts\Plugin;
use Filament\Panel;

class PollPlugin implements Plugin
{
    public function getId(): string
    {
        return 'poll';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([])
            ->pages([])
            ->widgets([]);
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
