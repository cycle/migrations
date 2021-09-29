<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations;

use Cycle\Database\Database;
use Cycle\Database\DatabaseInterface;
use Cycle\Database\DatabaseManager;
use Cycle\Database\Schema\AbstractTable;
use Cycle\Database\TableInterface;
use Cycle\Migrations\Exception\CapsuleException;

/**
 * Isolates set of table specific operations and schemas into one place. Kinda repository.
 */
final class Capsule implements CapsuleInterface
{
    /** @var DatabaseManager */
    private $database = null;

    /** @var array */
    private $schemas = [];

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * {@inheritdoc}
     */
    public function getDatabase(): DatabaseInterface
    {
        return $this->database;
    }

    /**
     * {@inheritdoc}
     */
    public function getTable(string $table): TableInterface
    {
        return $this->database->table($table);
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema(string $table): AbstractTable
    {
        if (!isset($this->schemas[$table])) {
            //We have to declare existed to prevent dropping existed schema
            $this->schemas[$table] = $this->database->table($table)->getSchema();
        }

        return $this->schemas[$table];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Throwable
     */
    public function execute(array $operations): void
    {
        foreach ($operations as $operation) {
            if (!$operation instanceof OperationInterface) {
                throw new CapsuleException(
                    sprintf(
                        'Migration operation expected to be an instance of `OperationInterface`, `%s` given',
                        get_class($operation)
                    )
                );
            }

            $operation->execute($this);
        }
    }
}
