<?php

namespace Caresome\FilamentPoll\Database\Factories;

use Caresome\FilamentPoll\Models\Poll;
use Caresome\FilamentPoll\PollPlugin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Throwable;

class PollFactory extends Factory
{
    protected $model = Poll::class;

    public function definition(): array
    {
        try {
            $plugin = PollPlugin::get();
            $multipleChoice = $plugin->getMultipleChoiceByDefault();
            $isActive = $plugin->getIsActiveByDefault();
            $showResultsBeforeVoting = $plugin->getShowResultsBeforeVotingByDefault();
            $allowGuestVoting = $plugin->getAllowGuestVotingByDefault();
            $showVoteCount = $plugin->getShowVoteCountByDefault();
        } catch (Throwable $e) {
            $multipleChoice = false;
            $isActive = true;
            $showResultsBeforeVoting = false;
            $allowGuestVoting = false;
            $showVoteCount = true;
        }

        return [
            'title' => fake()->sentence(),
            'description' => fake()->optional()->paragraph(),
            'multiple_choice' => $multipleChoice,
            'is_active' => $isActive,
            'show_results_before_voting' => $showResultsBeforeVoting,
            'allow_guest_voting' => $allowGuestVoting,
            'show_vote_count' => $showVoteCount,
            'closes_at' => fake()->optional(0.5)->dateTimeBetween('now', '+30 days'),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'closes_at' => fake()->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'closes_at' => fake()->dateTimeBetween('+1 day', '+30 days'),
        ]);
    }

    public function neverCloses(): static
    {
        return $this->state(fn (array $attributes) => [
            'closes_at' => null,
        ]);
    }

    public function multipleChoice(): static
    {
        return $this->state(fn (array $attributes) => [
            'multiple_choice' => true,
        ]);
    }

    public function singleChoice(): static
    {
        return $this->state(fn (array $attributes) => [
            'multiple_choice' => false,
        ]);
    }

    public function allowGuestVoting(): static
    {
        return $this->state(fn (array $attributes) => [
            'allow_guest_voting' => true,
        ]);
    }

    public function requireAuth(): static
    {
        return $this->state(fn (array $attributes) => [
            'allow_guest_voting' => false,
        ]);
    }

    public function showResultsBeforeVoting(): static
    {
        return $this->state(fn (array $attributes) => [
            'show_results_before_voting' => true,
        ]);
    }

    public function hideResultsBeforeVoting(): static
    {
        return $this->state(fn (array $attributes) => [
            'show_results_before_voting' => false,
        ]);
    }
}
