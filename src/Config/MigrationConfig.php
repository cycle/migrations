<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Config;

use Spiral\Core\InjectableConfig;

final class MigrationConfig extends InjectableConfig
{
    /**
     * @internal This is an internal config section name. Please, do not use
     *           this constant.
     */
    public const CONFIG = 'migration';

    /**
     * @param array{directory?: string|null, table?: string|null, safe?: bool|null} $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

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
