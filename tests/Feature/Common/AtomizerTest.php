<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Feature\Common;

use Cycle\Database\Injection\Fragment;
use Cycle\Database\Schema\AbstractTable;
use Cycle\Migrations\Atomizer\Atomizer;
use Cycle\Migrations\Atomizer\Renderer;
use Cycle\Migrations\Migration;
use Cycle\Migrations\State;
use Cycle\Migrations\Tests\MigrationsTestCase;
use Cycle\Testing\Database\Attribute\OnlyDriver;
use Cycle\Testing\Database\Driver;
use Mockery;
use Testo\Assert;
use Testo\Expect;
use Testo\Test;

final class AtomizerTest extends MigrationsTestCase
{
    #[Test]
    public function testCreateAndDiff(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $schema->index(['value']);

        $this->atomize('migration1', [$schema]);
        $migration = $this->migrator->run();

        Assert::instanceOf($migration, Migration::class);
        Assert::same($migration->getState()->getStatus(), State::STATUS_EXECUTED);
        Assert::instanceOf($migration->getState()->getTimeCreated(), \DateTimeInterface::class);
        Assert::instanceOf($migration->getState()->getTimeExecuted(), \DateTimeInterface::class);

        Assert::true($this->db->hasTable('sample'));

        $this->migrator->rollback();
        Assert::false($this->db->hasTable('sample'));
    }

    #[Test]
    public function testCreateAndThenUpdate(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $schema->index(['value']);
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        Assert::same($this->schema('sample')->column('value')->getAbstractType(), 'integer');

        $schema = $this->schema('sample');
        $schema->float('value');
        $this->atomize('migration2', [$schema]);

        $this->migrator->run();
        Assert::same($this->schema('sample')->column('value')->getAbstractType(), 'float');
        Assert::true($this->db->hasTable('sample'));

        $this->migrator->rollback();
        Assert::same($this->schema('sample')->column('value')->getAbstractType(), 'integer');
        Assert::true($this->db->hasTable('sample'));

        $this->migrator->rollback();
        Assert::false($this->db->hasTable('sample'));
    }

    #[Test]
    public function testCreateAndThenRenameColumn(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        Assert::true($this->schema('sample')->hasColumn('value'));

        $schema = $this->schema('sample');
        $schema->integer('value')->setName('value2');
        $this->atomize('migration2', [$schema]);

        $this->migrator->run();
        Assert::true($this->db->hasTable('sample'));
        Assert::true($this->schema('sample')->hasColumn('value2'));
        Assert::false($this->schema('sample')->hasColumn('value'));

        $this->migrator->rollback();
        Assert::true($this->db->hasTable('sample'));
        Assert::true($this->schema('sample')->hasColumn('value'));
        Assert::false($this->schema('sample')->hasColumn('value2'));

        $this->migrator->rollback();
        Assert::false($this->db->hasTable('sample'));
    }

    #[Test]
    public function testCreateAndThenRenameColumnWithIndex(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $schema->index(['value']);
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        Assert::true($this->schema('sample')->hasColumn('value'));

        $schema = $this->schema('sample');

        $schema->integer('value')->setName('value2');
        $this->atomize('migration2', [$schema]);

        $this->migrator->run();
        Assert::true($this->db->hasTable('sample'));
        Assert::true($this->schema('sample')->hasColumn('value2'));
        Assert::false($this->schema('sample')->hasColumn('value'));

        $this->migrator->rollback();
        Assert::true($this->db->hasTable('sample'));
        Assert::true($this->schema('sample')->hasColumn('value'));
        Assert::false($this->schema('sample')->hasColumn('value2'));

        $this->migrator->rollback();
        Assert::false($this->db->hasTable('sample'));
    }

    #[Test]
    public function testCreateAndThenUpdateAddDefault(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $schema->index(['value']);
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        Assert::same($this->schema('sample')->column('value')->getAbstractType(), 'integer');

        $schema = $this->schema('sample');
        $schema->float('value')->defaultValue(2);

        $this->atomize('migration2', [$schema]);

        $this->migrator->run();
        Assert::same($this->schema('sample')->column('value')->getAbstractType(), 'float');
        Assert::equals($this->schema('sample')->column('value')->getDefaultValue(), 2);

        Assert::true($this->db->hasTable('sample'));

        $this->migrator->rollback();
        Assert::same($this->schema('sample')->column('value')->getAbstractType(), 'integer');
        Assert::same($this->schema('sample')->column('value')->getDefaultValue(), null);

        Assert::true($this->db->hasTable('sample'));

        $this->migrator->rollback();
        Assert::false($this->db->hasTable('sample'));
    }

