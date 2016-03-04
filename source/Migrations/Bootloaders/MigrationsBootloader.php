<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
namespace Spiral\Migrations\Bootloaders;

use Spiral\Core\Bootloaders\Bootloader;
use Spiral\Migrations\FileRepository;
use Spiral\Migrations\RepositoryInterface;

/**
 * Migrations bootloader only define default migrations repository.
 */
class MigrationsBootloader extends Bootloader
{
    /**
     * @return array
     */
    protected $bindings = [
        RepositoryInterface::class => FileRepository::class
    ];
}