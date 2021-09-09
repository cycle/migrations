<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations;

use Cycle\Migrations\Exception\MigrationException;
use Cycle\Migrations\MigrationInterface;

interface MigratorInterface
{
    /**
     * Configure all related databases with migration table.
     *
     * @return void
     */
    public function configure(): void;

    /**
     * Execute one migration and return it's instance.
     *
     * @param CapsuleInterface|null $capsule
     * @return MigrationInterface|null
     * @throws MigrationException
     */
    public function run(CapsuleInterface $capsule = null): ?MigrationInterface;

    /**
     * Rollback last migration and return it's instance.
     *
     * @param CapsuleInterface|null $capsule
     * @return MigrationInterface|null
     * @throws MigrationException
     */
    public function rollback(CapsuleInterface $capsule = null): ?MigrationInterface;
}
