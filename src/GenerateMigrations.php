<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Migrations;

use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Spiral\Migrations\Atomizer\Atomizer;
use Spiral\Migrations\Atomizer\Renderer;
use Spiral\Migrations\Atomizer\RendererInterface;
use Spiral\Migrations\Migration;
use Spiral\Migrations\RepositoryInterface;
use Spiral\Reactor\ClassDeclaration;
use Spiral\Reactor\FileDeclaration;

/**
 * Migration generator creates set of migrations needed to sync database schema with desired state. Each database will
 * receive it's own migration.
 */
class GenerateMigrations implements GeneratorInterface
{
    /** @var RepositoryInterface */
    private $repository;

    /** @var RendererInterface */
    private $renderer;

    /**
     * GenerateMigrations constructor.
     *
     * @param RepositoryInterface    $migrationRepository
     * @param RendererInterface|null $renderer
     */
    public function __construct(
        RepositoryInterface $migrationRepository,
        RendererInterface $renderer = null
    ) {
        $this->repository = $migrationRepository;
        $this->renderer = $renderer ?? new Renderer();
    }

    /**
     * @param Registry $registry
     * @return Registry
     */
    public function run(Registry $registry): Registry
    {
        $databases = [];
        foreach ($registry as $e) {
            if ($registry->hasTable($e)) {
                $databases[$registry->getDatabase($e)][] = $registry->getTableSchema($e);
            }
        }

        $seq = 0;
        foreach ($databases as $database => $tables) {
            $name = sprintf(
                "orm_%s_%s_%s_%s",
                $database,
                str_replace('.', '_', microtime(false)),
                ++$seq,
                md5(microtime(true) . microtime(false))
            );

            list($class, $file) = $this->generate($name, $database, $tables);
            $this->repository->registerMigration($name, $class, $file->render());
        }

        return $registry;
    }

    /**
     * @param string $name
     * @param string $database
     * @param array  $tables
     * @return array [string, FileDeclaration]
     */
    protected function generate(string $name, string $database, array $tables): array
    {
        $atomizer = new Atomizer(new Renderer());

        foreach ($tables as $table) {
            $atomizer->addTable($table);
        }

        //Rendering
        $class = new ClassDeclaration($name, Migration::class);
        $class->constant('DATABASE')->setProtected()->setValue($database);

        $class->method('up')->setPublic();
        $class->method('down')->setPublic();

        $atomizer->declareChanges($class->method('up')->getSource());
        $atomizer->revertChanges($class->method('down')->getSource());

        $file = new FileDeclaration();
        $file->addElement($class);

        return [$class->getName(), $file];
    }
}