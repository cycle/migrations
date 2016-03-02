<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
namespace Spiral\Migrations;

use Spiral\Database\Entities\Database;
use Spiral\Migrations\Migration\Meta;

abstract class Migration implements MigrationInterface
{
    /**
     * @var Meta|null
     */
    private $status = null;

    /**
     * @var MigrationContext
     */
    private $context = null;

    /**
     * @param MigrationContext $context
     */
    public function setContext(MigrationContext $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function setMeta(Meta $state)
    {
        $this->status = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        return $this->status;
    }

    /**
     * @param string $database
     * @return Database
     */
    public function database($database = null)
    {
        return $this->context->getDatabase($database);
    }

    /**
     * @param string      $table
     * @param string|null $database
     * @return TableBlueprint
     */
    public function table($table, $database = null)
    {
        return new TableBlueprint($this->context, $database, $table);
    }
}