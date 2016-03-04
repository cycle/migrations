<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
namespace Spiral;

use Spiral\Core\DirectoriesInterface;
use Spiral\Modules\ModuleInterface;
use Spiral\Modules\PublisherInterface;
use Spiral\Modules\RegistratorInterface;

class MigrationsModule implements ModuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(RegistratorInterface $registrator)
    {
        //To ensure that our commands can be located
        $registrator->configure('tokenizer', 'directories', 'spiral/migrations', [
            "directory('libraries') . 'spiral/migrations'"
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function publish(PublisherInterface $publisher, DirectoriesInterface $directories)
    {
        $publisher->publish(
            __DIR__ . '/config/migrations.php',
            $directories->directory('config') . 'modules/migrations.php',
            PublisherInterface::FOLLOW
        );
    }
}