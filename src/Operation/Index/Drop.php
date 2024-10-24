<?php

declare(strict_types=1);

namespace Cycle\Migrations\Operation\Index;

use Cycle\Migrations\CapsuleInterface;
use Cycle\Migrations\Exception\Operation\IndexException;

final class Drop extends Index
{
    public function execute(CapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());

        if (!$schema->hasIndex($this->columns)) {
            $columns = \implode(',', $this->columns);
            throw new IndexException(
                "Unable to drop index '{$schema->getName()}'.({$columns}), index does not exists",
            );
        }

        $schema->dropIndex($this->columns);
    }
}
