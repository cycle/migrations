<?php
/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Config;

use Spiral\Core\InjectableConfig;

class MigrationConfig extends InjectableConfig
{
    const CONFIG = 'migration';

    /**
     * @var array
     */
    protected $config = [
        'directories' => [''],
        'table'       => 'migrations',
        'safe'        => false
    ];

    /**
     * Migrations directories.
     *
     * @return array
     */
    public function getDirectories(): array
    {
        return [$this->config['directory']];
    }

    /**
     * Table to store list of executed migrations.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->config['table'];
    }

    /**
     * Is it safe to run migration without user confirmation? Attention, this option does not
     * used in component directly and left for component consumers.
     *
     * @return bool
     */
    public function isSafe(): bool
    {
        return $this->config['safe'];
    }
}