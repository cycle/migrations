<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Feature\Common;

use Cycle\Migrations\Tests\MigrationsTestCase;
use Cycle\Migrations\Fixtures\TestMigration;
use CycleMigrationsTestsixturestestmigration;
use Cycle\Migrations\Capsule;
use Cycle\Migrations\Exception\MigrationException;
use Cycle\Migrations\State;

final class MigratorTest extends MigrationsTestCase
{
    #[\Testo\Test]
    public function testSortingOrder(): void
    {
        $files = [
            '20200909.024119_333_333_migration_1.php' => 'A3',
            '20200909.030203_22_22_migration_1.php' => 'B2',
            '20200909.030203_23_23_migration_1.php' => 'B3',
            '20200909.024119_1_1_migration_1.php' => 'A1',
            '20200909.024119_22_22_migration_2.php' => 'A2',
            '20200909.024119_4444_4444_migration_2.php' => 'A4',
            '20200923.040608_0_0_migration_3.php' => 'C',
            '20200909.030203_1_1_migration_1.php' => 'B1',
        ];

        $vendorFiles = [
            '20200908.024119_333_333_migration_1.php' => 'A3',
            '20200909.030203_22_22_migration_1.php' => 'B2',
            '20200910.030203_23_23_migration_1.php' => 'B3',
            '20200909.024119_1_1_migration_1.php' => 'A1',
            '20200909.024119_22_22_migration_2.php' => 'A2',
            '20200909.024119_4444_4444_migration_2.php' => 'A4',
            '20200856.040608_0_0_migration_3.php' => 'C',
            '20200909.030203_1_1_migration_1.php' => 'B1',
        ];

        $stub = file_get_contents(self::FILES . 'migration.stub');
        foreach ($files as $name => $class) {
            file_put_contents(self::FILES . "$name", sprintf($stub, $class));
        }

        foreach ($vendorFiles as $name => $class) {
            file_put_contents(self::FILES . "vendor/$name", \sprintf($stub, $class . 'Vendor'));
        }

        $migrations = $this->repository->getMigrations();
        $classes = array_map(
            static function ($migration) {
                return $migration::class;
            },
            array_values($migrations)
        );

        \Testo\Assert::same($classes, [
            'A3Vendor', 'A1Vendor', 'A1', 'A2Vendor', 'A2', 'A3', 'A4Vendor', 'A4',
            'B1Vendor', 'B1', 'B2Vendor', 'B2', 'B3', 'B3Vendor',
            'C', 'CVendor',
        ]);
    }

    #[\Testo\Test]
    public function testIsConfigured(): void
    {
        \Testo\Assert::false($this->migrator->isConfigured());

        $this->migrator->configure();
        \Testo\Assert::true($this->migrator->isConfigured());
    }

    #[\Testo\Test]
    public function testConfigure(): void
    {
        \Testo\Assert::false($this->migrator->isConfigured());

        $this->migrator->configure();
        \Testo\Assert::true($this->db->hasTable('migrations'));
    }

    //no errors expected
    #[\Testo\Test]
    public function testConfigureTwice(): void
    {
        \Testo\Assert::false($this->migrator->isConfigured());

        $this->migrator->configure();
        \Testo\Assert::true($this->db->hasTable('migrations'));

        $this->migrator->configure();
    }

    #[\Testo\Test]
    public function testConfiguredTableStructure(): void
    {
        $this->migrator->configure();
        $table = $this->db->table('migrations');

        \Testo\Assert::true($table->hasColumn('id'));
        \Testo\Assert::true($table->hasColumn('migration'));
        \Testo\Assert::true($table->hasColumn('time_executed'));
        \Testo\Assert::true($table->hasColumn('created_at'));

        \Testo\Assert::false($table->hasIndex(['migration']));
        \Testo\Assert::true($table->hasIndex(['migration', 'created_at']));
    }

    #[\Testo\Test]
    public function testGetEmptyMigrations(): void
    {
        $this->migrator->configure();
        \Testo\Assert::same($this->migrator->getMigrations(), []);
    }

    #[\Testo\Test]
    public function testRepository(): void
    {
        \Testo\Assert::same($this->migrator->getRepository(), $this->repository);
    }

    #[\Testo\Test]
    public function testConfig(): void
    {
        \Testo\Assert::same($this->migrator->getConfig(), $this->migrationConfig);
    }

    #[\Testo\Test]
    public function testRunUnconfigured(): void
    {
        \Testo\Expect::exception(MigrationException::class)->withMessageContaining('Unable to run migration, Migrator not configured');

        $this->migrator->run();
    }

    #[\Testo\Test]
    public function testRollbackUnconfigured(): void
    {
        \Testo\Expect::exception(MigrationException::class)->withMessageContaining('Unable to run migration, Migrator not configured');

        $this->migrator->rollback();
    }

    #[\Testo\Test]
    public function testCapsule(): void
    {
        $capsule = new Capsule($this->db);

        $s = $this->schema('test');
        $s->primary('id');
        $s->save();

        \Testo\Assert::true($capsule->getTable('test')->exists());
    }

    #[\Testo\Test]
    public function testCapsuleException(): void
    {
        \Testo\Expect::exception(\Cycle\Migrations\Exception\CapsuleException::class);
        $capsule = new Capsule($this->db);

        $capsule->execute([
            $this,
        ]);
    }

    #[\Testo\Test]
    public function testNoState(): void
    {
        \Testo\Expect::exception(MigrationException::class)->withMessageContaining('Unable to get migration state, no state are set');

        $x = new TestMigration();
        $x->up();
    }

    #[\Testo\Test]
    public function testNoCapsule(): void
    {
        \Testo\Expect::exception(MigrationException::class)->withMessageContaining('Unable to get table blueprint, no capsule are set');

        $x = new TestMigration();
        $x->getTable();
    }

    #[\Testo\Test]
    public function testNoCapsule2(): void
    {
        \Testo\Expect::exception(MigrationException::class)->withMessageContaining('Unable to get database, no capsule are set');

        $x = new TestMigration();
        $x->down();
    }

    #[\Testo\Test]
    public function testDatabase(): void
    {
        $x = new TestMigration();
        \Testo\Assert::same($x->withCapsule(new Capsule($this->db))->down(), $this->db);
    }

    #[\Testo\Test]
    public function testState(): void
    {
        $x = new TestMigration();

        $s = new State('name', new \DateTime());
        \Testo\Assert::same($x->withState($s)->up(), $s);
    }
}
