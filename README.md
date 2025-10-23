# Filament Poll

A FilamentPHP plugin for creating and managing polls with blade components and RichEditor integration.

## Features

- Create and manage polls through Filament admin panel
- Single or multiple choice polls
- Guest voting support
- Real-time vote counting
- Display results with percentages and progress bars
- Poll closing dates
- Livewire-powered interactive voting
- Blade component for easy frontend integration

## Installation

Install the package via composer:

```bash
composer require caresome/filament-poll
```

Publish and run migrations:

```bash
php artisan vendor:publish --tag="filament-poll-migrations"
php artisan migrate
```

Optionally publish the config file:

```bash
php artisan vendor:publish --tag="filament-poll-config"
```

Optionally publish the views:

```bash
php artisan vendor:publish --tag="filament-poll-views"
```

## Usage

### Register the Plugin

Add the plugin to your Filament panel provider:

```php
use Caresome\FilamentPoll\PollPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            PollPlugin::make(),
        ]);
}
```

### Creating Polls

Navigate to the Polls resource in your Filament admin panel to create and manage polls.

### Displaying Polls

#### Using Blade Component (Recommended)

```blade
<x-caresome::filament-poll :poll-id="1" />
```

Or pass the poll model:

```blade
<x-caresome::filament-poll :poll="$poll" />
```

#### Using Livewire Component Directly

```blade
@livewire('caresome::filament-poll', ['poll' => $poll])
```

## Configuration

The config file allows you to customize:

- Model classes
- Table names
- Default settings for guest voting, multiple choice, and result visibility
- Navigation group and icon

## License

MIT. See [LICENSE.md](LICENSE.md) for details.