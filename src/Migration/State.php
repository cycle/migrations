<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Migration;

/**
 * Migration meta information specific to current environment.
 *
 * @psalm-import-type StatusEnum from Status
 *
 * @internal State is an internal library class, please do not use it in your code.
 * @psalm-internal Cycle\Migrations
 */
class State
{
    /**
     * @var non-empty-string
     */
    private $name;

    /**
     * @var StatusEnum
     */
    private $status;

    /**
     * @var \DateTimeInterface
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface|null
     */
    private $executedAt;

    /**
     * @param non-empty-string $name
     * @param \DateTimeInterface $createdAt
     * @param StatusEnum $status
     * @param \DateTimeInterface|null $executedAt
     */
    public function __construct(
        string $name,
        \DateTimeInterface $createdAt,
        int $status = Status::STATUS_UNDEFINED,
        \DateTimeInterface $executedAt = null
    ) {
        $this->name = $name;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->executedAt = $executedAt;
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
        return $this->createdAt;
    }

    /**
     * Get migration execution time if any.
     *
     * @return \DateTimeInterface|null
     */
    public function getTimeExecuted(): ?\DateTimeInterface
    {
        return $this->executedAt;
    }

    /**
     * @param StatusEnum $status
     * @param \DateTimeInterface|null $executedAt
     * @return State
     */
    public function withStatus(int $status, \DateTimeInterface $executedAt = null): State
    {
        $state = clone $this;
        $state->status = $status;
        $state->executedAt = $executedAt;

        return $state;
    }
}
