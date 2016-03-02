<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
namespace Spiral\Migrations\Operations\Columns;

use Spiral\Migrations\ContextInterface;
use Spiral\Migrations\Exceptions\Operations\ColumnException;
use Spiral\Migrations\Operations\ColumnOperation;

class AlterColumn extends ColumnOperation
{
    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context)
    {
        $schema = $context->getSchema($this->getDatabase(), $this->getTable());

        if (!$schema->hasColumn($this->name)) {
            throw new ColumnException(
                "Unable to alter column '{$schema->getName()}'.'{$this->name}', column does not exists"
            );
        }

        //Declaring column change
        $this->declareColumn($schema);
    }
}