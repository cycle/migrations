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
use Spiral\Migrations\Operations\TableOperation;

class DropColumn extends TableOperation
{
    /**
     * Column name.
     *
     * @var string
     */
    private $name = '';

    /**
     * @param string $database
     * @param string $table
     * @param string $name
     */
    public function __construct($database, $table, $name)
    {
        parent::__construct($database, $table);

        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context)
    {
        $schema = $context->getSchema($this->getDatabase(), $this->getTable());

        if (!$schema->hasColumn($this->name)) {
            throw new ColumnException(
                "Unable to drop column '{$schema->getName()}'.'{$this->name}', column does not exists"
            );
        }

        //Declaring column
        $schema->dropColumn($this->name);
    }
}