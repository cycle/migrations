<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Migration;

use Spiral\Migrations\Migration\Status as SpiralStatus;

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
\class_alias(Status::class, SpiralStatus::class, false);
