<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests;

use Cycle\Database\Driver\DriverInterface;
use Cycle\Migrations\Migration;
use Cycle\Migrations\State;
use Psr\Log\LoggerAwareInterface;

/**
 * The Migrator must correctly resolve the state of executed migrations
 * when the driver is configured with the `withDatetimeMicroseconds` option.
 *
 * @see https://github.com/cycle/migrations/issues/66
 */
abstract class DatetimeMicrosecondsTest extends BaseTest
{
    public function getDriver(): DriverInterface
    {
        if (!isset($this->driver)) {
            $config = clone self::$config[static::DRIVER];
            $config->options['withDatetimeMicroseconds'] = true;

            $this->driver = $config->driver::create($config);
        }

        if (self::$config['debug'] && $this->driver instanceof LoggerAwareInterface) {
            $this->driver->setLogger(new TestLogger());
        }

        return $this->driver;
    }

    public function testMigrationResolvedAsExecutedAfterRun(): void
    {
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $this->atomize('migration1', [$schema]);

        $migration = $this->migrator->run();

        $this->assertInstanceOf(Migration::class, $migration);
        $this->assertSame(State::STATUS_EXECUTED, $migration->getState()->getStatus());
    }

    public function testSecondRunHasNothingToExecute(): void
    {
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();

        // The only migration has been executed, nothing is pending
        $this->assertNull($this->migrator->run());
    }

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
        $this->assertTrue($this->migrator->isConfigured());

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $this->atomize('migration1', [$schema]);

        $migration = $this->migrator->run();

        $this->assertInstanceOf(Migration::class, $migration);
        $this->assertSame(State::STATUS_EXECUTED, $migration->getState()->getStatus());
        $this->assertNull($this->migrator->run());
    }

    public function testRollbackAfterRun(): void
    {
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        $this->assertTrue($this->db->hasTable('sample'));

        $migration = $this->migrator->rollback();

        $this->assertInstanceOf(Migration::class, $migration);
        $this->assertFalse($this->db->hasTable('sample'));
    }
}
