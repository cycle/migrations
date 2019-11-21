<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Migrations\Operation\Table;

use Spiral\Migrations\CapsuleInterface;
use Spiral\Migrations\Exception\Operation\TableException;
use Spiral\Migrations\Operation\AbstractOperation;

final class PrimaryKeys extends AbstractOperation
{
    /** @var array */
    private $columns = [];

    /**
     * @param string $table
     * @param array  $columns
     */
    public function __construct(string $table, array $columns)
    {
        parent::__construct($table);
        $this->columns = $columns;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(CapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());
        $database = $this->database ?? '[default]';

        if ($schema->exists()) {
            throw new TableException(
                "Unable to set primary keys for table '{$database}'.'{$this->getTable()}', table already exists"
            );
        }

        $schema->setPrimaryKeys($this->columns);
    }
}
