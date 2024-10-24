<?php

declare(strict_types=1);

namespace Cycle\Migrations\Operation\Table;

use Cycle\Database\Driver\HandlerInterface;
use Cycle\Migrations\CapsuleInterface;
use Cycle\Migrations\Exception\Operation\TableException;
use Cycle\Migrations\Operation\AbstractOperation;

final class Rename extends AbstractOperation
{
    public function __construct(string $table, private string $newName)
    {
        parent::__construct($table);
    }

    public function execute(CapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());
        $database = $this->database ?? '[default]';

        if (!$schema->exists()) {
            throw new TableException(
                "Unable to rename table '{$database}'.'{$this->getTable()}', table does not exists",
            );
        }

        if ($capsule->getDatabase()->hasTable($this->newName)) {
            throw new TableException(
                "Unable to rename table '{$database}'.'{$this->getTable()}', table '{$this->newName}' already exists",
            );
        }

        $schema->setName($this->newName);
        $schema->save(HandlerInterface::DO_ALL);
    }
}
