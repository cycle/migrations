<?php

declare(strict_types=1);

namespace Cycle\Migrations\Operation\ForeignKey;

use Cycle\Migrations\CapsuleInterface;
use Cycle\Migrations\Exception\Operation\ForeignKeyException;

final class Drop extends ForeignKey
{
    public function execute(CapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());

        if (!$schema->hasForeignKey($this->columns)) {
            throw new ForeignKeyException(
                "Unable to drop foreign key '{$schema->getName()}'.'{$this->columnNames()}', "
                . 'foreign key does not exists',
            );
        }

        $schema->dropForeignKey($this->columns);
    }
}
