<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
namespace Spiral\Migrations;

interface RepositoryInterface
{
    /**
     * Get every available migration from repository in a valid order, Meta property of migration
     * must be filled with information except execution status and execution time.
     *
     * @return MigrationInterface[]
     */
    public function getMigrations();

    /**
     * Register new migration using given migration file body (must be valid filename), every
     * migration must have unique class name
     *
     * @param string $name
     * @param string $class
     * @param string $body When body is null repository will try to copy content from a specific
     *                     class filename.
     * @return string Migration filename.
     */
    public function registerMigration($name, $class, $body = null);
}