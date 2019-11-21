<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Migrations\Operation\Table;

use Spiral\Database\Driver\HandlerInterface;
use Spiral\Migrations\CapsuleInterface;
use Spiral\Migrations\Exception\Operation\TableException;
use Spiral\Migrations\Operation\AbstractOperation;

final class Rename extends AbstractOperation
{
    /** @var string */
    private $newName = '';

    /**
     * @param string $table
     * @param string $newName
     */
    public function __construct(string $table, string $newName)
    {
        parent::__construct($table);
        $this->newName = $newName;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(CapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());
        $database = $this->database ?? '[default]';

        if (!$schema->exists()) {
            throw new TableException(
                "Unable to rename table '{$database}'.'{$this->getTable()}', table does not exists"
            );
        }

        if ($capsule->getDatabase()->hasTable($this->newName)) {
            throw new TableException(
                "Unable to rename table '{$database}'.'{$this->getTable()}', table '{$this->newName}' already exists"
            );
        }

        $schema->setName($this->newName);
        $schema->save(HandlerInterface::DO_ALL);
    }
}
