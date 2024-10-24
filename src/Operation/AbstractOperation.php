<?php

declare(strict_types=1);

namespace Cycle\Migrations\Operation;

use Cycle\Migrations\OperationInterface;

abstract class AbstractOperation implements OperationInterface
{
    public function __construct(protected string $table) {}

    public function getTable(): string
    {
        return $this->table;
    }
}
