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
use Spiral\Migrations\CapsuleInterface as SpiralCapsuleInterface;
use Cycle\Migrations\Exception\Operation\TableException;
use Cycle\Migrations\Operation\AbstractOperation;

final class Update extends AbstractOperation
{
    /**
     * {@inheritDoc}
     */
    public function execute(SpiralCapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());
        $database = $this->database ?? '[default]';

        if (!$schema->exists()) {
            throw new TableException(
                "Unable to update table '{$database}'.'{$this->getTable()}', no table exists"
            );
        }

        $schema->save(HandlerInterface::DO_ALL);
    }
}
