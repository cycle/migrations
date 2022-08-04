<?php

namespace Cycle\Migrations\Operation\Table;

use Cycle\Migrations\CapsuleInterface;
use Cycle\Migrations\Exception\Operation\TableException;
use Cycle\Migrations\Operation\AbstractOperation;

final class Truncate extends AbstractOperation
{
    /**
     * {@inheritdoc}
     */
    public function execute(CapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());
        $database = $this->database ?? '[default]';

        if (!$schema->exists()) {
            throw new TableException(
                "Unable to truncate table '{$database}'.'{$this->getTable()}', table does not exists"
            );
        }

        $capsule->getDatabase()->execute(sprintf('TRUNCATE `%s`', $this->getTable()));
    }
}
