<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Migrations\Tests;

use Spiral\Migrations\Migration;
use Spiral\Migrations\State;

abstract class AtomizerTest extends BaseTest
{
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

        $this->assertInstanceOf(Migration::class, $migration);
        $this->assertSame(State::STATUS_EXECUTED, $migration->getState()->getStatus());
        $this->assertInstanceOf(\DateTimeInterface::class, $migration->getState()->getTimeCreated());
        $this->assertInstanceOf(\DateTimeInterface::class, $migration->getState()->getTimeExecuted());

        $this->assertTrue($this->db->hasTable('sample'));

        $this->migrator->rollback();
        $this->assertFalse($this->db->hasTable('sample'));
    }

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
        $this->assertSame('integer', $this->schema('sample')->column('value')->getAbstractType());

        $schema = $this->schema('sample');
        $schema->float('value');
        $this->atomize('migration2', [$schema]);

        $this->migrator->run();
        $this->assertSame('float', $this->schema('sample')->column('value')->getAbstractType());
        $this->assertTrue($this->db->hasTable('sample'));

        $this->migrator->rollback();
        $this->assertSame('integer', $this->schema('sample')->column('value')->getAbstractType());
        $this->assertTrue($this->db->hasTable('sample'));

        $this->migrator->rollback();
        $this->assertFalse($this->db->hasTable('sample'));
    }

    public function testCreateAndThenRenameColumn(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        $this->assertTrue($this->schema('sample')->hasColumn('value'));

        $schema = $this->schema('sample');
        $schema->integer('value')->setName('value2');
        $this->atomize('migration2', [$schema]);

        $this->migrator->run();
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertTrue($this->schema('sample')->hasColumn('value2'));
        $this->assertFalse($this->schema('sample')->hasColumn('value'));

        $this->migrator->rollback();
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertTrue($this->schema('sample')->hasColumn('value'));
        $this->assertFalse($this->schema('sample')->hasColumn('value2'));

        $this->migrator->rollback();
        $this->assertFalse($this->db->hasTable('sample'));
    }

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
        $this->assertTrue($this->schema('sample')->hasColumn('value'));

        $schema = $this->schema('sample');

        $schema->integer('value')->setName('value2');
        $this->atomize('migration2', [$schema]);

        $this->migrator->run();
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertTrue($this->schema('sample')->hasColumn('value2'));
        $this->assertFalse($this->schema('sample')->hasColumn('value'));

        $this->migrator->rollback();
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertTrue($this->schema('sample')->hasColumn('value'));
        $this->assertFalse($this->schema('sample')->hasColumn('value2'));

        $this->migrator->rollback();
        $this->assertFalse($this->db->hasTable('sample'));
    }

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
        $this->assertSame('integer', $this->schema('sample')->column('value')->getAbstractType());

        $schema = $this->schema('sample');
        $schema->float('value')->defaultValue(2);

        $this->atomize('migration2', [$schema]);

        $this->migrator->run();
        $this->assertSame('float', $this->schema('sample')->column('value')->getAbstractType());
        $this->assertEquals(2, $this->schema('sample')->column('value')->getDefaultValue());

        $this->assertTrue($this->db->hasTable('sample'));

        $this->migrator->rollback();
        $this->assertSame('integer', $this->schema('sample')->column('value')->getAbstractType());
        $this->assertSame(null, $this->schema('sample')->column('value')->getDefaultValue());

        $this->assertTrue($this->db->hasTable('sample'));

        $this->migrator->rollback();
        $this->assertFalse($this->db->hasTable('sample'));
    }

    public function testCreateAndTThenAddIndexAndMakeUnique(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertFalse($this->schema('sample')->hasIndex(['value']));

        $schema = $this->schema('sample');
        $schema->index(['value']);

        $this->atomize('migration2', [$schema]);

        $this->migrator->run();
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertTrue($this->schema('sample')->hasIndex(['value']));
        $this->assertFalse($this->schema('sample')->index(['value'])->isUnique());

        $schema = $this->schema('sample');
        $schema->index(['value'])->unique(true);

        $this->atomize('migration3', [$schema]);

        $this->migrator->run();
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertTrue($this->schema('sample')->hasIndex(['value']));
        $this->assertTrue($this->schema('sample')->index(['value'])->isUnique());

        $this->migrator->rollback();
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertTrue($this->schema('sample')->hasIndex(['value']));
        $this->assertFalse($this->schema('sample')->index(['value'])->isUnique());

        $this->migrator->rollback();
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertFalse($this->schema('sample')->hasIndex(['value']));

        $this->migrator->rollback();
        $this->assertFalse($this->db->hasTable('sample'));
    }

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
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertTrue($this->schema('sample')->hasIndex(['value']));

        $schema = $this->schema('sample');
        $schema->dropIndex(['value']);

        $this->atomize('migration2', [$schema]);

        $this->migrator->run();
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertFalse($this->schema('sample')->hasIndex(['value']));

        $this->migrator->rollback();
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertTrue($this->schema('sample')->hasIndex(['value']));

        $this->migrator->rollback();
        $this->assertFalse($this->db->hasTable('sample'));
    }

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
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertTrue($this->schema('sample')->hasIndex(['value']));

        $schema = $this->schema('sample');
        $schema->dropColumn('value');
        $schema->string('name', 120);

        $this->atomize('migration2', [$schema]);

        $this->migrator->run();
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertFalse($this->schema('sample')->hasColumn('value'));
        $this->assertTrue($this->schema('sample')->hasColumn('name'));

        $this->migrator->rollback();
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertTrue($this->schema('sample')->hasColumn('value'));
        $this->assertFalse($this->schema('sample')->hasColumn('name'));

        $this->migrator->rollback();
        $this->assertFalse($this->db->hasTable('sample'));
    }

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
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertSame(['id', 'value'], $this->schema('sample')->getPrimaryKeys());

        $this->migrator->rollback();
        $this->assertFalse($this->db->hasTable('sample'));
    }

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
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertSame(['id', 'value'], $this->schema('sample')->getPrimaryKeys());

        $schema = $this->schema('sample');
        $schema->dropColumn('value');
        $schema->setPrimaryKeys(['id']);

        $this->atomize('migration2', [$schema]);

        $this->expectException(\Spiral\Migrations\Exception\MigrationException::class);
        $this->expectExceptionMessageMatches(
            "/Error in the migration \([0-9a-z_\-]+ \(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\)\) occurred: "
            . "Unable to set primary keys for table \'.+\'\.\'.+\', table already exists/"
        );

        $this->migrator->run();
    }


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
        $this->assertSame(['a', 'b'], $this->schema('sample')->column('value')->getEnumValues());

        $schema = $this->schema('sample');
        $schema->enum('value', ['a', 'b', 'c']);
        $schema->index(['value'])->unique(true);
        $this->atomize('migration2', [$schema]);

        $this->migrator->run();
        $this->assertSame(
            ['a', 'b', 'c'],
            $this->schema('sample')->column('value')->getEnumValues()
        );

        $this->assertTrue($this->db->hasTable('sample'));

        $this->migrator->rollback();
        $this->assertSame(['a', 'b'], $this->schema('sample')->column('value')->getEnumValues());

        $this->assertTrue($this->db->hasTable('sample'));

        $this->migrator->rollback();
        $this->assertFalse($this->db->hasTable('sample'));
    }

    public function testChangeColumnScale(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->decimal('value', 2, 1);
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        $this->assertSame(2, $this->schema('sample')->column('value')->getPrecision());
        $this->assertSame(1, $this->schema('sample')->column('value')->getScale());

        $schema = $this->schema('sample');
        $schema->decimal('value', 3, 2);
        $this->atomize('migration2', [$schema]);

        $this->migrator->run();

        $this->assertSame(3, $this->schema('sample')->column('value')->getPrecision());
        $this->assertSame(2, $this->schema('sample')->column('value')->getScale());

        $this->assertTrue($this->db->hasTable('sample'));

        $this->migrator->rollback();
        $this->assertSame(2, $this->schema('sample')->column('value')->getPrecision());
        $this->assertSame(1, $this->schema('sample')->column('value')->getScale());

        $this->assertTrue($this->db->hasTable('sample'));

        $this->migrator->rollback();
        $this->assertFalse($this->db->hasTable('sample'));
    }
}
