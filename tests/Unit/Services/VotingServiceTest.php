<?php

use Caresome\FilamentPoll\Models\Poll;
use Caresome\FilamentPoll\Models\PollOption;
use Caresome\FilamentPoll\Models\PollVote;
use Caresome\FilamentPoll\Services\VotingService;
use Caresome\FilamentPoll\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new VotingService;
    $this->poll = Poll::factory()->active()->open()->create();
    $this->option = PollOption::factory()->forPoll($this->poll)->create();
});

it('returns false for unauthenticated check', function () {
    expect($this->service->isAuthenticated())->toBeFalse();
});

it('returns null for unauthenticated user id', function () {
    expect($this->service->getAuthUserId())->toBeNull();
});

it('checks if user has voted by user id', function () {
    expect($this->service->hasUserVoted($this->poll, 1, null, null))->toBeFalse();

    PollVote::factory()
        ->forOption($this->option)
        ->authenticated(1)
        ->create();

    expect($this->service->hasUserVoted($this->poll, 1, null, null))->toBeTrue();
});

it('checks if guest has voted by session and ip', function () {
    $sessionId = 'test-session';
    $ipAddress = '192.168.1.1';

    expect($this->service->hasUserVoted($this->poll, null, $ipAddress, $sessionId))->toBeFalse();

    PollVote::factory()
        ->forOption($this->option)
        ->state(['session_id' => $sessionId, 'ip_address' => $ipAddress, 'user_id' => null])
        ->create();

    expect($this->service->hasUserVoted($this->poll, null, $ipAddress, $sessionId))->toBeTrue();
});

it('allows authenticated user to vote', function () {
    expect($this->service->canUserVote($this->poll, true, false))->toBeTrue();
});

it('prevents unauthenticated user from voting when guest voting disabled', function () {
    expect($this->service->canUserVote($this->poll, false, false))->toBeFalse();
});

it('allows unauthenticated user to vote when guest voting enabled', function () {
    expect($this->service->canUserVote($this->poll, false, true))->toBeTrue();
});

it('prevents voting on closed poll', function () {
    $closedPoll = Poll::factory()->closed()->create();

    expect($this->service->canUserVote($closedPoll, true, true))->toBeFalse();
});

it('prevents voting on inactive poll', function () {
    $inactivePoll = Poll::factory()->inactive()->create();

    expect($this->service->canUserVote($inactivePoll, true, true))->toBeFalse();
});

it('allows voting on active and open poll', function () {
    $activePoll = Poll::factory()->active()->open()->create();

    expect($this->service->canUserVote($activePoll, true, true))->toBeTrue();
});
