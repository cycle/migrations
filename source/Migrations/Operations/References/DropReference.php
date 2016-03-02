<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
namespace Spiral\Migrations\Operations\References;

use Spiral\Migrations\ContextInterface;
use Spiral\Migrations\Exceptions\Operations\ReferenceException;
use Spiral\Migrations\Operations\ReferenceOperation;

class DropReference extends ReferenceOperation
{
    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context)
    {
        $schema = $context->getSchema($this->getDatabase(), $this->getTable());

        if (!$schema->hasForeign($this->column)) {
            throw new ReferenceException(
                "Unable to drop foreign key '{$schema->getName()}'.'{$this->column}', "
                . "foreign key does not exists"
            );
        }

        $schema->dropForeign($this->column);
    }
}