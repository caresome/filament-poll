# Filament Poll

A FilamentPHP plugin for creating and managing polls with Livewire-powered blade components.

![filament-poll-thumbnail](https://github.com/user-attachments/assets/e53cbcc9-b970-46fb-a039-9bde3e78b398)

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

Optionally publish the config file (for customizing table names):

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
            PollPlugin::make()
                ->navigationIcon('heroicon-o-chart-bar')
                ->navigationSort(10)
                ->isActiveByDefault(true)
                ->allowGuestVotingByDefault(false)
                ->multipleChoiceByDefault(false)
                ->showResultsBeforeVotingByDefault(false)
                ->showVoteCountByDefault(true),
        ]);
}
```

#### Available Configuration Methods

**Navigation & Display:**
- `navigationIcon(?string $icon)` - Set the navigation icon (default: `heroicon-o-chart-bar`)
- `navigationSort(?int $sort)` - Set the navigation sort order

**Default Poll Settings:**
- `isActiveByDefault(bool $isActive = true)` - Set default value for poll active status (default: `true`)
- `allowGuestVotingByDefault(bool $allow = true)` - Set default value for guest voting (default: `false`)
- `multipleChoiceByDefault(bool $multiple = true)` - Set default value for multiple choice polls (default: `false`)
- `showResultsBeforeVotingByDefault(bool $show = true)` - Set default value for showing results before voting (default: `false`)
- `showVoteCountByDefault(bool $show = true)` - Set default value for showing vote count (default: `true`)

**Validation & Limits:**
- `maxPollOptions(int $max)` - Maximum number of poll options allowed (default: `20`)
- `maxOptionTextLength(int $length)` - Maximum character length for option text (default: `255`)

**Real-time Updates:**
- `pollingInterval(?string $interval)` - How often to refresh poll results (default: `'5s'`). Set to `null` to disable live updates.

**Authentication Configuration:**
- `authGuard(?string $guard)` - Specify authentication guard to use (default: auto-detect or Laravel default)
- `useDefaultAuthGuard()` - Disable Filament auto-detection, always use Laravel's default guard

**Example with all options:**
```php
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
    ->authGuard('admin')  // Optional: specify auth guard
```

#### Authentication Behavior

**Automatic Detection** (Default):
- When used inside Filament panels, automatically detects and uses the panel's authentication guard
- Outside Filament context, uses Laravel's default guard
- Works seamlessly with multi-guard applications

**Manual Configuration:**
```php
// Explicitly set authentication guard
PollPlugin::make()
    ->authGuard('admin')

// Or use Laravel's default guard (disable auto-detection)
PollPlugin::make()
    ->useDefaultAuthGuard()
```

**Multi-Guard Support:**
The user model is automatically resolved from your guard configuration in `config/auth.php`:
```php
// config/auth.php
'guards' => [
    'admin' => ['provider' => 'admins'],
],
'providers' => [
    'admins' => ['model' => App\Models\Admin::class],
],
```

**Example Scenarios:**

```php
// Customer-facing Filament panel
PollPlugin::make()
    ->authGuard('customer')

// Admin Filament panel
PollPlugin::make()
    ->authGuard('admin')

// Auto-detect (no configuration needed)
PollPlugin::make()  // Uses panel's guard automatically
```

> **Note:** Navigation group is handled via translations in `resources/lang/en/navigation.php`

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

## Events

The package dispatches several events that you can listen to for custom functionality:

### Available Events

#### PollCreated
Dispatched when a new poll is created.

```php
use Caresome\FilamentPoll\Events\PollCreated;

class SendPollCreatedNotification
{
    public function handle(PollCreated $event): void
    {
        $poll = $event->poll;

        // Send notification to admins
        // Log the creation
        // Trigger webhooks
    }
}
```

#### PollVoted
Dispatched when a vote is cast (authenticated or guest).

```php
use Caresome\FilamentPoll\Events\PollVoted;

class TrackVoteAnalytics
{
    public function handle(PollVoted $event): void
    {
        $vote = $event->vote;
        $poll = $event->poll;

        // Track analytics
        // Send real-time updates
        // Notify poll creator
    }
}
```

#### PollClosed
Dispatched when a poll transitions from open to closed (either by `is_active` becoming false or `closes_at` passing).

```php
use Caresome\FilamentPoll\Events\PollClosed;