    #[Test]
    public function testCreateAndTThenAddIndexAndMakeUnique(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        Assert::true($this->db->hasTable('sample'));
        Assert::false($this->schema('sample')->hasIndex(['value']));

        $schema = $this->schema('sample');
        $schema->index(['value']);

        $this->atomize('migration2', [$schema]);

        $this->migrator->run();
        Assert::true($this->db->hasTable('sample'));
        Assert::true($this->schema('sample')->hasIndex(['value']));
        Assert::false($this->schema('sample')->index(['value'])->isUnique());

        $schema = $this->schema('sample');
        $schema->index(['value'])->unique(true);

        $this->atomize('migration3', [$schema]);

        $this->migrator->run();
        Assert::true($this->db->hasTable('sample'));
        Assert::true($this->schema('sample')->hasIndex(['value']));
        Assert::true($this->schema('sample')->index(['value'])->isUnique());

        $this->migrator->rollback();
        Assert::true($this->db->hasTable('sample'));
        Assert::true($this->schema('sample')->hasIndex(['value']));
        Assert::false($this->schema('sample')->index(['value'])->isUnique());

        $this->migrator->rollback();
        Assert::true($this->db->hasTable('sample'));
        Assert::false($this->schema('sample')->hasIndex(['value']));

        $this->migrator->rollback();
        Assert::false($this->db->hasTable('sample'));
    }

    #[Test]
    public function testCreateAndDropIndex(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $schema->index(['value']);
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        Assert::true($this->db->hasTable('sample'));
        Assert::true($this->schema('sample')->hasIndex(['value']));

        $schema = $this->schema('sample');
        $schema->dropIndex(['value']);

        $this->atomize('migration2', [$schema]);

        $this->migrator->run();
        Assert::true($this->db->hasTable('sample'));
        Assert::false($this->schema('sample')->hasIndex(['value']));

        $this->migrator->rollback();
        Assert::true($this->db->hasTable('sample'));
        Assert::true($this->schema('sample')->hasIndex(['value']));

        $this->migrator->rollback();
        Assert::false($this->db->hasTable('sample'));
    }

    #[Test]
    public function testCreateAndDropColumn(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $schema->index(['value']);
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        Assert::true($this->db->hasTable('sample'));
        Assert::true($this->schema('sample')->hasIndex(['value']));

        $schema = $this->schema('sample');
        $schema->dropColumn('value');
        $schema->string('name', 120);

        $this->atomize('migration2', [$schema]);

        $this->migrator->run();
        Assert::true($this->db->hasTable('sample'));
        Assert::false($this->schema('sample')->hasColumn('value'));
        Assert::true($this->schema('sample')->hasColumn('name'));

        $this->migrator->rollback();
        Assert::true($this->db->hasTable('sample'));
        Assert::true($this->schema('sample')->hasColumn('value'));
        Assert::false($this->schema('sample')->hasColumn('name'));

        $this->migrator->rollback();
        Assert::false($this->db->hasTable('sample'));
    }

    #[Test]
    public function testSetPrimaryKeys(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->integer('id')->nullable(false);
        $schema->integer('value')->nullable(false);
        $schema->setPrimaryKeys(['id', 'value']);

        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        Assert::true($this->db->hasTable('sample'));
        Assert::same($this->schema('sample')->getPrimaryKeys(), ['id', 'value']);

        $this->migrator->rollback();
        Assert::false($this->db->hasTable('sample'));
    }

    #[Test]
    public function testCreateDatetimeNowColumn(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $column = $schema->datetime('value');
        $column->defaultValue(new Fragment($column::DATETIME_NOW));

        $this->atomize('migration1', [$schema]);

        $this->migrator->run();

        Assert::true($this->db->hasTable('sample'));
        Assert::same((string)$this->schema('sample')->column('value')->getDefaultValue(), (string)$column->getDefaultValue());

        $this->migrator->rollback();
        Assert::false($this->db->hasTable('sample'));
    }

