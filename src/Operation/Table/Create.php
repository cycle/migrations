<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Operation\Table;

use Spiral\Database\Driver\HandlerInterface;
use Spiral\Migrations\CapsuleInterface;
use Spiral\Migrations\Exception\Operation\TableException;
use Spiral\Migrations\Operation\AbstractOperation;

class Create extends AbstractOperation
{
    /**
     * {@inheritdoc}
     */
    public function execute(CapsuleInterface $capsule)
    {
        $schema = $capsule->getSchema($this->getTable());
        $database = $this->database ?? '[default]';

        if ($schema->exists()) {
            throw new TableException(
                "Unable to create table '{$database}'.'{$this->getTable()}', table already exists"
            );
        }

        if (empty($schema->getColumns())) {
            throw new TableException(
                "Unable to create table '{$database}'.'{$this->getTable()}', no columns were added"
            );
        }

        $schema->save(HandlerInterface::DO_ALL);
    }
}