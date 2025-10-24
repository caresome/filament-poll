<?php

use Caresome\FilamentPoll\Events\PollClosed;
use Caresome\FilamentPoll\Events\PollCreated;
use Caresome\FilamentPoll\Models\Poll;
use Caresome\FilamentPoll\Models\PollOption;
use Caresome\FilamentPoll\Models\PollVote;
use Caresome\FilamentPoll\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(TestCase::class, RefreshDatabase::class);

it('dispatches PollCreated event when created', function () {
    Event::fake([PollCreated::class]);

    Poll::factory()->create();

    Event::assertDispatched(PollCreated::class);
});

it('dispatches PollClosed event when poll becomes closed', function () {
    $eventDispatched = false;
    Event::listen(PollClosed::class, function ($event) use (&$eventDispatched) {
        $eventDispatched = true;
    });

    $poll = Poll::factory()->active()->neverCloses()->create();
    expect($poll->isClosed())->toBeFalse();

    $poll->wasRecentlyCreated = false;

    $poll->update(['is_active' => false]);
    expect($poll->isClosed())->toBeTrue();

    expect($eventDispatched)->toBeTrue();
});

it('does not dispatch PollClosed event if already closed', function () {
    Event::fake([PollCreated::class, PollClosed::class]);

    $poll = Poll::factory()->inactive()->create();

    Event::assertDispatched(PollCreated::class);
    Event::assertNotDispatched(PollClosed::class);

    $poll->update(['title' => 'Updated Title']);

    Event::assertNotDispatched(PollClosed::class);
});

it('identifies closed poll by is_active flag', function () {
    $poll = Poll::factory()->inactive()->create();

    expect($poll->isClosed())->toBeTrue();
});

it('identifies closed poll by closes_at date', function () {
    $poll = Poll::factory()->closed()->create();

    expect($poll->isClosed())->toBeTrue();
});

it('identifies open poll', function () {
    $poll = Poll::factory()->active()->open()->create();

    expect($poll->isClosed())->toBeFalse();
});

it('identifies poll that never closes as open', function () {
    $poll = Poll::factory()->active()->neverCloses()->create();

    expect($poll->isClosed())->toBeFalse();
});

it('calculates total votes from relationship', function () {
    $poll = Poll::factory()->create();
    $option = PollOption::factory()->forPoll($poll)->create();

    expect($poll->total_votes)->toBe(0);

    PollVote::factory()->forOption($option)->authenticated(1)->create();
    PollVote::factory()->forOption($option)->authenticated(2)->create();

    expect($poll->fresh()->total_votes)->toBe(2);
});

it('uses votes_count attribute when available', function () {
    $poll = Poll::factory()->create();
    $poll->loadCount('votes');

    expect($poll->total_votes)->toBe(0);
});

it('has options relationship', function () {
    $poll = Poll::factory()->create();
    $option1 = PollOption::factory()->forPoll($poll)->create(['order' => 1]);
    $option2 = PollOption::factory()->forPoll($poll)->create(['order' => 0]);

    $poll->load('options');

    expect($poll->options)->toHaveCount(2)
        ->and($poll->options->first()->id)->toBe($option2->id);
});

it('has votes relationship', function () {
    $poll = Poll::factory()->create();
    $option = PollOption::factory()->forPoll($poll)->create();
    $vote = PollVote::factory()->forOption($option)->authenticated(1)->create();

    $poll->load('votes');

    expect($poll->votes)->toHaveCount(1)
        ->and($poll->votes->first()->id)->toBe($vote->id);
});

it('checks if user has voted', function () {
    $poll = Poll::factory()->create();
    $option = PollOption::factory()->forPoll($poll)->create();

    expect($poll->hasUserVoted(1, null, null))->toBeFalse();

    PollVote::factory()->forOption($option)->authenticated(1)->create();

    expect($poll->hasUserVoted(1, null, null))->toBeTrue();
});

it('uses custom table name from config', function () {
    config(['filament-poll.table_names.polls' => 'custom_polls']);

    $poll = new Poll;

    expect($poll->getTable())->toBe('custom_polls');
});
