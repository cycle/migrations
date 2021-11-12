<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Operation\Column;

use Cycle\Migrations\Exception\Operation\ColumnException;
use Spiral\Migrations\CapsuleInterface as SpiralCapsuleInterface;
use Spiral\Migrations\Operation\Column\Add as SpiralAdd;

\interface_exists(SpiralCapsuleInterface::class);

final class Add extends Column
{
    /**
     * {@inheritDoc}
     */
    public function execute(SpiralCapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());

        if ($schema->hasColumn($this->name)) {
            throw new ColumnException(
                "Unable to create column '{$schema->getName()}'.'{$this->name}', column already exists"
            );
        }

        //Declaring column
        $this->declareColumn($schema);
    }
}
\class_alias(Add::class, SpiralAdd::class, false);
