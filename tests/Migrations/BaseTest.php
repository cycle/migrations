<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests;

use Cycle\Database\DatabaseProviderInterface;
use Cycle\Database\Driver\DriverInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerAwareInterface;
use Spiral\Core\Container;
use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Database;
use Cycle\Database\DatabaseManager;
use Cycle\Database\Driver\HandlerInterface;
use Cycle\Database\Schema\AbstractTable;
use Cycle\Database\Schema\Comparator;
use Cycle\Database\Schema\Reflector;
use Spiral\Files\Files;
use Cycle\Migrations\Atomizer\Atomizer;
use Cycle\Migrations\Atomizer\Renderer;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\FileRepository;
use Cycle\Migrations\Migration;
use Cycle\Migrations\Migrator;
use Spiral\Reactor\FileDeclaration;
use Spiral\Reactor\Partial\PhpNamespace;

abstract class BaseTest extends TestCase
{
    public const DRIVER = null;

    public const CONFIG = [
        'directory' => __DIR__ . '/../files/',
        'table' => 'migrations',
        'safe' => true,
    ];

    public static array $config;
    protected static array $driverCache = [];
    protected DriverInterface $driver;
    protected ContainerInterface $container;
    protected Migrator $migrator;
    protected MigrationConfig $migrationConfig;
    protected DatabaseProviderInterface $dbal;
    protected Database $db;
    protected FileRepository $repository;

    public function setUp(): void
    {
        if (static::$config['debug']) {
            echo "\n\n-------- BEGIN: " . $this->getName() . " --------------\n\n";
        }

        $this->container = new Container();
        $this->dbal = $this->getDBAL();

        $this->migrationConfig = new MigrationConfig(static::CONFIG);

        $this->migrator = new Migrator(
            $this->migrationConfig,
            $this->dbal,
            $this->repository = new FileRepository($this->migrationConfig, $this->container)
        );
    }

    /**
     * @throws \Throwable
     */
    public function tearDown(): void
    {
        $files = new Files();
        foreach ($files->getFiles(__DIR__ . '/../files/', '*.php') as $file) {
            $files->delete($file);
            clearstatcache(true, $file);
        }

        //Clean up
        $reflector = new Reflector();
        foreach ($this->dbal->database()->getTables() as $table) {
            $schema = $table->getSchema();
            $schema->declareDropped();
            $reflector->addTable($schema);
        }

        $reflector->run();

        if (static::$config['debug']) {
            echo "\n\n-------- END: " . $this->getName() . " --------------\n\n";
        }
    }

    public function getDriver(): DriverInterface
    {
        $config = self::$config[static::DRIVER];
        if (!isset($this->driver)) {
            $this->driver = $config->driver::create($config);
        }

        if (self::$config['debug']) {
            if ($this->driver instanceof LoggerAwareInterface) {
                $this->driver->setLogger(new TestLogger());
            }
        }

        return $this->driver;
    }

