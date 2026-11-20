<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Regression\Issue66;

use Cycle\Migrations\Migration;
use Cycle\Migrations\State;
use Cycle\Migrations\Tests\Support\MigrationsTestCase;
use Testo\Assert;
use Testo\Test;

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

    #[Test]
    public function testMigrationResolvedAsExecutedAfterRun(): void
    {
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $this->atomize('migration1', [$schema]);

        $migration = $this->migrator->run();

        Assert::instanceOf($migration, Migration::class);
        Assert::same($migration->getState()->getStatus(), State::STATUS_EXECUTED);
    }

    #[Test]
    public function testSecondRunHasNothingToExecute(): void
    {
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();

        // The only migration has been executed, nothing is pending
        Assert::null($this->migrator->run());
    }

    #[Test]
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
        Assert::true($this->migrator->isConfigured());

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $this->atomize('migration1', [$schema]);

        $migration = $this->migrator->run();

        Assert::instanceOf($migration, Migration::class);
        Assert::same($migration->getState()->getStatus(), State::STATUS_EXECUTED);
        Assert::null($this->migrator->run());
    }

    #[Test]
    public function testRollbackAfterRun(): void
    {
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        Assert::true($this->db->hasTable('sample'));

        $migration = $this->migrator->rollback();

        Assert::instanceOf($migration, Migration::class);
        Assert::false($this->db->hasTable('sample'));
    }
}
