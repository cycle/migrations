<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations;

use Cycle\Migrations\Exception\OperationException;

/**
 * Represents simple table operation. Operation is a bridge between command and declarative
 * migrations.
 */
interface OperationInterface
{
    /**
     * Table operation related to.
     *
     * @return string
     */
    public function getTable(): string;

    /**
     * Execute operation in a given capsule.
     *
     * @param CapsuleInterface $capsule
     *
     * @throws OperationException
     */
    public function execute(CapsuleInterface $capsule);
}
