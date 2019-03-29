<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations;

use Spiral\Database\DatabaseManager;
use Spiral\Database\Table;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\Exception\MigrationException;

class Migrator
{
    /** @var MigrationConfig */
    private $config;

    /** @var DatabaseManager */
    private $dbal;

    /** @var RepositoryInterface */
    private $repository;

    /**
     * @param MigrationConfig     $config
     * @param DatabaseManager     $dbal
     * @param RepositoryInterface $repository
     */
    public function __construct(
        MigrationConfig $config,
        DatabaseManager $dbal,
        RepositoryInterface $repository
    ) {
        $this->config = $config;
        $this->repository = $repository;
        $this->dbal = $dbal;
    }

    /**
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    /**
     * Check if all related databases are configures with migrations.
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        foreach ($this->dbal->getDatabases() as $db) {
            if (!$db->hasTable($this->config->getTable())) {
                return false;
            }
        }

        return true;
    }

    /**
     * Configure all related databases with migration table.
     */
    public function configure()
    {
        if ($this->isConfigured()) {
            return;
        }

        foreach ($this->dbal->getDatabases() as $db) {
            $schema = $db->table($this->config->getTable())->getSchema();

            // Schema update will automatically sync all needed data
            $schema->primary('id');
            $schema->string('migration', 255)->nullable(false);
            $schema->datetime('time_executed')->datetime();
            $schema->index(['migration']);

            $schema->save();
        }
    }

    /**
     * Get every available migration with valid meta information.
     *
     * @return MigrationInterface[]
     */
    public function getMigrations(): array
    {
        $result = [];
        foreach ($this->repository->getMigrations() as $migration) {
            //Populating migration state and execution time (if any)
            $result[] = $migration->withState($this->resolveState($migration));
        }

        return $result;
    }

    /**
     * Execute one migration and return it's instance.
     *
     * @param CapsuleInterface $capsule
     * @return null|MigrationInterface
     *
     * @throws \Throwable
     */
    public function run(CapsuleInterface $capsule = null): ?MigrationInterface
    {
        if (!$this->isConfigured()) {
            throw new MigrationException("Unable to run migration, Migrator not configured");
        }

        foreach ($this->getMigrations() as $migration) {
            if ($migration->getState()->getStatus() != State::STATUS_PENDING) {
                continue;
            }

            $capsule = $capsule ?? new Capsule($this->dbal->database($migration->getDatabase()));
            $capsule->getDatabase()->transaction(function () use ($migration, $capsule) {
                $migration->withCapsule($capsule)->up();
            });

            $this->migrationTable($migration->getDatabase())->insertOne([
                'migration'     => $migration->getState()->getName(),
                'time_executed' => new \DateTime('now')
            ]);

            return $migration->withState($this->resolveState($migration));
        }

        return null;
    }

    /**
     * Rollback last migration and return it's instance.
     *
     * @param CapsuleInterface $capsule
     * @return null|MigrationInterface
     *
     * @throws \Throwable
     */
    public function rollback(CapsuleInterface $capsule = null): ?MigrationInterface
    {
        if (!$this->isConfigured()) {
            throw new MigrationException("Unable to run migration, Migrator not configured");
        }

        /** @var MigrationInterface $migration */
        foreach (array_reverse($this->getMigrations()) as $migration) {
            if ($migration->getState()->getStatus() != State::STATUS_EXECUTED) {
                continue;
            }

            $capsule = $capsule ?? new Capsule($this->dbal->database($migration->getDatabase()));
            $capsule->getDatabase()->transaction(function () use ($migration, $capsule) {
                $migration->withCapsule($capsule)->down();
            });

            $this->migrationTable($migration->getDatabase())->delete([
                'migration' => $migration->getState()->getName()
            ])->run();

            return $migration->withState($this->resolveState($migration));
        }

        return null;
    }

    /**
     * Clarify migration state with valid status and execution time
     *
     * @param MigrationInterface $migration
     * @return State
     */
    protected function resolveState(MigrationInterface $migration): State
    {
        $db = $this->dbal->database($migration->getDatabase());

        //Fetch migration information from database
        $data = $this->migrationTable($migration->getDatabase())
            ->select('id', 'time_executed')
            ->where(['migration' => $migration->getState()->getName()])
            ->run()->fetch();

        if (empty($data['time_executed'])) {
            return $migration->getState()->withStatus(State::STATUS_PENDING);
        }

        return $migration->getState()->withStatus(
            State::STATUS_EXECUTED,
            new \DateTime($data['time_executed'], $db->getDriver()->getTimezone())
        );
    }

    /**
     * Migration table, all migration information will be stored in it.
     *
     * @param string|null $database
     * @return Table
     */
    protected function migrationTable(string $database = null): Table
    {
        return $this->dbal->database($database)->table($this->config->getTable());
    }
}