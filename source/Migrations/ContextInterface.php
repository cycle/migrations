<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
namespace Spiral\Migrations;

use Spiral\Database\Entities\Database;
use Spiral\Database\Entities\Schemas\AbstractTable;
use Spiral\Database\Entities\Table;
use Spiral\Migrations\Exceptions\ContextException;

interface ContextInterface
{
    /**
     * @param string $database
     * @return Database
     */
    public function getDatabase($database);

    /**
     * @param string $database
     * @param string $table
     * @return Table
     */
    public function getTable($database, $table);

    /**
     * Get schema associated with given database and table.
     *
     * @param string|null $database
     * @param string      $table
     * @return AbstractTable
     * @throws ContextException
     */
    public function getSchema($database, $table);

    /**
     * Execute given set of operations.
     *
     * @param OperationInterface[] $operations
     */
    public function execute(array $operations);
}