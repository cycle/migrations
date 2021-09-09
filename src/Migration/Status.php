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
 * @psalm-type StatusEnum = Status::STATUS_*
 */
final class Status
{
    /**
     * This value means that the migration was loaded, but its status was
     * not determined.
     *
     * @var StatusEnum
     */
    public const STATUS_UNDEFINED = -1;

    /**
     * This value means that the migration is not present in the database
     * and is awaiting execution.
     *
     * @var StatusEnum
     */
    public const STATUS_PENDING = 0;

    /**
     * This value indicates that the migration is present in the database and
     * has already been performed.
     *
     * @var StatusEnum
     */
    public const STATUS_EXECUTED = 1;
}
