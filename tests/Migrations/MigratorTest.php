<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Migrations\Tests;

use Spiral\Migrations\Capsule;
use Spiral\Migrations\Exception\MigrationException;
use Spiral\Migrations\State;

abstract class MigratorTest extends BaseTest
{
    public function testSortingOrder(): void
    {
        $files = [
            '20200909.024119_333_333_migration_1.php'   => 'A3',
            '20200909.030203_22_22_migration_1.php'     => 'B2',
            '20200909.030203_23_23_migration_1.php'     => 'B3',
            '20200909.024119_1_1_migration_1.php'       => 'A1',
            '20200909.024119_22_22_migration_2.php'     => 'A2',
            '20200909.024119_4444_4444_migration_2.php' => 'A4',
            '20200923.040608_0_0_migration_3.php'       => 'C',
            '20200909.030203_1_1_migration_1.php'       => 'B1',
        ];
        $stub = file_get_contents(__DIR__ . '/../files/migration.stub');
        foreach ($files as $name => $class) {
            file_put_contents(__DIR__ . "/../files/$name", sprintf($stub, $class));
        }

        $migrations = $this->repository->getMigrations();
        $classes = array_map(
            static function ($migration) {
                return get_class($migration);
            },
            array_values($migrations)
        );

        $this->assertSame(['A1', 'A2', 'A3', 'A4', 'B1', 'B2', 'B3', 'C'], $classes);
    }

    public function testIsConfigured(): void
    {
        $this->assertFalse($this->migrator->isConfigured());

        $this->migrator->configure();
        $this->assertTrue($this->migrator->isConfigured());
    }

    public function testConfigure(): void
    {
        $this->assertFalse($this->migrator->isConfigured());

        $this->migrator->configure();
        $this->assertTrue($this->db->hasTable('migrations'));
    }

    //no errors expected
    public function testConfigureTwice(): void
    {
        $this->assertFalse($this->migrator->isConfigured());

        $this->migrator->configure();
        $this->assertTrue($this->db->hasTable('migrations'));

        $this->migrator->configure();
    }

    public function testConfiguredTableStructure(): void
    {
        $this->migrator->configure();
        $table = $this->db->table('migrations');

        $this->assertTrue($table->hasColumn('id'));
        $this->assertTrue($table->hasColumn('migration'));
        $this->assertTrue($table->hasColumn('time_executed'));
        $this->assertTrue($table->hasColumn('created_at'));

        $this->assertFalse($table->hasIndex(['migration']));
        $this->assertTrue($table->hasIndex(['migration', 'created_at']));
    }

    public function testGetEmptyMigrations(): void
    {
        $this->migrator->configure();
        $this->assertSame([], $this->migrator->getMigrations());
    }

    public function testRepository(): void
    {
        $this->assertSame($this->repository, $this->migrator->getRepository());
    }

    public function testConfig(): void
    {
        $this->assertSame($this->migrationConfig, $this->migrator->getConfig());
    }

    public function testRunUnconfigured(): void
    {
        $this->expectException(MigrationException::class);
        $this->expectExceptionMessage("Unable to run migration, Migrator not configured");

        $this->migrator->run();
    }

    public function testRollbackUnconfigured(): void
    {
        $this->expectException(MigrationException::class);
        $this->expectExceptionMessage("Unable to run migration, Migrator not configured");

        $this->migrator->rollback();
    }

    public function testCapsule(): void
    {
        $capsule = new Capsule($this->db);

        $s = $this->schema('test');
        $s->primary('id');
        $s->save();

        $this->assertTrue($capsule->getTable('test')->exists());
    }

    public function testCapsuleException(): void
    {
        $this->expectException(\Spiral\Migrations\Exception\CapsuleException::class);
        $capsule = new Capsule($this->db);

        $capsule->execute([
            $this
        ]);
    }

    public function testNoState(): void
    {
        $this->expectException(MigrationException::class);
        $this->expectExceptionMessage("Unable to get migration state, no state are set");

        $x = new TestMigration();
        $x->up();
    }

    public function testNoCapsule(): void
    {
        $this->expectException(MigrationException::class);
        $this->expectExceptionMessage("Unable to get table blueprint, no capsule are set");

        $x = new TestMigration();
        $x->getTable();
    }

    public function testNoCapsule2(): void
    {
        $this->expectException(MigrationException::class);
        $this->expectExceptionMessage("Unable to get database, no capsule are set");

        $x = new TestMigration();
        $x->down();
    }

    public function testDatabase(): void
    {
        $x = new TestMigration();
        $this->assertSame($this->db, $x->withCapsule(new Capsule($this->db))->down());
    }

    public function testState(): void
    {
        $x = new TestMigration();

        $s = new State('name', new \DateTime());
        $this->assertSame($s, $x->withState($s)->up());
    }
}
