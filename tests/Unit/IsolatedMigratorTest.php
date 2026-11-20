<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Unit;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Database;
use Cycle\Database\DatabaseManager;
use Cycle\Database\DatabaseProviderInterface;
use Cycle\Database\Driver\DriverInterface;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\Migrator;
use Cycle\Migrations\RepositoryInterface;
use Mockery;
use Testo\Assert;
use Testo\Test;

#[Test]
final class IsolatedMigratorTest
{
    public function testReadonlyDriversMustNotBeUsed(): void
    {
        $dbal = new DatabaseManager(new DatabaseConfig());
        $dbal->addDatabase(
            new Database(
                'foo',
                '',
                $driver = Mockery::mock(DriverInterface::class),
            ),
        );
        // Without shouldIgnoreMissing(), any other driver call fails the test.
        $driver->shouldReceive('isReadonly')->atLeast()->once()->andReturn(true);

        $repository = Mockery::mock(RepositoryInterface::class)->shouldIgnoreMissing();

        $migrator = new Migrator(
            new MigrationConfig([]),
            $dbal,
            $repository,
        );

        $migrator->isConfigured();
    }

    public function testProviderWithoutDatabaseListNeedsNoConfiguration(): void
    {
        // Without shouldIgnoreMissing(), any call to the provider fails the test.
        $dbal = Mockery::mock(DatabaseProviderInterface::class);
        $repository = Mockery::mock(RepositoryInterface::class)->shouldIgnoreMissing();
        $migrator = new Migrator(new MigrationConfig([]), $dbal, $repository);

        $migrator->configure();

        Assert::true($migrator->isConfigured());
    }
}
