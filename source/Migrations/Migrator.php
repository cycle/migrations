<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
namespace Spiral\Migrations;

use Spiral\Core\Container\SingletonInterface;
use Spiral\Database\DatabaseManager;
use Spiral\Database\Entities\Driver;
use Spiral\Database\Entities\Table;
use Spiral\Migrations\Configs\MigrationsConfig;
use Spiral\Migrations\Migration\Meta;

class Migrator implements SingletonInterface
{
    /**
     * @var MigrationsConfig
     */
    private $config = null;

    /**
     * @invisible
     * @var DatabaseManager
     */
    private $dbal = null;

    /**
     * @invisible
     * @var RepositoryInterface
     */
    protected $repository = null;

    /**
     * @param MigrationsConfig    $config
     * @param DatabaseManager     $dbal
     * @param RepositoryInterface $repository
     */
    public function __construct(
        MigrationsConfig $config,
        DatabaseManager $dbal,
        RepositoryInterface $repository
    ) {
        $this->config = $config;
        $this->dbal = $dbal;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function isConfigured()
    {
        return $this->stateTable()->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        if ($this->isConfigured()) {
            return;
        }

        //Migrations table is pretty simple.
        $schema = $this->stateTable()->schema();

        $schema->column('id')->primary();
        $schema->column('migration')->string(255)->index();
        $schema->column('time_executed')->datetime();

        $schema->save();
    }

    /**
     * @return RepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Get every available migration with valid meta information.
     *
     * @return Migration[]
     */
    public function getMigrations()
    {
        $result = [];
        foreach ($this->repository->getMigrations() as $migration) {
            $migration->setMeta(
                $this->resolveStatus($migration->getMeta())
            );

            $migration->setContext(new MigrationContext($this->dbal));

            $result[] = $migration;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        foreach ($this->getMigrations() as $migration) {
            if ($migration->getMeta()->getStatus() != Meta::STATUS_PENDING) {
                continue;
            }

            //Executing migration inside global transaction
            $this->execute(function () use ($migration) {
                $migration->up();
            });

            //Registering record in database
            $this->stateTable()->insert([
                'migration'     => $migration->getMeta()->getName(),
                'time_executed' => new \DateTime('now')
            ]);

            return $migration;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function rollback()
    {
        /**
         * @var MigrationInterface $migration
         */
        foreach (array_reverse($this->getMigrations()) as $migration) {
            if ($migration->getMeta()->getStatus() != Meta::STATUS_EXECUTED) {
                continue;
            }

            //Executing migration inside global transaction
            $this->execute(function () use ($migration) {
                $migration->down();
            });

            //Flushing DB record
            $this->stateTable()->delete([
                'migration' => $migration->getMeta()->getName()
            ])->run();

            return $migration;
        }

        return null;
    }

    /**
     * Migration table, all migration information will be stored in it.
     *
     * @return Table
     */
    protected function stateTable()
    {
        return $this->dbal->database(
            $this->config->getDatabase()
        )->table(
            $this->config->getTable()
        );
    }

    /**
     * Clarify migration meta with valid status and execution time
     *
     * @param Meta $meta
     * @return Meta
     */
    private function resolveStatus(Meta $meta)
    {
        //Fetch migration information from database
        $state = $this->stateTable()->select('id', 'time_executed')->where([
            'migration' => $meta->getName()
        ])->run()->fetch();

        if (empty($state['time_executed'])) {
            return $meta->withStatus(Meta::STATUS_PENDING);
        }

        return $meta->withStatus(
            Meta::STATUS_EXECUTED,
            new \DateTime(
                $state['time_executed'],
                $this->stateTable()->database()->driver()->getTimezone()
            )
        );
    }

    /**
     * Run given code under transaction open for every driver.
     *
     * @param \Closure $closure
     * @throws \Throwable
     */
    protected function execute(\Closure $closure)
    {
        $this->beginTransactions();
        try {
            call_user_func($closure);
        } catch (\Throwable $e) {
            $this->rollbackTransactions();
            throw $e;
        }

        $this->commitTransactions();
    }

    /**
     * Begin transaction for every available driver (we don't know what database migration related
     * to).
     */
    protected function beginTransactions()
    {
        foreach ($this->getDrivers() as $driver) {
            $driver->beginTransaction();
        }
    }

    /**
     * Rollback transaction for every available driver.
     */
    protected function rollbackTransactions()
    {
        foreach ($this->getDrivers() as $driver) {
            $driver->rollbackTransaction();
        }
    }

    /**
     * Commit transaction for every avialable driver.
     */
    protected function commitTransactions()
    {
        foreach ($this->getDrivers() as $driver) {
            $driver->commitTransaction();
        }
    }

    /**
     * Get all available drivers.
     *
     * @return Driver[]
     */
    protected function getDrivers()
    {
        $drivers = [];

        foreach ($this->dbal->getDatabases() as $database) {
            if (!in_array($database->driver(), $drivers, true)) {
                $drivers[] = $database->driver();
            }
        }

        return $drivers;
    }
}