<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Operation\ForeignKey;

use Spiral\Migrations\CapsuleInterface;
use Spiral\Migrations\Exception\Operation\ForeignKeyException;

class Drop extends ForeignKey
{
    /**
     * {@inheritdoc}
     */
    public function execute(CapsuleInterface $capsule)
    {
        $schema = $capsule->getSchema($this->getTable());

        if (!$schema->hasForeignKey($this->column)) {
            throw new ForeignKeyException(
                "Unable to drop foreign key '{$schema->getName()}'.'{$this->column}', "
                . "foreign key does not exists"
            );
        }

        $schema->dropForeignKey($this->column);
    }
}