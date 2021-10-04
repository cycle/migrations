<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations;

use Cycle\Migrations\Exception\MigrationException;

interface MigratorInterface
{
    /**
     * Configure all related databases with migration table.
     */
    public function configure(): void;

    /**
     * Execute one migration and return it's instance.
     *
     * @param CapsuleInterface|null $capsule
     *
     * @throws MigrationException
     *
     * @return MigrationInterface|null
     */
    public function run(CapsuleInterface $capsule = null): ?MigrationInterface;

    /**
     * Rollback last migration and return it's instance.
     *
     * @param CapsuleInterface|null $capsule
     *
     * @throws MigrationException
     *
     * @return MigrationInterface|null
     */
    public function rollback(CapsuleInterface $capsule = null): ?MigrationInterface;
}
