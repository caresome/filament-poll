<?php

namespace Caresome\FilamentPoll;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PollServiceProvider extends PackageServiceProvider
{
    public static string $name = 'poll';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile();
    }
}
