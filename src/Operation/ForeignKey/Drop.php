<?php
/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Migrations\Operation\ForeignKey;

use Spiral\Migrations\CapsuleInterface;
use Spiral\Migrations\Exception\Operation\ForeignKeyException;

final class Drop extends ForeignKey
{
    /**
     * {@inheritdoc}
     */
    public function execute(CapsuleInterface $capsule)
    {
        $schema = $capsule->getSchema($this->getTable());

        if (!$schema->hasForeignKey($this->columns)) {
            throw new ForeignKeyException(
                "Unable to drop foreign key '{$schema->getName()}'.'{$this->columnNames()}', "
                . "foreign key does not exists"
            );
        }

        $schema->dropForeignKey($this->columns);
    }
}