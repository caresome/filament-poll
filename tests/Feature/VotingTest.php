<?php

use Caresome\FilamentPoll\Events\PollVoted;
use Caresome\FilamentPoll\Models\Poll;
use Caresome\FilamentPoll\Models\PollOption;
use Caresome\FilamentPoll\Models\PollVote;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->poll = Poll::factory()
        ->singleChoice()
        ->active()
        ->open()
        ->create();

    $this->option1 = PollOption::factory()->forPoll($this->poll)->create(['text' => 'Option 1']);
    $this->option2 = PollOption::factory()->forPoll($this->poll)->create(['text' => 'Option 2']);
});

it('can cast a single choice vote as authenticated user', function () {
    $vote = PollVote::factory()
        ->forOption($this->option1)
        ->authenticated(1)
        ->create();

    expect($vote)->toBeInstanceOf(PollVote::class)
        ->and($vote->poll_id)->toBe($this->poll->id)
        ->and($vote->poll_option_id)->toBe($this->option1->id)
        ->and($vote->user_id)->toBe(1);
});

it('can cast a vote as guest', function () {
    $vote = PollVote::factory()
        ->forOption($this->option1)
        ->guest()
        ->create();

    expect($vote)->toBeInstanceOf(PollVote::class)
        ->and($vote->user_id)->toBeNull()
        ->and($vote->ip_address)->not->toBeNull()
        ->and($vote->session_id)->not->toBeNull();
});

it('dispatches PollVoted event when vote is cast', function () {
    Event::fake([PollVoted::class]);

    PollVote::factory()
        ->forOption($this->option1)
        ->authenticated(1)
        ->create();

    Event::assertDispatched(PollVoted::class, function ($event) {
        return $event->vote->poll_id === $this->poll->id
            && $event->poll->id === $this->poll->id;
    });
});

it('prevents duplicate votes from authenticated user for same option', function () {
    PollVote::factory()
        ->forOption($this->option1)
        ->authenticated(1)
        ->create();

    expect(fn () => PollVote::factory()
        ->forOption($this->option1)
        ->authenticated(1)
        ->create()
    )->toThrow(UniqueConstraintViolationException::class);
});

it('prevents duplicate votes from guest with same session and IP for same option', function () {
    $sessionId = 'test-session-123';
    $ipAddress = '192.168.1.1';

    PollVote::factory()
        ->forOption($this->option1)
        ->state([
            'session_id' => $sessionId,
            'ip_address' => $ipAddress,
            'user_id' => null,
        ])
        ->create();

    expect(fn () => PollVote::factory()
        ->forOption($this->option1)
        ->state([
            'session_id' => $sessionId,
            'ip_address' => $ipAddress,
            'user_id' => null,
        ])
        ->create()
    )->toThrow(UniqueConstraintViolationException::class);
});

it('allows guest vote from different session', function () {
    PollVote::factory()
        ->forOption($this->option1)
        ->state([
            'session_id' => 'session-1',
            'ip_address' => '192.168.1.1',
        ])
        ->create();

    $vote2 = PollVote::factory()
        ->forOption($this->option1)
        ->state([
            'session_id' => 'session-2',
            'ip_address' => '192.168.1.1',
        ])
        ->create();

    expect($vote2)->toBeInstanceOf(PollVote::class);
});

it('allows guest vote from different IP', function () {
    PollVote::factory()
        ->forOption($this->option1)
        ->state([
            'session_id' => 'session-1',
            'ip_address' => '192.168.1.1',
        ])
        ->create();

    $vote2 = PollVote::factory()
        ->forOption($this->option1)
        ->state([
            'session_id' => 'session-1',
            'ip_address' => '192.168.1.2',
        ])
        ->create();

    expect($vote2)->toBeInstanceOf(PollVote::class);
});

it('increments vote count when vote is cast', function () {
    expect($this->option1->votes_count)->toBe(0);

    PollVote::factory()
        ->forOption($this->option1)
        ->authenticated(1)
        ->create();

    expect($this->option1->fresh()->votes_count)->toBe(1);
});

it('decrements vote count when vote is deleted', function () {
    $vote = PollVote::factory()
        ->forOption($this->option1)
        ->authenticated(1)
        ->create();

    expect($this->option1->fresh()->votes_count)->toBe(1);

    $vote->delete();

    expect($this->option1->fresh()->votes_count)->toBe(0);
});

it('can cast multiple votes on multiple choice poll', function () {
    $multiPoll = Poll::factory()
        ->multipleChoice()
        ->active()
        ->open()
        ->create();

    $opt1 = PollOption::factory()->forPoll($multiPoll)->create();
    $opt2 = PollOption::factory()->forPoll($multiPoll)->create();

    PollVote::factory()->forOption($opt1)->authenticated(1)->create();
    PollVote::factory()->forOption($opt2)->authenticated(1)->create();

    expect($multiPoll->votes()->where('user_id', 1)->count())->toBe(2);
});

it('checks if user has voted', function () {
    expect($this->poll->hasUserVoted(1, null, null))->toBeFalse();

    PollVote::factory()
        ->forOption($this->option1)
        ->authenticated(1)
        ->create();

    expect($this->poll->hasUserVoted(1, null, null))->toBeTrue();
});

it('checks if guest has voted', function () {
    $sessionId = 'test-session';
    $ipAddress = '192.168.1.1';

    expect($this->poll->hasUserVoted(null, $ipAddress, $sessionId))->toBeFalse();

    PollVote::factory()
        ->forOption($this->option1)
        ->state(['session_id' => $sessionId, 'ip_address' => $ipAddress, 'user_id' => null])
        ->create();

    expect($this->poll->hasUserVoted(null, $ipAddress, $sessionId))->toBeTrue();
});

it('correctly identifies closed poll by date', function () {
    $closedPoll = Poll::factory()->closed()->create();

    expect($closedPoll->isClosed())->toBeTrue();
});

it('correctly identifies closed poll by is_active', function () {
    $inactivePoll = Poll::factory()->inactive()->create();

    expect($inactivePoll->isClosed())->toBeTrue();
});

it('correctly identifies open poll', function () {
    $openPoll = Poll::factory()->open()->active()->create();

    expect($openPoll->isClosed())->toBeFalse();
});

it('calculates total votes correctly', function () {
    expect($this->poll->total_votes)->toBe(0);

    PollVote::factory()->forOption($this->option1)->authenticated(1)->create();
    PollVote::factory()->forOption($this->option2)->authenticated(2)->create();

    expect($this->poll->fresh()->total_votes)->toBe(2);
});

it('calculates option percentage correctly', function () {
    PollVote::factory()->forOption($this->option1)->authenticated(1)->create();
    PollVote::factory()->forOption($this->option1)->authenticated(2)->create();
    PollVote::factory()->forOption($this->option2)->authenticated(3)->create();

    $this->option1->refresh();
    $this->option2->refresh();
    $this->poll->refresh();

    expect($this->option1->percentage)->toBe(66.67)
        ->and($this->option2->percentage)->toBe(33.33);
});
