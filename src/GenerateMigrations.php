<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations;

use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\GeneratorInterface;
use Cycle\Schema\Registry;
use Spiral\Database\Schema\AbstractTable;
use Spiral\Migrations\Atomizer\Atomizer;
use Spiral\Migrations\Atomizer\Renderer;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\RepositoryInterface;

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

    /** @var MigrationConfig */
    private $migrationConfig;

    /**
     * GenerateMigrations constructor.
     *
     * @param RepositoryInterface $migrationRepository
     * @param MigrationConfig     $migrationConfig
     */
    public function __construct(
        RepositoryInterface $migrationRepository,
        MigrationConfig $migrationConfig
    ) {
        $this->repository = $migrationRepository;
        $this->migrationConfig = $migrationConfig;
    }

    /**
     * @param Registry $registry
     *
     * @return Registry
     */
    public function run(Registry $registry): Registry
    {
        $databases = [];
        foreach ($registry as $e) {
            if ($registry->hasTable($e) && !$e->getOptions()->has(SyncTables::READONLY_SCHEMA)) {
                $databases[$registry->getDatabase($e)][] = $registry->getTableSchema($e);
            }
        }

        foreach ($databases as $database => $tables) {
            $image = $this->generate($database, $tables);
            if (!$image) {
                // no changes
                continue;
            }
            $class = $image->getClass()->getName();
            $name = substr($image->buildFileName(), 0, 128);

            $this->repository->registerMigration($name, $class, $image->getFile()->render());
        }

        return $registry;
    }

    /**
     * @param string          $database
     * @param AbstractTable[] $tables
     *
     * @return array [string, FileDeclaration]
     */
    protected function generate(string $database, array $tables): ?MigrationImage
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
            return null;
        }

        $image = new MigrationImage($this->migrationConfig, $database);
        $image->setName($this->generateName($atomizer));
        $image->fileNamePattern = self::$sec++.'_{database}_{name}';

        $atomizer->declareChanges($image->getClass()->method('up')->getSource());
        $atomizer->revertChanges($image->getClass()->method('down')->getSource());

        return $image;
    }

    /**
     * @param Atomizer $atomizer
     *
     * @return string
     */
    private function generateName(Atomizer $atomizer): string
    {
        $name = [];

        foreach ($atomizer->getTables() as $table) {
            if ($table->getStatus() === AbstractTable::STATUS_NEW) {
                $name[] = 'create_'.$table->getName();
                continue;
            }

            if ($table->getStatus() === AbstractTable::STATUS_DECLARED_DROPPED) {
                $name[] = 'drop_'.$table->getName();
                continue;
            }

            if ($table->getComparator()->isRenamed()) {
                $name[] = 'rename_'.$table->getInitialName();
                continue;
            }

            $name[] = 'change_'.$table->getName();

            $comparator = $table->getComparator();

            foreach ($comparator->addedColumns() as $column) {
                $name[] = 'add_'.$column->getName();
            }

            foreach ($comparator->droppedColumns() as $column) {
                $name[] = 'rm_'.$column->getName();
            }

            foreach ($comparator->alteredColumns() as $column) {
                $name[] = 'alter_'.$column[0]->getName();
            }

            foreach ($comparator->addedIndexes() as $index) {
                $name[] = 'add_index_'.$index->getName();
            }

            foreach ($comparator->droppedIndexes() as $index) {
                $name[] = 'rm_index_'.$index->getName();
            }

            foreach ($comparator->alteredIndexes() as $index) {
                $name[] = 'alter_index_'.$index[0]->getName();
            }

            foreach ($comparator->addedForeignKeys() as $fk) {
                $name[] = 'add_fk_'.$fk->getName();
            }

            foreach ($comparator->droppedForeignKeys() as $fk) {
                $name[] = 'rm_fk_'.$fk->getName();
            }

            foreach ($comparator->alteredForeignKeys() as $fk) {
                $name[] = 'alter_fk_'.$fk[0]->getName();
            }
        }

        return implode('_', $name);
    }
}
