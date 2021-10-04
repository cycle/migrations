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
     * {@inheritdoc}
     */
    public function getTable(): string
    {
        return $this->table;
    }
}
