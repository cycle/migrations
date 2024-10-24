<?php

declare(strict_types=1);

namespace Cycle\Migrations\Operation\ForeignKey;

use Cycle\Migrations\Operation\AbstractOperation;

abstract class ForeignKey extends AbstractOperation
{
    /**
     * Some options has set of aliases.
     */
    protected array $aliases = [
        'onDelete' => ['delete'],
        'onUpdate' => ['update'],
    ];

    public function __construct(string $table, protected array $columns)
    {
        parent::__construct($table);
    }

    public function columnNames(): string
    {
        return \implode(', ', $this->columns);
    }
}
