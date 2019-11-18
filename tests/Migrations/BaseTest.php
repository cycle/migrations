<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Generator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Migrations\GenerateMigrations;
use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use PHPUnit\Framework\TestCase;
use Spiral\Core\Container;
use Spiral\Database\Config\DatabaseConfig;
use Spiral\Database\Database;
use Spiral\Database\DatabaseManager;
use Spiral\Database\Driver\Driver;
use Spiral\Database\Driver\Handler;
use Spiral\Database\Schema\AbstractTable;
use Spiral\Database\Schema\Comparator;
use Spiral\Files\Files;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\FileRepository;
use Spiral\Migrations\Migrator;
use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class BaseTest extends TestCase
{

    // currently active driver
    public const DRIVER = null;

    public const CONFIG = [
        'directory' => __DIR__ . '/../files/',
        'table'     => 'migrations',
        'safe'      => true,
        'namespace' => 'Migration',
    ];
    // tests configuration
    public static $config;

    // cross test driver cache
    public static $driverCache = [];

    protected static $lastORM;

    /** @var Driver */
    protected $driver;

    /** @var DatabaseManager */
    protected $dbal;

    /** @var ORM */
    protected $orm;

    /** @var TestLogger */
    protected $logger;

    /** @var ClassesInterface */
    protected $locator;

    /** @var Migrator */
    protected $migrator;

    /**
     * Init all we need.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->dbal = new DatabaseManager(new DatabaseConfig([
            'default'   => 'default',
            'databases' => [],
        ]));
        $this->dbal->addDatabase(new Database(
            'default',
            '',
            $this->getDriver()
        ));

        $this->dbal->addDatabase(new Database(
            'secondary',
            'secondary_',
            $this->getDriver()
        ));

        $this->logger = new TestLogger();
        $this->getDriver()->setLogger($this->logger);

        if (self::$config['debug']) {
            $this->logger->display();
        }

        $this->logger = new TestLogger();
        $this->getDriver()->setLogger($this->logger);

        if (self::$config['debug']) {
            $this->logger->display();
        }

        $this->orm = new ORM(new Factory(
            $this->dbal,
            RelationConfig::getDefault()
        ));

        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/Fixtures'],
            'exclude'     => [],
        ]));

        $this->locator = $tokenizer->classLocator();

        $config = new MigrationConfig(static::CONFIG);

        $this->migrator = new Migrator(
            $config,
            $this->dbal,
            new FileRepository(
                $config,
                new Container(),
                new Tokenizer(new TokenizerConfig([
                    'directories' => [__DIR__ . '/../files'],
                    'exclude'     => [],
                ]))
            )
        );

        $this->migrator->configure();
    }

    /**
     * Cleanup.
     */
    public function tearDown(): void
    {
        $files = new Files();
        foreach ($files->getFiles(__DIR__ . '/../files/', '*.php') as $file) {
            $files->delete($file);
            clearstatcache(true, $file);
        }

        $this->disableProfiling();
        $this->dropDatabase($this->dbal->database('default'));
        $this->orm = null;
        $this->dbal = null;
    }

    /**
     * Calculates missing parameters for typecasting.
     *
     * @param SchemaInterface $schema
     * @return ORM|\Cycle\ORM\ORMInterface
     */
    public function withSchema(SchemaInterface $schema)
    {
        $this->orm = $this->orm->withSchema($schema);
        return $this->orm;
    }

    /**
     * @return Driver
     */
    public function getDriver(): Driver
    {
        if (isset(static::$driverCache[static::DRIVER])) {
            return static::$driverCache[static::DRIVER];
        }

        $config = self::$config[static::DRIVER];
        if (!isset($this->driver)) {
            $class = $config['driver'];

            $this->driver = new $class([
                'connection' => $config['conn'],
                'username'   => $config['user'],
                'password'   => $config['pass'],
                'options'    => []
            ]);
        }

        $this->driver->setProfiling(true);

        return static::$driverCache[static::DRIVER] = $this->driver;
    }

    protected function migrate(string $directory): array
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [$directory],
            'exclude'     => [],
        ]));

        $locator = $tokenizer->classLocator();

        $p = Generator::getDefaultParser();
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($locator, $p),
            new ResetTables(),
            new MergeColumns($p),
            new GenerateRelations(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($p),
            new GenerateTypecast(),
            new GenerateMigrations($this->migrator->getRepository(), new MigrationConfig(static::CONFIG))
        ]);

        $tables = [];
        foreach ($r as $e) {
            $tables[] = $r->getTableSchema($e);
        }
        return $tables;
    }

    /**
     * @return Database
     */
    protected function getDatabase(): Database
    {
        return $this->dbal->database('default');
    }

    /**
     * @param Database|null $database
     */
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

            $schema->save(Handler::DROP_FOREIGN_KEYS);
        }

        foreach ($database->getTables() as $table) {
            $schema = $table->getSchema();
            $schema->declareDropped();
            $schema->save();
        }
    }

    /**
     * For debug purposes only.
     */
    protected function enableProfiling(): void
    {
        if (!is_null($this->logger)) {
            $this->logger->display();
        }
    }

    /**
     * For debug purposes only.
     */
    protected function disableProfiling(): void
    {
        if (!is_null($this->logger)) {
            $this->logger->hide();
        }
    }

    protected function assertSameAsInDB(AbstractTable $current): void
    {
        $source = $current->getState();
        $target = $current->getDriver()->getSchema($current->getName())->getState();

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
            $current->getDriver()->getSchema($current->getName())->getState()
        );

        if ($comparator->hasChanges()) {
            $this->fail($this->makeMessage($current->getName(), $comparator));
        }
    }

    /**
     * @param string     $table
     * @param Comparator $comparator
     * @return string
     */
    protected function makeMessage(string $table, Comparator $comparator)
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
                print_r($pair);
            }

            return "Table '{$table}' not synced, column(s) '" . join(
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
