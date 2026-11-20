<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests;

use Cycle\Database\ForeignKeyInterface;

abstract class ComplexAtomizerTest extends BaseTest
{
    #[\Testo\Test]
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

        \Testo\Assert::true($this->db->hasTable('sample'));
        \Testo\Assert::true($this->db->hasTable('sample1'));

        $this->migrator->rollback();
        \Testo\Assert::false($this->db->hasTable('sample'));
        \Testo\Assert::false($this->db->hasTable('sample1'));
    }

    #[\Testo\Test]
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

        \Testo\Assert::true($this->db->hasTable('sample'));
        \Testo\Assert::true($this->db->hasTable('sample1'));

        $fk = $this->schema('sample1')->foreignKey(['sample_id']);
        \Testo\Assert::same($fk->getDeleteRule(), ForeignKeyInterface::CASCADE);
        \Testo\Assert::same($fk->getUpdateRule(), ForeignKeyInterface::CASCADE);

        $schema1 = $this->schema('sample1');
        $schema1->foreignKey(['sample_id'])->references('sample', ['id'])
            ->onDelete(ForeignKeyInterface::NO_ACTION)
            ->onUpdate(ForeignKeyInterface::NO_ACTION);

        $this->atomize('migration1', [$this->schema('sample'), $schema1]);
        $this->migrator->run();

        $fk = $this->schema('sample1')->foreignKey(['sample_id']);
        \Testo\Assert::same($fk->getDeleteRule(), ForeignKeyInterface::NO_ACTION);
        \Testo\Assert::same($fk->getUpdateRule(), ForeignKeyInterface::NO_ACTION);

        $this->migrator->rollback();
        \Testo\Assert::true($this->db->hasTable('sample'));
        \Testo\Assert::true($this->db->hasTable('sample1'));

        $fk = $this->schema('sample1')->foreignKey(['sample_id']);
        \Testo\Assert::same($fk->getDeleteRule(), ForeignKeyInterface::CASCADE);
        \Testo\Assert::same($fk->getUpdateRule(), ForeignKeyInterface::CASCADE);

        $this->migrator->rollback();
        \Testo\Assert::false($this->db->hasTable('sample'));
        \Testo\Assert::false($this->db->hasTable('sample1'));
    }

    #[\Testo\Test]
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

        \Testo\Assert::true($this->db->hasTable('sample'));
        \Testo\Assert::true($this->db->hasTable('sample1'));
        \Testo\Assert::true($this->db->hasTable('sample2'));

        $this->migrator->rollback();
        \Testo\Assert::false($this->db->hasTable('sample'));
        \Testo\Assert::false($this->db->hasTable('sample1'));
        \Testo\Assert::false($this->db->hasTable('sample2'));
    }

    #[\Testo\Test]
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
        \Testo\Assert::true($this->db->hasTable('sample'));
        \Testo\Assert::true($this->db->hasTable('sample1'));

        $schema1 = $this->schema('sample1');
        $schema1->integer('sample_id');
        $schema1->foreignKey(['sample_id'])->references('sample', ['id']);

        $this->atomize('migration2', [$this->schema('sample'), $schema1]);

        $this->migrator->run();
        \Testo\Assert::true($this->db->hasTable('sample'));
        \Testo\Assert::true($this->db->hasTable('sample1'));
        \Testo\Assert::true($this->schema('sample1')->hasForeignKey(['sample_id']));

        $this->migrator->rollback();
        \Testo\Assert::true($this->db->hasTable('sample'));
        \Testo\Assert::true($this->db->hasTable('sample1'));
        \Testo\Assert::false($this->schema('sample1')->hasForeignKey(['sample_id']));

        $this->migrator->rollback();
        \Testo\Assert::false($this->db->hasTable('sample'));
        \Testo\Assert::false($this->db->hasTable('sample1'));
    }

    #[\Testo\Test]
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

        \Testo\Assert::true($this->db->hasTable('sample'));
        \Testo\Assert::true($this->db->hasTable('sample1'));

        \Testo\Assert::true($this->db->table('sample1')->hasForeignKey(['sk']));

        $schema1 = $this->schema('sample1');
        $schema1->dropForeignKey(['sk']);

        $this->atomize('migration2', [$this->schema('sample'), $schema1]);

        $this->migrator->run();
        \Testo\Assert::false($this->db->table('sample1')->hasForeignKey(['sk']));

        $this->migrator->rollback();
        \Testo\Assert::true($this->db->table('sample1')->hasForeignKey(['sk']));
    }
}
