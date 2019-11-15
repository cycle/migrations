<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations;

use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Spiral\Database\Schema\AbstractTable;
use Spiral\Migrations\Atomizer\Atomizer;
use Spiral\Migrations\Atomizer\Renderer;
use Spiral\Migrations\Atomizer\RendererInterface;
use Spiral\Migrations\Config\MigrationConfig;
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
    /** @var int */
    private static $sec = 0;

    /** @var RepositoryInterface */
    private $repository;

    /** @var RendererInterface */
    private $renderer;

    /** @var MigrationConfig $migrationConfig */
    private $migrationConfig;

    /**
     * GenerateMigrations constructor.
     *
     * @param RepositoryInterface    $migrationRepository
     * @param MigrationConfig        $migrationConfig
     * @param RendererInterface|null $renderer
     */
    public function __construct(
        RepositoryInterface $migrationRepository,
        MigrationConfig $migrationConfig,
        RendererInterface $renderer = null
    ) {
        $this->repository = $migrationRepository;
        $this->migrationConfig = $migrationConfig;
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

        foreach ($databases as $database => $tables) {
            list($name, $class, $file) = $this->generate($database, $tables);
            if ($class === null || $file === null) {
                // no changes
                continue;
            }

            $this->repository->registerMigration($name, $class, $file->render());
        }

        return $registry;
    }

    /**
     * @param string          $database
     * @param AbstractTable[] $tables
     * @return array [string, FileDeclaration]
     */
    protected function generate(string $database, array $tables): array
    {
        $atomizer = new Atomizer(new Renderer());

        $reasonable = false;
        foreach ($tables as $table) {
            if ($table->getComparator()->hasChanges()) {
                $reasonable = true;
                $atomizer->addTable($table);
            }
        }

        if (!$reasonable) {
            return [null, null, null];
        }

        // unique class name for the migration
        $name = sprintf(
            'orm_%s_%s',
            $database,
            md5(microtime(true) . microtime(false))
        );

        $class = new ClassDeclaration($name, 'Migration');
        $class->constant('DATABASE')->setProtected()->setValue($database);

        $class->method('up')->setPublic();
        $class->method('down')->setPublic();

        $atomizer->declareChanges($class->method('up')->getSource());
        $atomizer->revertChanges($class->method('down')->getSource());

        $file = new FileDeclaration($this->migrationConfig->getNamespace());
        $file->addUse(Migration::class);
        $file->addElement($class);

        return [
            substr(sprintf(
                '%s_%s_%s',
                self::$sec++,
                $database,
                $this->generateName($atomizer)
            ), 0, 128),
            $class->getName(),
            $file
        ];
    }

    /**
     * @param Atomizer $atomizer
     * @return string
     */
    private function generateName(Atomizer $atomizer): string
    {
        $name = [];

        foreach ($atomizer->getTables() as $table) {
            if ($table->getStatus() === AbstractTable::STATUS_NEW) {
                $name[] = 'create_' . $table->getName();
                continue;
            }

            if ($table->getStatus() === AbstractTable::STATUS_DECLARED_DROPPED) {
                $name[] = 'drop_' . $table->getName();
                continue;
            }

            if ($table->getComparator()->isRenamed()) {
                $name[] = 'rename_' . $table->getInitialName();
                continue;
            }

            $name[] = 'change_' . $table->getName();

            $comparator = $table->getComparator();

            foreach ($comparator->addedColumns() as $column) {
                $name[] = 'add_' . $column->getName();
            }

            foreach ($comparator->droppedColumns() as $column) {
                $name[] = 'rm_' . $column->getName();
            }

            foreach ($comparator->alteredColumns() as $column) {
                $name[] = 'alter_' . $column[0]->getName();
            }

            foreach ($comparator->addedIndexes() as $index) {
                $name[] = 'add_index_' . $index->getName();
            }

            foreach ($comparator->droppedIndexes() as $index) {
                $name[] = 'rm_index_' . $index->getName();
            }

            foreach ($comparator->alteredIndexes() as $index) {
                $name[] = 'alter_index_' . $index[0]->getName();
            }

            foreach ($comparator->addedForeignKeys() as $fk) {
                $name[] = 'add_fk_' . $fk->getName();
            }

            foreach ($comparator->droppedForeignKeys() as $fk) {
                $name[] = 'rm_fk_' . $fk->getName();
            }

            foreach ($comparator->alteredForeignKeys() as $fk) {
                $name[] = 'alter_fk_' . $fk[0]->getName();
            }
        }

        return join('_', $name);
    }
}
