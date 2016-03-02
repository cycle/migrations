<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
namespace Spiral\Migrations\Operations\Table;

use Spiral\Migrations\ContextInterface;
use Spiral\Migrations\Exceptions\Operations\TableException;
use Spiral\Migrations\Operations\TableOperation;

class PrimaryKeys extends TableOperation
{
    /**
     * @var array
     */
    private $columns = [];

    /**
     * @param string $database
     * @param string $table
     * @param array  $columns
     */
    public function __construct($database, $table, array $columns)
    {
        parent::__construct($database, $table);
        $this->columns = $columns;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context)
    {
        $schema = $context->getSchema($this->getDatabase(), $this->getTable());
        $database = !empty($this->database) ? $this->database : '[default]';

        if ($schema->exists()) {
            throw new TableException(
                "Unable to set primary keys for table '{$database}'.'{$this->getTable()}', table already exists"
            );
        }

        $schema->setPrimaryKeys($this->columns);
    }
}