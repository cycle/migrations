<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
namespace Spiral\Migrations;

use Spiral\Migrations\Migration\Meta;

interface MigrationInterface
{
    /**
     * @param MigrationContext $pipeline
     */
    public function setContext(MigrationContext $pipeline);

    /**
     * @param Meta $state
     */
    public function setMeta(Meta $state);

    /**
     * @return Meta|null
     */
    public function getMeta();

    /**
     * Up migration.
     */
    public function up();

    /**
     * Rollback migration.
     */
    public function down();
}