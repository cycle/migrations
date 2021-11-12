<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Operation\Index;

use Cycle\Migrations\Operation\AbstractOperation;
use Spiral\Migrations\Operation\Index\Index as SpiralIndex;

abstract class Index extends AbstractOperation
{
    /** @var array */
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
}
\class_alias(Index::class, SpiralIndex::class, false);
