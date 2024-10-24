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

final class IsolatedMigratorTest extends TestCase
{
    public function testReadonlyDriversMustNotBeUsed()
    {
        $dbal = new DatabaseManager(new DatabaseConfig());
        $dbal->addDatabase(
            new Database(
                'foo',
                '',
                $driver = $this->createMock(DriverInterface::class),
            ),
        );
        $driver->expects($this->atLeastOnce())->method('isReadonly')->willReturn(true);
        $driver
            ->expects($this->any())
            ->method($this->callback(fn($name) => $name !== 'isReadonly'))
            ->willThrowException(new \RuntimeException('Unexpected method call'));

        $repository = $this->createMock(RepositoryInterface::class);

        $migrator = new Migrator(
            new MigrationConfig([]),
            $dbal,
            $repository,
        );

        $migrator->isConfigured();
    }
}
