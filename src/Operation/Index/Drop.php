<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Operation\Index;

use Cycle\Migrations\CapsuleInterface;
use Cycle\Migrations\Exception\Operation\IndexException;

final class Drop extends Index
{
    /**
     * {@inheritdoc}
     */
    public function execute(CapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());

        if (!$schema->hasIndex($this->columns)) {
            $columns = implode(',', $this->columns);
            throw new IndexException(
                "Unable to drop index '{$schema->getName()}'.({$columns}), index does not exists"
            );
        }

        $schema->dropIndex($this->columns);
    }
}
