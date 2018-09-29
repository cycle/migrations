<?php
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
use Spiral\Migrations\Exceptions\MigrationException;

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
     * @param RepositoryInterface $repository
     * @param DatabaseManager     $dbal
     */
    public function __construct(
        MigrationConfig $config,
        RepositoryInterface $repository,
        DatabaseManager $dbal
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
            return !$db->hasTable($this->config->getTable());
        }

        return false;
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
     * @param MigrationInterface $migration
     * @param CapsuleInterface   $capsule
     * @return null|MigrationInterface
     *
     * @throws \Throwable
     */
    public function run(
        MigrationInterface $migration,
        CapsuleInterface $capsule
    ): ?MigrationInterface {
        if (!$this->isConfigured()) {
            throw new MigrationException("Unable to run migration, Migrator not configured");
        }

        $capsule = $capsule ?? new Capsule($this->dbal->database($migration->getDatabase()));

        foreach ($this->getMigrations() as $migration) {
            if ($migration->getState()->getStatus() != State::STATUS_PENDING) {
                continue;
            }

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
     * @param MigrationInterface $migration
     * @param CapsuleInterface   $capsule
     * @return null|MigrationInterface
     *
     * @throws \Throwable
     */
    public function rollback(
        MigrationInterface $migration,
        CapsuleInterface $capsule
    ): ?MigrationInterface {
        if (!$this->isConfigured()) {
            throw new MigrationException("Unable to run migration, Migrator not configured");
        }

        $capsule = $capsule ?? new Capsule($this->dbal->database($migration->getDatabase()));

        /** @var MigrationInterface $migration */
        foreach (array_reverse($this->getMigrations()) as $migration) {
            if ($migration->getState()->getStatus() != State::STATUS_EXECUTED) {
                continue;
            }

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
     * Migration table, all migration information will be stored in it.
     *
     * @param string $database
     * @return Table
     */
    protected function migrationTable(string $database): Table
    {
        return $this->dbal->database($database)->table($this->config->getTable());
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
        $data = $this->migrationTable($db)->select('id', 'time_executed')
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
}