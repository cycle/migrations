<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Operation\ForeignKey;

use Spiral\Migrations\CapsuleInterface as SpiralCapsuleInterface;
use Cycle\Migrations\Exception\Operation\ForeignKeyException;

final class Drop extends ForeignKey
{
    /**
     * {@inheritDoc}
     */
    public function execute(SpiralCapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());

        if (!$schema->hasForeignKey($this->columns)) {
            throw new ForeignKeyException(
                "Unable to drop foreign key '{$schema->getName()}'.'{$this->columnNames()}', "
                . 'foreign key does not exists'
            );
        }

        $schema->dropForeignKey($this->columns);
    }
}
