<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Unit;

use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\Database;
use Cycle\Database\DatabaseManager;
use Cycle\Database\Driver\DriverInterface;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\Migrator;
use Cycle\Migrations\RepositoryInterface;
use PHPUnit\Framework\TestCase;

final class IsolatedMigratorTest
{
    #[\Testo\Test]
    public function testReadonlyDriversMustNotBeUsed()
    {
        $dbal = new DatabaseManager(new DatabaseConfig());
        $dbal->addDatabase(
            new Database(
                'foo',
                '',
                $driver = \Mockery::mock(DriverInterface::class)->shouldIgnoreMissing(),
            ),
        );
        $driver->shouldReceive('isReadonly')->atLeast()->once()->andReturn(true);
        $driver->shouldReceive($this->callback(fn($name) => $name !== 'isReadonly'))->andThrow(new \RuntimeException('Unexpected method call'));

        $repository = \Mockery::mock(RepositoryInterface::class)->shouldIgnoreMissing();

        $migrator = new Migrator(
            new MigrationConfig([]),
            $dbal,
            $repository,
        );

        $migrator->isConfigured();
    }
}
