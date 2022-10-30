<?php

declare(strict_types=1);

namespace Cycle\Migrations\V2;

use Cycle\Database\DatabaseInterface;
use Cycle\Migrations\CapsuleInterface;
use Cycle\Migrations\Exception\MigrationException;
use Cycle\Migrations\MigrationInterface;
use Cycle\Migrations\State;

abstract class Migration implements MigrationInterface
{
    use ColumnTrait;

    // Target migration database
    protected const DATABASE = null;

    private ?State $state = null;
    private CapsuleInterface $capsule;

    public function getDatabase(): ?string
    {
        return static::DATABASE;
    }

    public function withCapsule(CapsuleInterface $capsule): MigrationInterface
    {
        $migration = clone $this;
        $migration->capsule = $capsule;

        return $migration;
    }

    public function withState(State $state): MigrationInterface
    {
        $migration = clone $this;
        $migration->state = $state;

        return $migration;
    }

    public function getState(): State
    {
        if (empty($this->state)) {
            throw new MigrationException('Unable to get migration state, no state are set');
        }

        return $this->state;
    }

    protected function database(): DatabaseInterface
    {
        if (empty($this->capsule)) {
            throw new MigrationException('Unable to get database, no capsule are set');
        }

        return $this->capsule->getDatabase();
    }

    protected function table(string $tableName): TableBlueprint
    {
        return new TableBlueprint($tableName, $this->capsule);
    }

    protected function index(array $fields, ?string $name = null): Index
    {
        return new Index($fields, $name);
    }

    protected function foreignKey(array $fields, string $table, array $outerKeys): ForeignKey
    {
        return new ForeignKey($fields, $table, $outerKeys);
    }

    protected function enum(string $name): TypeBuilder
    {
        return new TypeBuilder(
            $name,
            TypeBuilder::ENUM,
            $this->database()
        );
    }
}
