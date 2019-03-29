<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Operation\Column;

use Spiral\Migrations\CapsuleInterface;
use Spiral\Migrations\Exception\Operation\ColumnException;

final class Alter extends Column
{
    /**
     * {@inheritdoc}
     */
    public function execute(CapsuleInterface $capsule)
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