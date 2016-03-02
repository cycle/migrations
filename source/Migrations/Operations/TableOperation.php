<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
namespace Spiral\Migrations\Operations;

use Spiral\Database\Entities\Table;
use Spiral\Migrations\OperationInterface;

abstract class TableOperation implements OperationInterface
{
    /**
     * @var string|null
     */
    protected $database = null;

    /**
     * @var string
     */
    protected $table = null;

    /**
     * @param string $database
     * @param string $table
     */
    public function __construct($database, $table)
    {
        $this->database = $database;
        $this->table = $table;
    }

    /**
     * {@inheritdoc}
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }
}