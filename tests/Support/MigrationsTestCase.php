<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Support;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Database;
use Cycle\Database\DatabaseManager;
use Cycle\Database\Schema\AbstractTable;
use Cycle\Migrations\Atomizer\Atomizer;
use Cycle\Migrations\Atomizer\Renderer;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\FileRepository;
use Cycle\Migrations\Migration;
use Cycle\Migrations\Migrator;
use Cycle\Testing\Database\DatabaseTestCase;
use Spiral\Core\Container;
use Spiral\Files\Files;
use Spiral\Reactor\FileDeclaration;
use Spiral\Reactor\Partial\PhpNamespace;
use Testo\Assert;
use Testo\Lifecycle\AfterTest;
use Testo\Lifecycle\BeforeTest;

/**
 * A migrator over the suite's connection: `default` with the `tests_` prefix plus `slave`, migration
 * files written to `tests/files/`.
 */
abstract class MigrationsTestCase extends DatabaseTestCase
{
    protected const FILES = __DIR__ . '/../files/';
    protected const CONFIG = [
        'directory' => self::FILES,
        'table' => 'migrations',
        'safe' => true,
        'vendorDirectories' => self::FILES . 'vendor/',
    ];

    protected Migrator $migrator;
    protected MigrationConfig $migrationConfig;
    protected Database $db;
    protected FileRepository $repository;

    #[BeforeTest]
    public function createMigrator(): void
    {
        $dbal = new DatabaseManager(new DatabaseConfig(['default' => 'default', 'databases' => [], 'connections' => []]));
        $dbal->addDatabase($this->db = $this->db('default', 'tests_', $this->driverConfig()));
        $dbal->addDatabase($this->db('slave', 'slave_', $this->driverConfig()));

        $this->migrationConfig = new MigrationConfig(static::CONFIG);
        $this->repository = new FileRepository($this->migrationConfig, new Container());
        $this->migrator = new Migrator($this->migrationConfig, $dbal, $this->repository);
    }

    #[AfterTest]
    public function removeMigrationFiles(): void
    {
        $files = new Files();
        foreach ([self::FILES, self::FILES . 'vendor/'] as $directory) {
            foreach ($files->getFiles($directory, '*.php') as $file) {
                $files->delete($file);
                \clearstatcache(true, $file);
            }
        }
    }

    /**
     * Driver config overrides for the migrator's connection, see {@see DatabaseTestCase::db()}.
     *
     * @return array<non-empty-string, mixed>
     */
    protected function driverConfig(): array
    {
        return [];
    }

    /**
     * The schema of a table in the migrator's `default` database.
     */
    protected function schema(string $table, string $prefix = 'tests_', array $driverConfig = []): AbstractTable
    {
        return parent::schema($table, $prefix, $driverConfig ?: $this->driverConfig());
    }

    protected function atomize(string $name, array $tables): void
    {
        $atomizer = new Atomizer(new Renderer());

        $name = $name . '_' . \md5(\microtime(true) . \microtime(false));

        foreach ($tables as $table) {
            $atomizer->addTable($table);
        }

        Assert::count($atomizer->getTables(), \count($tables));

        $namespace = new PhpNamespace('Migrations');
        $namespace->addUse(Migration::class);

        $class = $namespace->addClass($name);
        $class->setExtends(Migration::class);

        $class->addMethod('up')->setPublic()->setReturnType('void');
        $class->addMethod('down')->setPublic()->setReturnType('void');

        $atomizer->declareChanges($class->getMethod('up'));
        $atomizer->revertChanges($class->getMethod('down'));

        $file = new FileDeclaration();
        $file->addNamespace($namespace);

        $this->repository->registerMigration($name, $name, (string) $file);
    }
}
