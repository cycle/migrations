<?php

declare(strict_types=1);

namespace Cycle\Migrations\Operation\Table;

use Cycle\Migrations\CapsuleInterface;
use Cycle\Migrations\Exception\Operation\TableException;
use Cycle\Migrations\Operation\AbstractOperation;

final class PrimaryKeys extends AbstractOperation
{
    public function __construct(string $table, private array $columns)
    {
        parent::__construct($table);
    }

    public function execute(CapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());
        $database = $this->database ?? '[default]';

        if ($schema->exists()) {
            throw new TableException(
                "Unable to set primary keys for table '{$database}'.'{$this->getTable()}', table already exists",
            );
        }

        $schema->setPrimaryKeys($this->columns);
    }
}
