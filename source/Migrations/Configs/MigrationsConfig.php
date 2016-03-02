<?php
/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */
namespace Spiral\Migrations\Configs;

use Spiral\Core\InjectableConfig;

class MigrationsConfig extends InjectableConfig
{
    /**
     * Configuration section.
     */
    const CONFIG = 'modules/migrations';

    /**
     * @var array
     */
    protected $config = [
        'directory' => '',
        'database'  => 'default',
        'table'     => 'migrations',
        'safe'      => false
    ];

    /**
     * Migrations directory.
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->config['directory'];
    }

    /**
     * @return string
     */
    public function getDatabase()
    {
        return $this->config['database'];
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->config['table'];
    }

    /**
     * @return array
     */
    public function isSafe()
    {
        return $this->config['safe'];
    }
}