    protected function atomize(string $name, array $tables): void
    {
        $atomizer = new Atomizer(new Renderer());

        //Make sure name is unique\
        $name = $name . '_' . md5(microtime(true) . microtime(false));

        foreach ($tables as $table) {
            $atomizer->addTable($table);
        }

        $this->assertCount(count($tables), $atomizer->getTables());

        //Rendering
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

    protected function db(string $name = 'default', string $prefix = ''): Database
    {
        if (isset(static::$driverCache[static::DRIVER])) {
            $driver = static::$driverCache[static::DRIVER];
        } else {
            static::$driverCache[static::DRIVER] = $driver = $this->getDriver();
        }

        return new Database($name, $prefix, $driver);
    }

    protected function schema(string $table): AbstractTable
    {
        return $this->db->table($table)->getSchema();
    }

    protected function getDBAL(): DatabaseProviderInterface
    {
        $dbal = new DatabaseManager(
            new DatabaseConfig([
                'default' => 'default',
                'aliases' => [],
                'databases' => [],
                'connections' => [],
            ])
        );

        $dbal->addDatabase(
            $this->db = new Database(
                'default',
                'tests_',
                $this->getDriver()
            )
        );

        $dbal->addDatabase(new Database(
            'slave',
            'slave_',
            $this->getDriver()
        ));

        return $dbal;
    }

    protected function dropDatabase(Database $database = null): void
    {
        if (empty($database)) {
            return;
        }

        foreach ($database->getTables() as $table) {
            $schema = $table->getSchema();

            foreach ($schema->getForeignKeys() as $foreign) {
                $schema->dropForeignKey($foreign->getColumns());
            }

            $schema->save(HandlerInterface::DROP_FOREIGN_KEYS);
        }

        foreach ($database->getTables() as $table) {
            $schema = $table->getSchema();
            $schema->declareDropped();
            $schema->save();
        }
    }

    protected function assertSameAsInDB(AbstractTable $current): void
    {
        $source = $current->getState();
        $target = $this->fetchSchema($current)->getState();

        // testing changes

        $this->assertSame(
            $source->getPrimaryKeys(),
            $target->getPrimaryKeys(),
            'Primary keys changed'
        );

        $this->assertSame(
            count($source->getColumns()),
            count($target->getColumns()),
            'Column number has changed'
        );

        $this->assertSame(
            count($source->getIndexes()),
            count($target->getIndexes()),
            'Index number has changed'
        );

        $this->assertSame(
            count($source->getForeignKeys()),
            count($target->getForeignKeys()),
            'FK number has changed'
        );

        // columns

        foreach ($source->getColumns() as $column) {
            $this->assertTrue(
                $target->hasColumn($column->getName()),
                "Column {$column} has been removed"
            );

            $this->assertTrue(
                $column->compare($target->findColumn($column->getName())),
                "Column {$column} has been changed"
            );
        }

        foreach ($target->getColumns() as $column) {
            $this->assertTrue(
                $source->hasColumn($column->getName()),
                "Column {$column} has been added"
            );

            $this->assertTrue(
                $column->compare($source->findColumn($column->getName())),
                "Column {$column} has been changed"
            );
        }

        // indexes

        foreach ($source->getIndexes() as $index) {
            $this->assertTrue(
                $target->hasIndex($index->getColumns()),
                "Index {$index->getName()} has been removed"
            );

            $this->assertTrue(
                $index->compare($target->findIndex($index->getColumns())),
                "Index {$index->getName()} has been changed"
            );
        }

        foreach ($target->getIndexes() as $index) {
            $this->assertTrue(
                $source->hasIndex($index->getColumns()),
                "Index {$index->getName()} has been removed"
            );

            $this->assertTrue(
                $index->compare($source->findIndex($index->getColumns())),
                "Index {$index->getName()} has been changed"
            );
        }

        // FK
        foreach ($source->getForeignKeys() as $key) {
            $this->assertTrue(
                $target->hasForeignKey($key->getColumns()),
                "FK {$key->getName()} has been removed"
            );

            $this->assertTrue(
                $key->compare($target->findForeignKey($key->getColumns())),
                "FK {$key->getName()} has been changed"
            );
        }

        foreach ($target->getForeignKeys() as $key) {
            $this->assertTrue(
                $source->hasForeignKey($key->getColumns()),
                "FK {$key->getName()} has been removed"
            );

            $this->assertTrue(
                $key->compare($source->findForeignKey($key->getColumns())),
                "FK {$key->getName()} has been changed"
            );
        }

        // everything else
        $comparator = new Comparator(
            $current->getState(),
            $this->schema($current->getName())->getState()
        );

        if ($comparator->hasChanges()) {
            $this->fail($this->makeMessage($current->getName(), $comparator));
        }
    }

    protected function fetchSchema(AbstractTable $table): AbstractTable
    {
        return $this->schema($table->getName());
    }

    protected function makeMessage(string $table, Comparator $comparator): string
    {
        if ($comparator->isPrimaryChanged()) {
            return "Table '{$table}' not synced, primary indexes are different.";
        }

        if ($comparator->droppedColumns()) {
            return "Table '{$table}' not synced, columns are missing.";
        }

        if ($comparator->addedColumns()) {
            return "Table '{$table}' not synced, new columns found.";
        }

        if ($comparator->alteredColumns()) {
            $names = [];
            foreach ($comparator->alteredColumns() as $pair) {
                $names[] = $pair[0]->getName();
            }

            return "Table '{$table}' not synced, column(s) '" . implode(
                "', '",
                $names
            ) . "' have been changed.";
        }

        if ($comparator->droppedForeignKeys()) {
            return "Table '{$table}' not synced, FKs are missing.";
        }

        if ($comparator->addedForeignKeys()) {
            return "Table '{$table}' not synced, new FKs found.";
        }


        return "Table '{$table}' not synced, no idea why, add more messages :P";
    }
}
