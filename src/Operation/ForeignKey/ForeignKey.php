<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Operation\ForeignKey;

use Cycle\Migrations\Operation\AbstractOperation;

abstract class ForeignKey extends AbstractOperation
{
    /**
     * Some options has set of aliases.
     *
     * @var array
     */
    protected $aliases = [
        'onDelete' => ['delete'],
        'onUpdate' => ['update']
    ];

    /**
     * Column foreign key associated to.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * @param string $table
     * @param array  $columns
     */
    public function __construct(string $table, array $columns)
    {
        parent::__construct($table);
        $this->columns = $columns;
    }

    /**
     * @return string
     */
    public function columnNames(): string
    {
        return join(', ', $this->columns);
    }
}
