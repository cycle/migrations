<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations;

use Cycle\Migrations\Exception\RepositoryException;

interface RepositoryInterface
{
    /**
     * Get every available migration from repository in a valid order, Meta property of migration
     * must be filled with information except execution status and execution time.
     *
     * Attention, Capsule must be set for migration before executing it.
     *
     * @return MigrationInterface[]
     * @throws RepositoryException
     */
    public function getMigrations(): array;

    /**
     * Register new migration using given migration file body (must be valid filename), every
     * migration must have unique class name.
     *
     * @param string $name
     * @param string $class
     * @param string|null $body When body is null repository will try to copy content from a specific
     *                     class filename. Must include all PHP headers.
     *
     * @throws RepositoryException
     *
     * @return string      Migration filename.
     */
    public function registerMigration(string $name, string $class, string $body = null): string;
}
