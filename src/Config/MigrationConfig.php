<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Config;

use Spiral\Core\InjectableConfig;

final class MigrationConfig extends InjectableConfig
{
    public const CONFIG = 'migration';

    /**
     * Migrations directory.
     *
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->config['directory'] ?? '';
    }

    /**
     * Table to store list of executed migrations.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->config['table'] ?? 'migrations';
    }

    /**
     * Is it safe to run migration without user confirmation? Attention, this option does not
     * used in component directly and left for component consumers.
     *
     * @return bool
     */
    public function isSafe(): bool
    {
        return $this->config['safe'] ?? false;
    }

    /**
     * Namespace for generated migration class
     *
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->config['namespace'] ?? 'Migration';
    }
}
