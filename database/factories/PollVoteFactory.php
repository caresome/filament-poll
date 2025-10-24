<?php

namespace Caresome\FilamentPoll\Database\Factories;

use Caresome\FilamentPoll\Models\Poll;
use Caresome\FilamentPoll\Models\PollOption;
use Caresome\FilamentPoll\Models\PollVote;
use Illuminate\Database\Eloquent\Factories\Factory;

class PollVoteFactory extends Factory
{
    protected $model = PollVote::class;

    public function definition(): array
    {
        $poll = Poll::factory()->create();
        $option = PollOption::factory()->forPoll($poll)->create();

        return [
            'poll_id' => $poll->id,
            'poll_option_id' => $option->id,
            'user_id' => null,
            'ip_address' => fake()->ipv4(),
            'session_id' => fake()->uuid(),
        ];
    }

    public function forPoll(Poll $poll): static
    {
        return $this->state(fn (array $attributes) => [
            'poll_id' => $poll->id,
        ]);
    }

    public function forOption(PollOption $option): static
    {
        return $this->state(fn (array $attributes) => [
            'poll_option_id' => $option->id,
            'poll_id' => $option->poll_id,
        ]);
    }

    public function authenticated(int $userId): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
            'ip_address' => null,
            'session_id' => null,
        ]);
    }

    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'ip_address' => fake()->ipv4(),
            'session_id' => fake()->uuid(),
        ]);
    }
}
