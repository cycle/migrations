<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations;

use Cycle\Database\DatabaseInterface;
use Cycle\Database\Schema\AbstractTable;
use Cycle\Database\TableInterface;
use Cycle\Migrations\Exception\ContextException;
use Spiral\Migrations\CapsuleInterface as SpiralCapsuleInterface;

/**
 * Migration capsule (isolation).
 */
interface CapsuleInterface
{
    /**
     * @return DatabaseInterface
     */
    public function getDatabase(): DatabaseInterface;

    /**
     * @param string $table
     *
     * @return TableInterface
     */
    public function getTable(string $table): TableInterface;

    /**
     * Get schema associated with given database and table.
     *
     * @param string $table
     *
     * @throws ContextException
     *
     * @return AbstractTable
     */
    public function getSchema(string $table): AbstractTable;

    /**
     * Execute given set of operations.
     *
     * @param OperationInterface[] $operations
     */
    public function execute(array $operations);
}
\class_alias(CapsuleInterface::class, SpiralCapsuleInterface::class, false);
