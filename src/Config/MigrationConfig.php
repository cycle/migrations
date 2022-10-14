<?php

declare(strict_types=1);

namespace Cycle\Migrations\Config;

use Spiral\Core\InjectableConfig;

final class MigrationConfig extends InjectableConfig
{
    public const CONFIG = 'migration';

    /**
     * Migrations directory.
     */
    public function getDirectory(): string
    {
        return $this->config['directory'] ?? '';
    }

    /**
     * Vendor migrations directories.
     */
    public function getVendorDirectories(): array
    {
        return (array) ($this->config['vendorDirectories'] ?? []);
    }

    /**
     * Table to store list of executed migrations.
     */
    public function getTable(): string
    {
        return $this->config['table'] ?? 'migrations';
    }

    /**
     * Is it safe to run migration without user confirmation? Attention, this option does not
     * used in component directly and left for component consumers.
     */
    public function isSafe(): bool
    {
        return $this->config['safe'] ?? false;
    }

    /**
     * Namespace for generated migration class
     */
    public function getNamespace(): string
    {
        return $this->config['namespace'] ?? 'Migration';
    }
}
