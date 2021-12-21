<?php

declare(strict_types=1);

namespace Cycle\Migrations\Operation\Index;

use Cycle\Migrations\Operation\AbstractOperation;

abstract class Index extends AbstractOperation
{
    public function __construct(string $table, protected array $columns)
    {
        parent::__construct($table);
    }
}
