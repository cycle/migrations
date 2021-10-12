<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations;

use Cycle\Database\Database;
use Cycle\Database\DatabaseManager;
use Spiral\Database\DatabaseManager as SpiralDatabaseManager;
use Cycle\Database\Table;
use Cycle\Migrations\Config\MigrationConfig;
use Spiral\Migrations\Config\MigrationConfig as SpiralMigrationConfig;
use Cycle\Migrations\Exception\MigrationException;
use Cycle\Migrations\Migration\State;
use Cycle\Migrations\Migration\Status;
use Cycle\Migrations\Migrator\MigrationsTable;
use Spiral\Migrations\RepositoryInterface as SpiralRepositoryInterface;
use Spiral\Migrations\CapsuleInterface as SpiralCapsuleInterface;

final class Migrator implements MigratorInterface
{
    private const DB_DATE_FORMAT = 'Y-m-d H:i:s';

    /** @var MigrationConfig */
    private $config;

    /** @var DatabaseManager */
    private $dbal;

    /** @var RepositoryInterface */
    private $repository;

    /**
     * @param SpiralMigrationConfig|MigrationConfig $config The signature of this
     *        argument will be changed to {@see MigrationConfig} in future release.
     * @param SpiralDatabaseManager|DatabaseManager $dbal The signature of this
     *        argument will be changed to {@see DatabaseManager} in future release.
     * @param SpiralRepositoryInterface|RepositoryInterface $repository The signature
     *        of this argument will be changed to {@see RepositoryInterface} in future release.
     */
    public function __construct(
        SpiralMigrationConfig $config,
        SpiralDatabaseManager $dbal,
        SpiralRepositoryInterface $repository
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
     * @return bool
     */
    public function isConfigured(): bool
    {
        $databases = $this->getDatabases();

        foreach ($databases as $db) {
            if (!$this->checkMigrationTableStructure($db)) {
                return false;
            }
        }

        return !$this->isRestoreMigrationDataRequired($databases);
    }

    /**
     * {@inheritDoc}
     */
    public function configure(): void
    {
        if ($this->isConfigured()) {
            return;
        }

        $databases = $this->getDatabases();

        foreach ($databases as $database) {
            $this->createMigrationTable($database);
        }

        if ($this->isRestoreMigrationDataRequired($databases)) {
            $this->restoreMigrationData();
        }
    }

    /**
     * Get all databases for which there are migrations.
     *
     * @return array<Database>
     */
    private function getDatabases(): array
    {
        $result = [];

        foreach ($this->repository->getMigrations() as $migration) {
            $database = $this->dbal->database($migration->getDatabase());

            if (! isset($result[$database->getName()])) {
                $result[$database->getName()] = $database;
            }
        }

        return $result;
    }

    /**
     * Create migration table inside given database
     *
     * @param Database $database
     */
    private function createMigrationTable(Database $database): void
    {
        $table = new MigrationsTable($database, $this->config->getTable());
        $table->actualize();
    }

    /**
     * Get every available migration with valid meta information.
     *
     * @return MigrationInterface[]
     * @throws \Exception
     */
    public function getMigrations(): array
    {
        $result = [];

        foreach ($this->repository->getMigrations() as $migration) {
            // Populating migration state and execution time (if any)
            $result[] = $migration->withState($this->resolveState($migration));
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function run(SpiralCapsuleInterface $capsule = null): ?MigrationInterface
    {
        if (!$this->isConfigured()) {
            $this->configure();
        }

        foreach ($this->getMigrations() as $migration) {
            $state = $migration->getState();

            if ($state->getStatus() !== Status::STATUS_PENDING) {
                continue;
            }

            try {
                $capsule = $capsule ?? new Capsule($this->dbal->database($migration->getDatabase()));
                $capsule->getDatabase()->transaction(
                    static function () use ($migration, $capsule): void {
                        $migration->withCapsule($capsule)->up();
                    }
                );

                $this->migrationTable($migration->getDatabase())->insertOne(
                    [
                        'migration' => $state->getName(),
                        'time_executed' => new \DateTime('now'),
                        'created_at' => $this->getMigrationCreatedAtForDb($migration),
                    ]
                );

                return $migration->withState($this->resolveState($migration));
            } catch (\Throwable $exception) {
                $state = $migration->getState();
                throw new MigrationException(
                    \sprintf(
                        'Error in the migration (%s) occurred: %s',
                        \sprintf(
                            '%s (%s)',
                            $state->getName(),
                            $state->getTimeCreated()->format(self::DB_DATE_FORMAT)
                        ),
                        $exception->getMessage()
                    ),
                    (int)$exception->getCode(),
                    $exception
                );
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function rollback(SpiralCapsuleInterface $capsule = null): ?MigrationInterface
    {
        if (!$this->isConfigured()) {
            $this->configure();
        }

        /** @var MigrationInterface $migration */
        foreach (\array_reverse($this->getMigrations()) as $migration) {
            if ($migration->getState()->getStatus() !== Status::STATUS_EXECUTED) {
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
     * @throws \Exception
     */
    private function resolveState(MigrationInterface $migration): State
    {
        $db = $this->dbal->database($migration->getDatabase());

        $data = $this->fetchMigrationData($migration);

        if (empty($data['time_executed'])) {
            return $migration->getState()->withStatus(Status::STATUS_PENDING);
        }

        return $migration->getState()->withStatus(
            Status::STATUS_EXECUTED,
            new \DateTimeImmutable($data['time_executed'], $db->getDriver()->getTimezone())
        );
    }

    /**
     * Migration table, all migration information will be stored in it.
     *
     * @param string|null $database
     *
     * @return Table
     */
    private function migrationTable(string $database = null): Table
    {
        return $this->dbal->database($database)->table($this->config->getTable());
    }

    /**
     * @param Database $db
     *
     * @return bool
     */
    private function checkMigrationTableStructure(Database $db): bool
    {
        $table = new MigrationsTable($db, $this->config->getTable());

        return $table->isPresent();
    }

    /**
     * Fetch migration information from database
     *
     * @param MigrationInterface $migration
     *
     * @return array|null
     */
    private function fetchMigrationData(MigrationInterface $migration): ?array
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

    /**
     * This method updates the state of the empty (null) "created_at" fields for
     * each entry in the migration table within the
     * issue {@link https://github.com/spiral/migrations/issues/13}.
     *
     * TODO It is worth noting that this method works in an extremely suboptimal
     *      way and requires optimizations.
     */
    private function restoreMigrationData(): void
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
     * Check if some data modification required.
     *
     * This method checks for empty (null) "created_at" fields created within
     * the issue {@link https://github.com/spiral/migrations/issues/13}.
     *
     * @param iterable<Database> $databases
     * @return bool
     */
    private function isRestoreMigrationDataRequired(iterable $databases): bool
    {
        foreach ($databases as $db) {
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

    /**
     * Creates a new date object based on the database timezone and the
     * migration creation date.
     *
     * @param MigrationInterface $migration
     * @return \DateTimeInterface
     */
    private function getMigrationCreatedAtForDb(MigrationInterface $migration): \DateTimeInterface
    {
        $db = $this->dbal->database($migration->getDatabase());

        $createdAt = $migration->getState()
            ->getTimeCreated()
            ->format(self::DB_DATE_FORMAT)
        ;

        $timezone = $db->getDriver()
            ->getTimezone()
        ;

        return \DateTimeImmutable::createFromFormat(self::DB_DATE_FORMAT, $createdAt, $timezone);
    }
}