class HandlePollClosure
{
    public function handle(PollClosed $event): void
    {
        $poll = $event->poll;

        // Send results summary
        // Archive poll data
        // Notify stakeholders
    }
}
```

### Registering Event Listeners

In your `EventServiceProvider`:

```php
use Caresome\FilamentPoll\Events\PollCreated;
use Caresome\FilamentPoll\Events\PollVoted;
use Caresome\FilamentPoll\Events\PollClosed;

protected $listen = [
    PollCreated::class => [
        SendPollCreatedNotification::class,
    ],
    PollVoted::class => [
        TrackVoteAnalytics::class,
    ],
    PollClosed::class => [
        HandlePollClosure::class,
        SendResultsSummary::class,
    ],
];
```

Or use the `Event::listen()` method in a service provider:

```php
use Illuminate\Support\Facades\Event;
use Caresome\FilamentPoll\Events\PollVoted;

public function boot(): void
{
    Event::listen(PollVoted::class, function (PollVoted $event) {
        // Handle the vote
    });
}
```

## Advanced Configuration

### Plugin Configuration

Most configuration is done through the plugin's chainable methods (see above), following FilamentPHP v4 best practices.

### Config File

The config file (`config/filament-poll.php`) is only used for infrastructure-level settings:

- **Table Names**: Customize database table names if needed
  ```php
  'table_names' => [
      'polls' => 'polls',
      'poll_options' => 'poll_options',
      'poll_votes' => 'poll_votes',
  ],
  ```

After publishing the config file, you can customize the table names to match your database structure.

## Security Considerations

### Vote Tracking & Duplicate Prevention

The package uses different methods to track votes depending on authentication status:

**Authenticated Users:**
- Votes are tracked by `user_id`
- One vote per user per poll (enforced by database unique constraint)

**Guest Users (when enabled):**
- Votes are tracked by combination of `session_id` AND `ip_address`
- Both values must match to identify a returning voter
- Database unique constraint prevents duplicate votes

**Important Notes:**
- IP addresses can be spoofed but combined with session ID provides reasonable protection for guest voting
- For high-security scenarios, consider disabling guest voting with `allowGuestVotingByDefault(false)`
- Session-based tracking means guests can vote again if they clear cookies or use different browsers
- VPN/proxy users may share IP addresses, but unique session IDs prevent false duplicates

### Performance Optimizations

The package includes several performance optimizations:
- Eager loading of vote counts to prevent N+1 queries
- Database indexes on frequently queried fields
- Unique constraints that double as query indexes
- Cached vote counting when using `withCount()`

## Testing

The package includes comprehensive test coverage:

### Running Tests

```bash
composer test
```

### Test Coverage

```bash
composer test-coverage
```

### Available Factories

The package provides factories for easy testing:

```php
use Caresome\FilamentPoll\Models\Poll;
use Caresome\FilamentPoll\Models\PollOption;
use Caresome\FilamentPoll\Models\PollVote;

// Create a poll with various states
$poll = Poll::factory()->create();
$poll = Poll::factory()->active()->multipleChoice()->allowGuestVoting()->create();
$poll = Poll::factory()->closed()->create();

// Create poll options
$option = PollOption::factory()->forPoll($poll)->create();

// Create votes
$vote = PollVote::factory()->forOption($option)->authenticated(1)->create();
$vote = PollVote::factory()->forOption($option)->guest()->create();
```

Available poll factory states:
- `active()` / `inactive()`
- `closed()` / `open()` / `neverCloses()`
- `multipleChoice()` / `singleChoice()`
- `allowGuestVoting()` / `requireAuth()`
- `showResultsBeforeVoting()` / `hideResultsBeforeVoting()`

## Accessibility

The package follows WCAG 2.1 AA accessibility standards:

- **ARIA Labels**: All interactive elements have proper ARIA labels
- **Keyboard Navigation**: Full keyboard support for voting
- **Screen Readers**: Comprehensive screen reader announcements
- **Focus Management**: Visible focus indicators and logical tab order
- **Live Regions**: Dynamic updates announced to assistive technologies
- **Semantic HTML**: Proper use of semantic elements (fieldset, legend, etc.)
- **Progress Bars**: Accessible progress indicators with proper ARIA attributes

## License

MIT. See [LICENSE.md](LICENSE.md) for details.
