<?php

namespace Caresome\FilamentPoll\Services;

use Caresome\FilamentPoll\Models\Poll;
use Caresome\FilamentPoll\PollPlugin;
use Throwable;

class VotingService
{
    protected function getAuthGuard(): ?string
    {
        try {
            return PollPlugin::get()->resolveAuthGuard();
        } catch (Throwable $e) {
            return null;
        }
    }

    public function getAuthUserId(): ?int
    {
        return auth($this->getAuthGuard())->id();
    }

    public function isAuthenticated(): bool
    {
        return auth($this->getAuthGuard())->check();
    }

    public function hasUserVoted(Poll $poll, ?int $userId = null, ?string $ipAddress = null, ?string $sessionId = null): bool
    {
        return $poll->votes()
            ->where(function ($query) use ($userId, $ipAddress, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId)
                        ->where('ip_address', $ipAddress);
                }
            })
            ->exists();
    }

    public function canUserVote(Poll $poll, bool $isAuthenticated, bool $allowGuestVoting): bool
    {
        if ($poll->isClosed()) {
            return false;
        }

        if (! $poll->is_active) {
            return false;
        }

        if ($isAuthenticated) {
            return true;
        }

        return $allowGuestVoting;
    }
}
