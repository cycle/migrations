<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Operation\ForeignKey;

use Spiral\Migrations\CapsuleInterface;
use Spiral\Migrations\Exceptions\Operation\ReferenceException;

class Drop extends ForeignKey
{
    /**
     * {@inheritdoc}
     */
    public function execute(CapsuleInterface $capsule)
    {
        $schema = $capsule->getSchema($this->getTable());

        if (!$schema->hasForeignKey($this->column)) {
            throw new ReferenceException(
                "Unable to drop foreign key '{$schema->getName()}'.'{$this->column}', "
                . "foreign key does not exists"
            );
        }

        $schema->dropForeignKey($this->column);
    }
}