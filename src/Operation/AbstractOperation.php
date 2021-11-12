<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Operation;

use Cycle\Migrations\OperationInterface;
use Spiral\Migrations\Operation\AbstractOperation as SpiralAbstractOperation;

abstract class AbstractOperation implements OperationInterface
{
    /** @var string */
    protected $table;

    /**
     * @param string $table
     */
    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * {@inheritDoc}
     */
    public function getTable(): string
    {
        return $this->table;
    }
}
\class_alias(AbstractOperation::class, SpiralAbstractOperation::class, false);
