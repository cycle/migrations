<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Migrations\Tests;

use Cycle\ORM\Config\RelationConfig;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\SchemaInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;
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
    // tests configuration
    public static $config;

    // currently active driver
    public const DRIVER = null;

    public const CONFIG = [
        'directory' => __DIR__ . '/../files/',
        'table'     => 'migrations',
        'safe'      => true
    ];

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
    public function setUp()
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
    public function tearDown()
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
    protected function dropDatabase(Database $database = null)
    {
        if (empty($database)) {
            return;
        }

        foreach ($database->getTables() as $table) {
            $schema = $table->getSchema();

            foreach ($schema->getForeignKeys() as $foreign) {
                $schema->dropForeignKey($foreign->getColumn());
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
    protected function enableProfiling()
    {
        if (!is_null($this->logger)) {
            $this->logger->display();
        }
    }

    /**
     * For debug purposes only.
     */
    protected function disableProfiling()
    {
        if (!is_null($this->logger)) {
            $this->logger->hide();
        }
    }

    protected function assertSameAsInDB(AbstractTable $current)
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
                $target->hasForeignKey($key->getColumn()),
                "FK {$key->getName()} has been removed"
            );

            $this->assertTrue(
                $key->compare($target->findForeignKey($key->getColumn())),
                "FK {$key->getName()} has been changed"
            );
        }

        foreach ($target->getForeignKeys() as $key) {
            $this->assertTrue(
                $source->hasForeignKey($key->getColumn()),
                "FK {$key->getName()} has been removed"
            );

            $this->assertTrue(
                $key->compare($source->findForeignKey($key->getColumn())),
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

            return "Table '{$table}' not synced, column(s) '" . join("', '",
                    $names) . "' have been changed.";
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

class TestLogger implements LoggerInterface
{
    use LoggerTrait;

    private $display;

    private $countWrites;
    private $countReads;

    public function __construct()
    {
        $this->countWrites = 0;
        $this->countReads = 0;
    }

    public function countWriteQueries(): int
    {
        return $this->countWrites;
    }

    public function countReadQueries(): int
    {
        return $this->countReads;
    }

    public function log($level, $message, array $context = [])
    {
        if (!empty($context['query'])) {
            $sql = strtolower($context['query']);
            if (
                strpos($sql, 'insert') === 0
                || strpos($sql, 'update') === 0
                || strpos($sql, 'delete') === 0
            ) {
                $this->countWrites++;
            } else {
                if (!$this->isPostgresSystemQuery($sql)) {
                    $this->countReads++;
                }
            }
        }

        if (!$this->display) {
            return;
        }

        if ($level == LogLevel::ERROR) {
            echo " \n! \033[31m" . $message . "\033[0m";
        } elseif ($level == LogLevel::ALERT) {
            echo " \n! \033[35m" . $message . "\033[0m";
        } elseif (strpos($message, 'SHOW') === 0) {
            echo " \n> \033[34m" . $message . "\033[0m";
        } else {
            if ($this->isPostgresSystemQuery($message)) {
                echo " \n> \033[90m" . $message . "\033[0m";

                return;
            }

            if (strpos($message, 'SELECT') === 0) {
                echo " \n> \033[32m" . $message . "\033[0m";
            } elseif (strpos($message, 'INSERT') === 0) {
                echo " \n> \033[36m" . $message . "\033[0m";
            } else {
                echo " \n> \033[33m" . $message . "\033[0m";
            }
        }
    }

    public function display()
    {
        $this->display = true;
    }

    public function hide()
    {
        $this->display = false;
    }

    protected function isPostgresSystemQuery(string $query): bool
    {
        $query = strtolower($query);
        if (
            strpos($query, 'tc.constraint_name')
            || strpos($query, 'pg_indexes')
            || strpos($query, 'tc.constraint_name')
            || strpos($query, 'pg_constraint')
            || strpos($query, 'information_schema')
            || strpos($query, 'pg_class')
        ) {
            return true;
        }

        return false;
    }
}