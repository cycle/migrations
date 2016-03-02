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

class RenameTable extends TableOperation
{
    /**
     * @var string
     */
    private $newName = '';

    /**
     * @param string $database
     * @param string $table
     * @param string $newName
     */
    public function __construct($database, $table, $newName)
    {
        parent::__construct($database, $table);
        $this->newName = $newName;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context)
    {
        $schema = $context->getSchema($this->getDatabase(), $this->getTable());
        $database = !empty($this->database) ? $this->database : '[default]';

        if (!$schema->exists()) {
            throw new TableException(
                "Unable to rename table '{$database}'.'{$this->getTable()}', table does not exists"
            );
        }

        if ($context->getDatabase($this->getDatabase())->hasTable($this->newName)) {
            throw new TableException(
                "Unable to rename table '{$database}'.'{$this->getTable()}', table '{$this->newName}' already exists"
            );
        }

        $schema->setName($this->newName);
        $schema->save();
    }
}