<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Operation\Index;

use Spiral\Migrations\CapsuleInterface;
use Spiral\Migrations\Exception\Operation\IndexException;

class Drop extends Index
{
    /**
     * {@inheritdoc}
     */
    public function execute(CapsuleInterface $capsule)
    {
        $schema = $capsule->getSchema($this->getTable());

        if (!$schema->hasIndex($this->columns)) {
            $columns = join(',', $this->columns);
            throw new IndexException(
                "Unable to drop index '{$schema->getName()}'.({$columns}), index does not exists"
            );
        }

        $schema->dropIndex($this->columns);
    }
}