<?php

declare(strict_types=1);

namespace Cycle\Migrations;

/**
 * Migration meta information specific to current environment
 */
final class State
{
    // Migration status
    public const STATUS_UNDEFINED = -1;
    public const STATUS_PENDING = 0;
    public const STATUS_EXECUTED = 1;

    public function __construct(
        private string $name,
        private \DateTimeInterface $timeCreated,
        private int $status = self::STATUS_UNDEFINED,
        private ?\DateTimeInterface $timeExecuted = null,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getTimeCreated(): \DateTimeInterface
    {
        return $this->timeCreated;
    }

    public function getTimeExecuted(): ?\DateTimeInterface
    {
        return $this->timeExecuted;
    }

    public function withStatus(int $status, ?\DateTimeInterface $timeExecuted = null): self
    {
        $state = clone $this;
        $state->status = $status;
        $state->timeExecuted = $timeExecuted;

        return $state;
    }
}
