<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Operation\Column;

use Cycle\Migrations\CapsuleInterface;
use Cycle\Migrations\Exception\Operation\ColumnException;

final class Alter extends Column
{
    /**
     * {@inheritdoc}
     */
    public function execute(CapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());

        if (!$schema->hasColumn($this->name)) {
            throw new ColumnException(
                "Unable to alter column '{$schema->getName()}'.'{$this->name}', column does not exists"
            );
        }

        //Declaring column change
        $this->declareColumn($schema);
    }
}
