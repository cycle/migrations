<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Migrations;

use Spiral\Database\DatabaseInterface;
use Spiral\Database\Schema\AbstractTable;
use Spiral\Database\TableInterface;
use Spiral\Migrations\Exception\ContextException;

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
     * @return TableInterface
     */
    public function getTable(string $table): TableInterface;

    /**
     * Get schema associated with given database and table.
     *
     * @param string $table
     * @return AbstractTable
     *
     * @throws ContextException
     */
    public function getSchema(string $table): AbstractTable;

    /**
     * Execute given set of operations.
     *
     * @param OperationInterface[] $operations
     */
    public function execute(array $operations);
}
