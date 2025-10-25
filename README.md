# Filament Poll

A FilamentPHP v4 plugin for creating and managing interactive polls with Livewire-powered components.

![filament-poll-thumbnail](https://github.com/user-attachments/assets/e2ba0ff6-1ca9-4a4a-99e9-5835c066e1ce)


## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Quick Start](#quick-start)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Events](#events)
- [Testing](#testing)
- [Security](#security-considerations)
- [Accessibility](#accessibility)
- [License](#license)

## Features

- **Easy Management** - Create and manage polls through Filament admin panel
- **Flexible Voting** - Single or multiple choice polls
- **Guest Support** - Optional guest voting with session tracking
- **Real-time Updates** - Live vote counting with configurable polling intervals
- **Rich Display** - Results with percentages and progress bars
- **Scheduled Closing** - Set poll closing dates
- **Interactive UI** - Livewire-powered voting experience
- **Simple Integration** - Blade component for easy frontend use
- **Event System** - Listen to poll lifecycle events
- **Accessible** - WCAG 2.1 AA compliant

## Requirements

- PHP 8.1 or higher
- Laravel 10.x or 11.x
- FilamentPHP 4.x

## Quick Start

```bash
composer require caresome/filament-poll
php artisan vendor:publish --tag="filament-poll-migrations"
php artisan migrate
```

Register the plugin in your panel provider:

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

Display a poll on your frontend:

```blade
<x-caresome::filament-poll :poll-id="1" />
```

## Installation

### Step 1: Install Package

```bash
composer require caresome/filament-poll
```

### Step 2: Publish and Run Migrations

```bash
php artisan vendor:publish --tag="filament-poll-migrations"
php artisan migrate
```

### Step 3: Optional Publishing

Publish config file (for customizing table names):

```bash
php artisan vendor:publish --tag="filament-poll-config"
```

Publish views (for customization):

```bash
php artisan vendor:publish --tag="filament-poll-views"
```

## Configuration

### Basic Setup

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

### Configuration Options

The plugin provides chainable methods to customize behavior:

#### Navigation

```php
PollPlugin::make()
    ->navigationIcon('heroicon-o-chart-bar')
    ->navigationSort(10)
```

| Method | Description | Default |
|--------|-------------|---------|
| `navigationIcon()` | Set navigation icon | `heroicon-o-chart-bar` |
| `navigationSort()` | Set navigation sort order | - |

#### Poll Defaults

```php
PollPlugin::make()
    ->isActiveByDefault(true)
    ->allowGuestVotingByDefault(false)
    ->multipleChoiceByDefault(false)
    ->showResultsBeforeVotingByDefault(false)
    ->showVoteCountByDefault(true)
```

| Method | Description | Default |
|--------|-------------|---------|
| `isActiveByDefault()` | Polls are active by default | `true` |
| `allowGuestVotingByDefault()` | Allow guest voting | `false` |
| `multipleChoiceByDefault()` | Enable multiple choice | `false` |
| `showResultsBeforeVotingByDefault()` | Show results before voting | `false` |
| `showVoteCountByDefault()` | Display vote counts | `true` |

#### Limits & Performance

```php
PollPlugin::make()
    ->maxPollOptions(20)
    ->maxOptionTextLength(255)
    ->pollingInterval('5s')
```

| Method | Description | Default |
|--------|-------------|---------|
| `maxPollOptions()` | Maximum poll options | `20` |
| `maxOptionTextLength()` | Max characters per option | `255` |
| `pollingInterval()` | Live update interval (null to disable) | `5s` |

#### Authentication

```php
PollPlugin::make()
    ->authGuard('admin')
```

| Method | Description | Default |
|--------|-------------|---------|
| `authGuard()` | Set authentication guard | Auto-detect from panel |
| `useDefaultAuthGuard()` | Use Laravel's default guard | - |

**How it works:**
- Auto-detects the Filament panel's guard
- Falls back to Laravel's default guard
- Supports multi-guard applications

**Example:**

```php
PollPlugin::make()
    ->authGuard('admin')
```

### Complete Example

```php
use Caresome\FilamentPoll\PollPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            PollPlugin::make()
                ->navigationIcon('heroicon-o-chart-bar')
                ->navigationSort(10)
                ->isActiveByDefault(true)
                ->allowGuestVotingByDefault(false)
                ->multipleChoiceByDefault(false)
                ->showResultsBeforeVotingByDefault(false)
                ->showVoteCountByDefault(true)
                ->maxPollOptions(20)
                ->maxOptionTextLength(255)
                ->pollingInterval('5s')
                ->authGuard('admin'),
        ]);
}
```

### Custom Table Names

Publish the config file and modify table names:

```bash
php artisan vendor:publish --tag="filament-poll-config"
```

```php
return [
    'table_names' => [
        'polls' => 'custom_polls',
        'poll_options' => 'custom_poll_options',
        'poll_votes' => 'custom_poll_votes',
    ],
];
```

## Usage

### 1. Create Polls

Navigate to the **Polls** resource in your Filament admin panel:

1. Click "New Poll"
2. Enter poll question and options
3. Configure settings (guest voting, multiple choice, etc.)
4. Set closing date (optional)
5. Save and publish

### 2. Display Polls on Frontend

#### Using Blade Component (Recommended)

**By Poll ID:**
```blade
<x-caresome::filament-poll :poll-id="1" />
```

**By Poll Model:**
```blade
<x-caresome::filament-poll :poll="$poll" />
```

#### Using Livewire Component

```blade
@livewire('caresome::filament-poll', ['poll' => $poll])
```

### 3. Passing Polls to Views

**In your controller:**
```php
use Caresome\FilamentPoll\Models\Poll;

public function index()
{
    $poll = Poll::where('is_active', true)->first();

    return view('welcome', compact('poll'));
}
```

**In your view:**
```blade
@if($poll)
    <x-caresome::filament-poll :poll="$poll" />
@endif
```

## Events

The package dispatches lifecycle events for custom integrations.

### Available Events

| Event | When Fired | Properties |
|-------|------------|------------|
| `PollCreated` | New poll created | `$event->poll` |
| `PollVoted` | Vote is cast | `$event->poll`, `$event->vote` |
| `PollClosed` | Poll closes | `$event->poll` |

### Listening to Events

**In EventServiceProvider:**

```php
use Caresome\FilamentPoll\Events\{PollCreated, PollVoted, PollClosed};

protected $listen = [
    PollCreated::class => [
        SendPollNotification::class,
    ],
    PollVoted::class => [
        TrackAnalytics::class,
    ],
    PollClosed::class => [
        SendResultsSummary::class,
    ],
];
```

**Using Event::listen():**

```php
use Illuminate\Support\Facades\Event;
use Caresome\FilamentPoll\Events\PollVoted;

Event::listen(PollVoted::class, function (PollVoted $event) {
    $poll = $event->poll;
    $vote = $event->vote;

});
```

### Example Use Cases

- Send notifications when polls are created
- Track voting analytics
- Trigger webhooks on vote events
- Archive closed polls
- Send result summaries to stakeholders

## Security Considerations

### Overview

The package implements multiple security layers for vote tracking and prevention of abuse.

### Vote Tracking Methods

| User Type | Tracking Method | Duplicate Prevention |
|-----------|----------------|---------------------|
| **Authenticated** | User ID | Database unique constraint |
| **Guest** | Session ID + IP Address | Database unique constraint |

### Security Notes

**Authenticated Users:**
- ✅ One vote per user per poll
- ✅ Database-level enforcement
- ✅ High security

**Guest Voting:**
- ⚠️ Session + IP tracking (reasonable protection)
- ⚠️ Can vote again if cookies cleared
- ⚠️ VPN users share IPs (but unique sessions prevent conflicts)
- 🔒 Disable for high-security polls: `allowGuestVotingByDefault(false)`

### Performance

The package includes built-in optimizations:
- ✅ Eager loading prevents N+1 queries
- ✅ Database indexes on frequently queried fields
- ✅ Cached vote counting
- ✅ Efficient query constraints

## Testing

### Run Tests

```bash
composer test
composer test-coverage
```

### Using Factories

The package provides factories for testing:

```php
use Caresome\FilamentPoll\Models\{Poll, PollOption, PollVote};

$poll = Poll::factory()->active()->multipleChoice()->create();

$option = PollOption::factory()->forPoll($poll)->create();

$vote = PollVote::factory()->forOption($option)->authenticated(1)->create();
$vote = PollVote::factory()->forOption($option)->guest()->create();
```

### Available Factory States

| Model | States |
|-------|--------|
| **Poll** | `active()`, `inactive()`, `closed()`, `open()`, `neverCloses()`, `multipleChoice()`, `singleChoice()`, `allowGuestVoting()`, `requireAuth()` |
| **PollOption** | `forPoll($poll)` |
| **PollVote** | `forOption($option)`, `authenticated($userId)`, `guest()` |

## Accessibility

WCAG 2.1 AA compliant with full accessibility support:

| Feature | Implementation |
|---------|---------------|
| **ARIA Labels** | All interactive elements labeled |
| **Keyboard Navigation** | Full keyboard support |
| **Screen Readers** | Comprehensive announcements |
| **Focus Management** | Visible indicators, logical tab order |
| **Live Regions** | Real-time updates announced |
| **Semantic HTML** | Proper use of fieldset, legend, etc. |
| **Progress Bars** | Accessible with ARIA attributes |

## Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md) for details.
