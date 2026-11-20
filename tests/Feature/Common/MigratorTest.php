<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Feature\Common;

use Cycle\Migrations\Capsule;
use Cycle\Migrations\Exception\MigrationException;
use Cycle\Migrations\Fixtures\TestMigration;
use Cycle\Migrations\State;
use Cycle\Migrations\Tests\Support\MigrationsTestCase;
use Testo\Assert;
use Testo\Expect;
use Testo\Test;

final class MigratorTest extends MigrationsTestCase
{
    #[Test]
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

        Assert::same($classes, [
            'A3Vendor', 'A1Vendor', 'A1', 'A2Vendor', 'A2', 'A3', 'A4Vendor', 'A4',
            'B1Vendor', 'B1', 'B2Vendor', 'B2', 'B3', 'B3Vendor',
            'C', 'CVendor',
        ]);
    }

    #[Test]
    public function testIsConfigured(): void
    {
        Assert::false($this->migrator->isConfigured());

        $this->migrator->configure();
        Assert::true($this->migrator->isConfigured());
    }

    #[Test]
    public function testConfigure(): void
    {
        Assert::false($this->migrator->isConfigured());

        $this->migrator->configure();
        Assert::true($this->db->hasTable('migrations'));
    }

    //no errors expected
    #[Test]
    public function testConfigureTwice(): void
    {
        Assert::false($this->migrator->isConfigured());

        $this->migrator->configure();
        Assert::true($this->db->hasTable('migrations'));

        $this->migrator->configure();
    }

    #[Test]
    public function testConfiguredTableStructure(): void
    {
        $this->migrator->configure();
        $table = $this->db->table('migrations');

        Assert::true($table->hasColumn('id'));
        Assert::true($table->hasColumn('migration'));
        Assert::true($table->hasColumn('time_executed'));
        Assert::true($table->hasColumn('created_at'));

        Assert::false($table->hasIndex(['migration']));
        Assert::true($table->hasIndex(['migration', 'created_at']));
    }

    #[Test]
    public function testGetEmptyMigrations(): void
    {
        $this->migrator->configure();
        Assert::same($this->migrator->getMigrations(), []);
    }

    #[Test]
    public function testRepository(): void
    {
        Assert::same($this->migrator->getRepository(), $this->repository);
    }

    #[Test]
    public function testConfig(): void
    {
        Assert::same($this->migrator->getConfig(), $this->migrationConfig);
    }

    #[Test]
    public function testRunUnconfigured(): void
    {
        Expect::exception(MigrationException::class)->withMessageContaining('Unable to run migration, Migrator not configured');

        $this->migrator->run();
    }

    #[Test]
    public function testRollbackUnconfigured(): void
    {
        Expect::exception(MigrationException::class)->withMessageContaining('Unable to run migration, Migrator not configured');

        $this->migrator->rollback();
    }

    #[Test]
    public function testCapsule(): void
    {
        $capsule = new Capsule($this->db);

        $s = $this->schema('test');
        $s->primary('id');
        $s->save();

        Assert::true($capsule->getTable('test')->exists());
    }

    #[Test]
    public function testCapsuleException(): void
    {
        Expect::exception(\Cycle\Migrations\Exception\CapsuleException::class);
        $capsule = new Capsule($this->db);

        $capsule->execute([
            $this,
        ]);
    }

    #[Test]
    public function testNoState(): void
    {
        Expect::exception(MigrationException::class)->withMessageContaining('Unable to get migration state, no state are set');

        $x = new TestMigration();
        $x->up();
    }

    #[Test]
    public function testNoCapsule(): void
    {
        Expect::exception(MigrationException::class)->withMessageContaining('Unable to get table blueprint, no capsule are set');

        $x = new TestMigration();
        $x->getTable();
    }

    #[Test]
    public function testNoCapsule2(): void
    {
        Expect::exception(MigrationException::class)->withMessageContaining('Unable to get database, no capsule are set');

        $x = new TestMigration();
        $x->down();
    }

    #[Test]
    public function testDatabase(): void
    {
        $x = new TestMigration();
        Assert::same($x->withCapsule(new Capsule($this->db))->down(), $this->db);
    }

    #[Test]
    public function testState(): void
    {
        $x = new TestMigration();

        $s = new State('name', new \DateTime());
        Assert::same($x->withState($s)->up(), $s);
    }
}
