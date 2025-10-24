<?php

use Caresome\FilamentPoll\Models\Poll;
use Caresome\FilamentPoll\Models\PollOption;
use Caresome\FilamentPoll\Models\PollVote;
use Caresome\FilamentPoll\PollPlugin;
use Caresome\FilamentPoll\Services\VotingService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->poll = Poll::factory()
        ->active()
        ->allowGuestVoting()
        ->create();

    $this->option = PollOption::factory()->forPoll($this->poll)->create();
});

it('resolves auth guard when explicitly set', function () {
    $plugin = PollPlugin::make()->authGuard('admin');

    expect($plugin->resolveAuthGuard())->toBe('admin');
});

it('resolves to null when no guard is set and no filament context', function () {
    $plugin = PollPlugin::make();

    expect($plugin->resolveAuthGuard())->toBeNull();
});

it('does not auto-detect when useDefaultAuthGuard is called', function () {
    $plugin = PollPlugin::make()->useDefaultAuthGuard();

    expect($plugin->shouldAutoDetectFilamentAuth())->toBeFalse()
        ->and($plugin->resolveAuthGuard())->toBeNull();
});

it('gets user model class from guard configuration', function () {
    config([
        'auth.guards.web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'auth.providers.users' => [
            'driver' => 'eloquent',
            'model' => User::class,
        ],
    ]);

    $plugin = PollPlugin::make();
    $userModel = $plugin->getUserModelClass();

    expect($userModel)->toBe(User::class);
});

it('voting service checks authentication with configured guard', function () {
    $service = new VotingService;

    expect($service->isAuthenticated())->toBeFalse()
        ->and($service->getAuthUserId())->toBeNull();
});

it('can vote when authenticated and guest voting disabled', function () {
    $service = new VotingService;
    $poll = Poll::factory()->requireAuth()->active()->open()->create();

    expect($service->canUserVote($poll, true, false))->toBeTrue()
        ->and($service->canUserVote($poll, false, false))->toBeFalse();
});

it('can vote as guest when guest voting enabled', function () {
    $service = new VotingService;
    $poll = Poll::factory()->allowGuestVoting()->active()->open()->create();

    expect($service->canUserVote($poll, false, true))->toBeTrue();
});

it('cannot vote on closed poll', function () {
    $service = new VotingService;
    $poll = Poll::factory()->closed()->allowGuestVoting()->create();

    expect($service->canUserVote($poll, true, true))->toBeFalse();
});

it('cannot vote on inactive poll', function () {
    $service = new VotingService;
    $poll = Poll::factory()->inactive()->allowGuestVoting()->create();

    expect($service->canUserVote($poll, true, true))->toBeFalse();
});

it('allows different users to vote on same poll', function () {
    $option = PollOption::factory()->forPoll($this->poll)->create();

    $vote1 = PollVote::factory()
        ->forOption($option)
        ->authenticated(1)
        ->create();

    $vote2 = PollVote::factory()
        ->forOption($option)
        ->authenticated(2)
        ->create();

    expect($vote1->user_id)->toBe(1)
        ->and($vote2->user_id)->toBe(2)
        ->and($this->poll->votes()->count())->toBe(2);
});
