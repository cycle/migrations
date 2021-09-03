<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

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

    /** @var string */
    private $name;

    /** @var int */
    private $status;

    /** @var \DateTimeInterface */
    private $timeCreated;

    /** @var \DateTimeInterface|null */
    private $timeExecuted;

    /**
     * @param string             $name
     * @param \DateTimeInterface $timeCreated
     * @param int                $status
     * @param \DateTimeInterface $timeExecuted
     */
    public function __construct(
        string $name,
        \DateTimeInterface $timeCreated,
        int $status = self::STATUS_UNDEFINED,
        \DateTimeInterface $timeExecuted = null
    ) {
        $this->name = $name;
        $this->status = $status;
        $this->timeCreated = $timeCreated;
        $this->timeExecuted = $timeExecuted;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Migration status.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Get migration creation time.
     *
     * @return \DateTimeInterface
     */
    public function getTimeCreated(): \DateTimeInterface
    {
        return $this->timeCreated;
    }

    /**
     * Get migration execution time if any.
     *
     * @return \DateTimeInterface|null
     */
    public function getTimeExecuted(): ?\DateTimeInterface
    {
        return $this->timeExecuted;
    }

    /**
     * @param int                     $status
     * @param \DateTimeInterface|null $timeExecuted
     *
     * @return State
     */
    public function withStatus(int $status, \DateTimeInterface $timeExecuted = null): self
    {
        $state = clone $this;
        $state->status = $status;
        $state->timeExecuted = $timeExecuted;

        return $state;
    }
}
