<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations;

use Cycle\Migrations\Migration\State as BaseState;
use Cycle\Migrations\Migration\Status;

/**
 * @psalm-import-type StatusEnum from Status
 *
 * @deprecated This class has been deprecated and moved into "Migration"
 *             namespace. Please use {@see BaseState} instead.
 */
final class State extends BaseState
{
    /**
     * @deprecated Please use {@see Status::STATUS_UNDEFINED} instead.
     * @var StatusEnum
     */
    public const STATUS_UNDEFINED = Status::STATUS_UNDEFINED;

    /**
     * @deprecated Please use {@see Status::STATUS_PENDING} instead.
     * @var StatusEnum
     */
    public const STATUS_PENDING = Status::STATUS_PENDING;

    /**
     * @deprecated Please use {@see Status::STATUS_EXECUTED} instead.
     * @var StatusEnum
     */
    public const STATUS_EXECUTED  = Status::STATUS_EXECUTED;
}
