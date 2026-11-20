<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Regression\Issue66;

use Cycle\Migrations\Tests\MigrationsTestCase;
use Cycle\Migrations\Migration;
use Cycle\Migrations\State;

/**
 * The Migrator must correctly resolve the state of executed migrations
 * when the driver is configured with the `withDatetimeMicroseconds` option.
 *
 * @see https://github.com/cycle/migrations/issues/66
 */
final class DatetimeMicrosecondsTest extends MigrationsTestCase
{
    protected function driverConfig(): array
    {
        return ['options' => ['withDatetimeMicroseconds' => true]];
    }

    #[\Testo\Test]
    public function testMigrationResolvedAsExecutedAfterRun(): void
    {
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $this->atomize('migration1', [$schema]);

        $migration = $this->migrator->run();

        \Testo\Assert::instanceOf($migration, Migration::class);
        \Testo\Assert::same($migration->getState()->getStatus(), State::STATUS_EXECUTED);
    }

    #[\Testo\Test]
    public function testSecondRunHasNothingToExecute(): void
    {
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();

        // The only migration has been executed, nothing is pending
        \Testo\Assert::null($this->migrator->run());
    }

    #[\Testo\Test]
    public function testUpgradeFromLegacyTableStructure(): void
    {
        // The migration table as it was created by previous versions of the package:
        // datetime columns without precision
        $schema = $this->db->table('migrations')->getSchema();
        $schema->primary('id');
        $schema->string('migration', 191)->nullable(false);
        $schema->datetime('time_executed')->datetime();
        $schema->datetime('created_at')->datetime();
        $schema->index(['migration', 'created_at'])->unique(true);
        $schema->save();

        $this->migrator->configure();
        \Testo\Assert::true($this->migrator->isConfigured());

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $this->atomize('migration1', [$schema]);

        $migration = $this->migrator->run();

        \Testo\Assert::instanceOf($migration, Migration::class);
        \Testo\Assert::same($migration->getState()->getStatus(), State::STATUS_EXECUTED);
        \Testo\Assert::null($this->migrator->run());
    }

    #[\Testo\Test]
    public function testRollbackAfterRun(): void
    {
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        \Testo\Assert::true($this->db->hasTable('sample'));

        $migration = $this->migrator->rollback();

        \Testo\Assert::instanceOf($migration, Migration::class);
        \Testo\Assert::false($this->db->hasTable('sample'));
    }
}
