<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations;

use Cycle\Database\Database;
use Cycle\Database\DatabaseInterface;
use Cycle\Migrations\Exception\MigrationException;

/**
 * Simple migration class with shortcut for database and blueprint instances.
 */
abstract class Migration implements MigrationInterface
{
    // Target migration database
    protected const DATABASE = null;

    private ?State $state = null;
    private CapsuleInterface $capsule;

    /**
     * {@inheritdoc}
     */
    public function getDatabase(): ?string
    {
        return static::DATABASE;
    }

    /**
     * {@inheritdoc}
     */
    public function withCapsule(CapsuleInterface $capsule): MigrationInterface
    {
        $migration = clone $this;
        $migration->capsule = $capsule;

        return $migration;
    }

    /**
     * {@inheritdoc}
     */
    public function withState(State $state): MigrationInterface
    {
        $migration = clone $this;
        $migration->state = $state;

        return $migration;
    }

    /**
     * {@inheritdoc}
     */
    public function getState(): State
    {
        if (empty($this->state)) {
            throw new MigrationException('Unable to get migration state, no state are set');
        }

        return $this->state;
    }

    /**
     * @return Database
     */
    protected function database(): DatabaseInterface
    {
        if (empty($this->capsule)) {
            throw new MigrationException('Unable to get database, no capsule are set');
        }

        return $this->capsule->getDatabase();
    }

    /**
     * Get table schema builder (blueprint).
     */
    protected function table(string $table): TableBlueprint
    {
        if (empty($this->capsule)) {
            throw new MigrationException('Unable to get table blueprint, no capsule are set');
        }

        return new TableBlueprint($this->capsule, $table);
    }
}
