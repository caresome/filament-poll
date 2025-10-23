<?php

namespace Caresome\FilamentPoll;

use Caresome\FilamentPoll\Livewire\PollWidget;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PollServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-poll';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews('filament-poll')
            ->hasTranslations()
            ->hasMigrations([
                'create_polls_table',
                'create_poll_options_table',
                'create_poll_votes_table',
            ]);
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Css::make('filament-poll', __DIR__.'/../resources/css/filament-poll.css'),
        ], package: 'caresome/filament-poll');

        Livewire::component('caresome::filament-poll', PollWidget::class);

        Blade::componentNamespace('Caresome\\FilamentPoll\\Components', 'caresome');
    }
}
