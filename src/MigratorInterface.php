<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations;

use Spiral\Migrations\CapsuleInterface as SpiralCapsuleInterface;
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
     * @param SpiralCapsuleInterface|CapsuleInterface|null $capsule The signature
     *        of this argument will be changed to {@see CapsuleInterface} in future release.
     * @return MigrationInterface|null
     * @throws MigrationException
     */
    public function run(SpiralCapsuleInterface $capsule = null): ?MigrationInterface;

    /**
     * Rollback last migration and return it's instance.
     *
     * @param SpiralCapsuleInterface|CapsuleInterface|null $capsule The signature
     *        of this argument will be changed to {@see CapsuleInterface} in future release.
     * @return MigrationInterface|null
     * @throws MigrationException
     */
    public function rollback(SpiralCapsuleInterface $capsule = null): ?MigrationInterface;
}
