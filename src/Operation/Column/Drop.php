<?php

declare(strict_types=1);

namespace Cycle\Migrations\Operation\Column;

use Cycle\Migrations\CapsuleInterface;
use Cycle\Migrations\Exception\Operation\ColumnException;
use Cycle\Migrations\Operation\AbstractOperation;

final class Drop extends AbstractOperation
{
    public function __construct(string $table, private string $name)
    {
        parent::__construct($table);
    }

    public function execute(CapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());

        if (!$schema->hasColumn($this->name)) {
            throw new ColumnException(
                "Unable to drop column '{$schema->getName()}'.'{$this->name}', column does not exists",
            );
        }

        //Declaring column
        $schema->dropColumn($this->name);
    }
}
