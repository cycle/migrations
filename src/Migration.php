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
use Cycle\Database\DatabaseInterface;
use Cycle\Migrations\Exception\MigrationException;
use Cycle\Migrations\Migration\DefinitionInterface;
use Cycle\Migrations\Migration\ProvidesSyncStateInterface;
use Cycle\Migrations\Migration\State;
use Spiral\Migrations\Migration\State as SpiralState;
use Spiral\Migrations\CapsuleInterface as SpiralCapsuleInterface;

/**
 * Simple migration class with shortcut for database and blueprint instances.
 */
abstract class Migration implements MigrationInterface
{
    // Target migration database
    protected const DATABASE = null;

    /** @var State|null */
    private $state;

    /** @var CapsuleInterface */
    private $capsule;

    /**
     * {@inheritDoc}
     */
    public function getDatabase(): ?string
    {
        return static::DATABASE;
    }

    /**
     * {@inheritDoc}
     */
    public function withCapsule(SpiralCapsuleInterface $capsule): DefinitionInterface
    {
        $migration = clone $this;
        $migration->capsule = $capsule;

        return $migration;
    }

    /**
     * {@inheritDoc}
     */
    public function withState(SpiralState $state): ProvidesSyncStateInterface
    {
        $migration = clone $this;
        $migration->state = $state;

        return $migration;
    }

    /**
     * {@inheritDoc}
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
     *
     * @param string $table
     *
     * @return TableBlueprint
     */
    protected function table(string $table): TableBlueprint
    {
        if (empty($this->capsule)) {
            throw new MigrationException('Unable to get table blueprint, no capsule are set');
        }

        return new TableBlueprint($this->capsule, $table);
    }
}
