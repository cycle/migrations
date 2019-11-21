<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Migrations\Tests;

use Spiral\Database\ForeignKeyInterface;

abstract class ComplexAtomizerTest extends BaseTest
{
    public function testCreateMultiple(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $schema->index(['value']);

        $schema1 = $this->schema('sample1');
        $schema1->primary('id');
        $schema1->float('value');
        $schema1->integer('sample_id');
        $schema1->foreignKey(['sample_id'])->references('sample', ['id']);

        $this->atomize('migration1', [$schema, $schema1]);
        $this->migrator->run();

        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertTrue($this->db->hasTable('sample1'));

        $this->migrator->rollback();
        $this->assertFalse($this->db->hasTable('sample'));
        $this->assertFalse($this->db->hasTable('sample1'));
    }

    public function testCreateMultipleChangeFK(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $schema->index(['value']);

        $schema1 = $this->schema('sample1');
        $schema1->primary('id');
        $schema1->float('value');
        $schema1->integer('sample_id');
        $schema1->foreignKey(['sample_id'])->references('sample', ['id'])
            ->onDelete(ForeignKeyInterface::CASCADE)
            ->onUpdate(ForeignKeyInterface::CASCADE);

        $this->atomize('migration1', [$schema, $schema1]);
        $this->migrator->run();

        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertTrue($this->db->hasTable('sample1'));

        $fk = $this->schema('sample1')->foreignKey(['sample_id']);
        $this->assertSame(ForeignKeyInterface::CASCADE, $fk->getDeleteRule());
        $this->assertSame(ForeignKeyInterface::CASCADE, $fk->getUpdateRule());

        $schema1 = $this->schema('sample1');
        $schema1->foreignKey(['sample_id'])->references('sample', ['id'])
            ->onDelete(ForeignKeyInterface::NO_ACTION)
            ->onUpdate(ForeignKeyInterface::NO_ACTION);

        $this->atomize('migration1', [$this->schema('sample'), $schema1]);
        $this->migrator->run();

        $fk = $this->schema('sample1')->foreignKey(['sample_id']);
        $this->assertSame(ForeignKeyInterface::NO_ACTION, $fk->getDeleteRule());
        $this->assertSame(ForeignKeyInterface::NO_ACTION, $fk->getUpdateRule());

        $this->migrator->rollback();
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertTrue($this->db->hasTable('sample1'));

        $fk = $this->schema('sample1')->foreignKey(['sample_id']);
        $this->assertSame(ForeignKeyInterface::CASCADE, $fk->getDeleteRule());
        $this->assertSame(ForeignKeyInterface::CASCADE, $fk->getUpdateRule());

        $this->migrator->rollback();
        $this->assertFalse($this->db->hasTable('sample'));
        $this->assertFalse($this->db->hasTable('sample1'));
    }

    public function testCreateMultipleWithPivot(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $schema->index(['value']);

        $schema1 = $this->schema('sample1');
        $schema1->primary('id');
        $schema1->float('value');
        $schema1->integer('sample_id');
        $schema1->foreignKey(['sample_id'])->references('sample', ['id']);

        $schema2 = $this->schema('sample2');
        $schema2->integer('sample_id');
        $schema2->foreignKey(['sample_id'])->references('sample', ['id']);
        $schema2->integer('sample1_id');
        $schema2->foreignKey(['sample1_id'])->references('sample1', ['id']);

        $this->atomize('migration1', [$schema, $schema1, $schema2]);
        $this->migrator->run();

        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertTrue($this->db->hasTable('sample1'));
        $this->assertTrue($this->db->hasTable('sample2'));

        $this->migrator->rollback();
        $this->assertFalse($this->db->hasTable('sample'));
        $this->assertFalse($this->db->hasTable('sample1'));
        $this->assertFalse($this->db->hasTable('sample2'));
    }

    public function testCreateAndAddFK(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $schema->index(['value']);

        $schema1 = $this->schema('sample1');
        $schema1->primary('id');
        $schema1->float('value');

        $this->atomize('migration1', [$schema, $schema1]);
        $this->migrator->run();
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertTrue($this->db->hasTable('sample1'));

        $schema1 = $this->schema('sample1');
        $schema1->integer('sample_id');
        $schema1->foreignKey(['sample_id'])->references('sample', ['id']);

        $this->atomize('migration2', [$this->schema('sample'), $schema1]);

        $this->migrator->run();
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertTrue($this->db->hasTable('sample1'));
        $this->assertTrue($this->schema('sample1')->hasForeignKey(['sample_id']));

        $this->migrator->rollback();
        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertTrue($this->db->hasTable('sample1'));
        $this->assertFalse($this->schema('sample1')->hasForeignKey(['sample_id']));

        $this->migrator->rollback();
        $this->assertFalse($this->db->hasTable('sample'));
        $this->assertFalse($this->db->hasTable('sample1'));
    }

    public function testDropFK(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('value');
        $schema->index(['value']);

        $schema1 = $this->schema('sample1');
        $schema1->primary('id');
        $schema1->float('value');
        $schema1->integer('sk');
        $schema1->foreignKey(['sk'])->references('sample', ['id']);

        $this->atomize('migration1', [$schema, $schema1]);
        $this->migrator->run();

        $this->assertTrue($this->db->hasTable('sample'));
        $this->assertTrue($this->db->hasTable('sample1'));

        $this->assertTrue($this->db->table('sample1')->hasForeignKey(['sk']));

        $schema1 = $this->schema('sample1');
        $schema1->dropForeignKey(['sk']);

        $this->atomize('migration2', [$this->schema('sample'), $schema1]);

        $this->migrator->run();
        $this->assertFalse($this->db->table('sample1')->hasForeignKey(['sk']));

        $this->migrator->rollback();
        $this->assertTrue($this->db->table('sample1')->hasForeignKey(['sk']));
    }
}
