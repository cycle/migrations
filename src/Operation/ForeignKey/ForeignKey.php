<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Operation\ForeignKey;

use Cycle\Migrations\Operation\AbstractOperation;
use Spiral\Migrations\Operation\ForeignKey\ForeignKey as SpiralForeignKey;

abstract class ForeignKey extends AbstractOperation
{
    /**
     * Some options has set of aliases.
     *
     * @var array
     */
    protected $aliases = [
        'onDelete' => ['delete'],
        'onUpdate' => ['update'],
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
        return implode(', ', $this->columns);
    }
}
\class_alias(ForeignKey::class, SpiralForeignKey::class, false);