    #[Test]
    public function testChangePrimaryKeys(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->integer('id')->nullable(false);
        $schema->integer('value')->nullable(false);
        $schema->setPrimaryKeys(['id', 'value']);

        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        Assert::true($this->db->hasTable('sample'));
        Assert::same($this->schema('sample')->getPrimaryKeys(), ['id', 'value']);

        $schema = $this->schema('sample');
        $schema->dropColumn('value');
        $schema->setPrimaryKeys(['id']);

        $this->atomize('migration2', [$schema]);

        Expect::exception(\Cycle\Migrations\Exception\MigrationException::class)->withMessageMatchingRegex("/Error in the migration \([0-9a-z_\-]+ \(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\)\) occurred: "
        . "Unable to set primary keys for table \'.+\'\.\'.+\', table already exists/");

        $this->migrator->run();
    }

    #[Test]
    public function testCreateAndThenUpdateEnumDefault(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->enum('value', ['a', 'b'])->defaultValue('a');
        $schema->index(['value']);
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        Assert::same($this->schema('sample')->column('value')->getEnumValues(), ['a', 'b']);

        $schema = $this->schema('sample');
        $schema->enum('value', ['a', 'b', 'c']);
        $schema->index(['value'])->unique(true);
        $this->atomize('migration2', [$schema]);

        $this->migrator->run();
        Assert::same($this->schema('sample')->column('value')->getEnumValues(), ['a', 'b', 'c']);

        Assert::true($this->db->hasTable('sample'));

        $this->migrator->rollback();
        Assert::same($this->schema('sample')->column('value')->getEnumValues(), ['a', 'b']);

        Assert::true($this->db->hasTable('sample'));

        $this->migrator->rollback();
        Assert::false($this->db->hasTable('sample'));
    }

    #[Test]
    public function testChangeColumnScale(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->decimal('value', 2, 1);
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        Assert::same($this->schema('sample')->column('value')->getPrecision(), 2);
        Assert::same($this->schema('sample')->column('value')->getScale(), 1);

        $schema = $this->schema('sample');
        $schema->decimal('value', 3, 2);
        $this->atomize('migration2', [$schema]);

        $this->migrator->run();

        Assert::same($this->schema('sample')->column('value')->getPrecision(), 3);
        Assert::same($this->schema('sample')->column('value')->getScale(), 2);

        Assert::true($this->db->hasTable('sample'));

        $this->migrator->rollback();
        Assert::same($this->schema('sample')->column('value')->getPrecision(), 2);
        Assert::same($this->schema('sample')->column('value')->getScale(), 1);

        Assert::true($this->db->hasTable('sample'));

        $this->migrator->rollback();
        Assert::false($this->db->hasTable('sample'));
    }

    #[Test]
    public function testSetTables(): void
    {
        $atomizer = new Atomizer(new Renderer());

        $table1 = Mockery::mock(AbstractTable::class)->shouldIgnoreMissing();
        $table2 = Mockery::mock(AbstractTable::class)->shouldIgnoreMissing();
        $table3 = Mockery::mock(AbstractTable::class)->shouldIgnoreMissing();

        $atomizer->addTable($table1);
        Assert::same($atomizer->getTables(), [$table1]);

        $atomizer->setTables([$table2, $table3]);
        Assert::same($atomizer->getTables(), [$table2, $table3]);
    }

    #[Test]
    #[OnlyDriver(Driver::MySQL, Driver::SQLServer)]
    public function testChangeBinaryColumnSize(): void
    {
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->binary('value', size: 16);
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        Assert::same($this->schema('sample')->column('value')->getSize(), 16);

        $schema = $this->schema('sample');
        $schema->binary('value', size: 255);
        $this->atomize('migration2', [$schema]);

        $this->migrator->run();

        Assert::same($this->schema('sample')->column('value')->getSize(), 255);

        Assert::true($this->db->hasTable('sample'));

        $this->migrator->rollback();
        Assert::same($this->schema('sample')->column('value')->getSize(), 16);

        Assert::true($this->db->hasTable('sample'));

        $this->migrator->rollback();
        Assert::false($this->db->hasTable('sample'));
    }
}
