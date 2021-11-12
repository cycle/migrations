<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Operation\Table;

use Cycle\Migrations\Exception\Operation\TableException;
use Cycle\Migrations\Operation\AbstractOperation;
use Spiral\Migrations\CapsuleInterface as SpiralCapsuleInterface;
use Spiral\Migrations\Operation\Table\PrimaryKeys as SpiralPrimaryKeys;

\interface_exists(SpiralCapsuleInterface::class);

final class PrimaryKeys extends AbstractOperation
{
    /** @var array */
    private $columns = [];

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
     * {@inheritDoc}
     */
    public function execute(SpiralCapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());
        $database = $this->database ?? '[default]';

        if ($schema->exists()) {
            throw new TableException(
                "Unable to set primary keys for table '{$database}'.'{$this->getTable()}', table already exists"
            );
        }

        $schema->setPrimaryKeys($this->columns);
    }
}
\class_alias(PrimaryKeys::class, SpiralPrimaryKeys::class, false);
