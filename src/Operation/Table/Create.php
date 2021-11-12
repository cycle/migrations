<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Operation\Table;

use Cycle\Database\Driver\HandlerInterface;
use Cycle\Migrations\Exception\Operation\TableException;
use Cycle\Migrations\Operation\AbstractOperation;
use Spiral\Migrations\CapsuleInterface as SpiralCapsuleInterface;
use Spiral\Migrations\Operation\Table\Create as SpiralCreate;

\interface_exists(SpiralCapsuleInterface::class);

final class Create extends AbstractOperation
{
    /**
     * {@inheritDoc}
     */
    public function execute(SpiralCapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());
        $database = $this->database ?? '[default]';

        if ($schema->exists()) {
            throw new TableException(
                "Unable to create table '{$database}'.'{$this->getTable()}', table already exists"
            );
        }

        if (empty($schema->getColumns())) {
            throw new TableException(
                "Unable to create table '{$database}'.'{$this->getTable()}', no columns were added"
            );
        }

        $schema->save(HandlerInterface::DO_ALL);
    }
}
\class_alias(Create::class, SpiralCreate::class, false);
