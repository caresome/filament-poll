<?php

namespace Caresome\FilamentPoll\Database\Factories;

use Caresome\FilamentPoll\Models\Poll;
use Caresome\FilamentPoll\Models\PollOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class PollOptionFactory extends Factory
{
    protected $model = PollOption::class;

    public function definition(): array
    {
        return [
            'poll_id' => Poll::factory(),
            'text' => fake()->sentence(3),
            'votes_count' => 0,
            'order' => 0,
        ];
    }

    public function forPoll(Poll $poll): static
    {
        return $this->state(fn (array $attributes) => [
            'poll_id' => $poll->id,
        ]);
    }

    public function withVotes(int $count): static
    {
        return $this->state(fn (array $attributes) => [
            'votes_count' => $count,
        ]);
    }

    public function ordered(int $order): static
    {
        return $this->state(fn (array $attributes) => [
            'order' => $order,
        ]);
    }
}
