<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Migrations;

use Spiral\Database\Database;
use Spiral\Database\DatabaseManager;
use Spiral\Database\Table;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\Exception\MigrationException;

final class Migrator
{
    private const DB_DATE_FORMAT = 'Y-m-d H:i:s';

    private const MIGRATION_TABLE_FIELDS_LIST = [
        'id',
        'migration',
        'time_executed',
        'created_at'
    ];

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
     * @return MigrationConfig
     */
    public function getConfig(): MigrationConfig
    {
        return $this->config;
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
            if (!$db->hasTable($this->config->getTable()) || !$this->checkMigrationTableStructure($db)) {
                return false;
            }
        }

        return !$this->isRestoreMigrationDataRequired();
    }

    /**
     * Configure all related databases with migration table.
     */
    public function configure(): void
    {
        if ($this->isConfigured()) {
            return;
        }

        foreach ($this->dbal->getDatabases() as $db) {
            $schema = $db->table($this->config->getTable())->getSchema();

            // Schema update will automatically sync all needed data
            $schema->primary('id');
            $schema->string('migration', 191)->nullable(false);
            $schema->datetime('time_executed')->datetime();
            $schema->datetime('created_at')->datetime();
            $schema->index(['migration', 'created_at'])
                ->unique(true);

            if ($schema->hasIndex(['migration'])) {
                $schema->dropIndex(['migration']);
            }

            $schema->save();
        }

        if ($this->isRestoreMigrationDataRequired()) {
            $this->restoreMigrationData();
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
     *
     * @return null|MigrationInterface
     *
     * @throws MigrationException
     */
    public function run(CapsuleInterface $capsule = null): ?MigrationInterface
    {
        if (!$this->isConfigured()) {
            throw new MigrationException('Unable to run migration, Migrator not configured');
        }

        foreach ($this->getMigrations() as $migration) {
            if ($migration->getState()->getStatus() !== State::STATUS_PENDING) {
                continue;
            }

            try {
                $capsule = $capsule ?? new Capsule($this->dbal->database($migration->getDatabase()));
                $capsule->getDatabase($migration->getDatabase())->transaction(
                    static function () use ($migration, $capsule): void {
                        $migration->withCapsule($capsule)->up();
                    }
                );

                $this->migrationTable($migration->getDatabase())->insertOne(
                    [
                        'migration' => $migration->getState()->getName(),
                        'time_executed' => new \DateTime('now'),
                        'created_at' => $this->getMigrationCreatedAtForDb($migration),
                    ]
                );

                return $migration->withState($this->resolveState($migration));
            } catch (\Throwable $exception) {
                throw new MigrationException(
                    \sprintf(
                        'Error in the migration (%s) occurred: %s',
                        \sprintf(
                            '%s (%s)',
                            $migration->getState()->getName(),
                            $migration->getState()->getTimeCreated()->format(self::DB_DATE_FORMAT)
                        ),
                        $exception->getMessage()
                    ),
                    $exception->getCode(),
                    $exception
                );
            }
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
            throw new MigrationException('Unable to run migration, Migrator not configured');
        }

        /** @var MigrationInterface $migration */
        foreach (array_reverse($this->getMigrations()) as $migration) {
            if ($migration->getState()->getStatus() !== State::STATUS_EXECUTED) {
                continue;
            }

            $capsule = $capsule ?? new Capsule($this->dbal->database($migration->getDatabase()));
            $capsule->getDatabase()->transaction(
                static function () use ($migration, $capsule): void {
                    $migration->withCapsule($capsule)->down();
                }
            );

            $migrationData = $this->fetchMigrationData($migration);

            if (!empty($migrationData)) {
                $this->migrationTable($migration->getDatabase())
                    ->delete(['id' => $migrationData['id']])
                    ->run();
            }

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

        $data = $this->fetchMigrationData($migration);

        if (empty($data['time_executed'])) {
            return $migration->getState()->withStatus(State::STATUS_PENDING);
        }

        return $migration->getState()->withStatus(
            State::STATUS_EXECUTED,
            new \DateTimeImmutable($data['time_executed'], $db->getDriver()->getTimezone())
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

    protected function checkMigrationTableStructure(Database $db): bool
    {
        $table = $db->table($this->config->getTable());

        foreach (self::MIGRATION_TABLE_FIELDS_LIST as $field) {
            if (!$table->hasColumn($field)) {
                return false;
            }
        }

        if (!$table->hasIndex(['migration', 'created_at'])) {
            return false;
        }

        return true;
    }

    /**
     * Fetch migration information from database
     *
     * @param MigrationInterface $migration
     *
     * @return array|null
     */
    protected function fetchMigrationData(MigrationInterface $migration): ?array
    {
        $migrationData = $this->migrationTable($migration->getDatabase())
            ->select('id', 'time_executed', 'created_at')
            ->where(
                [
                    'migration' => $migration->getState()->getName(),
                    'created_at' => $this->getMigrationCreatedAtForDb($migration)->format(self::DB_DATE_FORMAT),
                ]
            )
            ->run()
            ->fetch();

        return is_array($migrationData) ? $migrationData : [];
    }

    protected function restoreMigrationData(): void
    {
        foreach ($this->repository->getMigrations() as $migration) {
            $migrationData = $this->migrationTable($migration->getDatabase())
                ->select('id')
                ->where(
                    [
                        'migration' => $migration->getState()->getName(),
                        'created_at' => null,
                    ]
                )
                ->run()
                ->fetch();

            if (!empty($migrationData)) {
                $this->migrationTable($migration->getDatabase())
                    ->update(
                        ['created_at' => $this->getMigrationCreatedAtForDb($migration)],
                        ['id' => $migrationData['id']]
                    )
                    ->run();
            }
        }
    }

    /**
     * Check if some data modification required
     *
     * @return bool
     */
    protected function isRestoreMigrationDataRequired(): bool
    {
        foreach ($this->dbal->getDatabases() as $db) {
            $table = $db->table($this->config->getTable());

            if (
                $table->select('id')
                    ->where(['created_at' => null])
                    ->count() > 0
            ) {
                return true;
            }
        }

        return false;
    }

    protected function getMigrationCreatedAtForDb(MigrationInterface $migration): \DateTimeInterface
    {
        $db = $this->dbal->database($migration->getDatabase());

        return \DateTimeImmutable::createFromFormat(
            self::DB_DATE_FORMAT,
            $migration->getState()->getTimeCreated()->format(self::DB_DATE_FORMAT),
            $db->getDriver()->getTimezone()
        );
    }
}
