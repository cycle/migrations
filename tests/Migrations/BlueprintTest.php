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
use Cycle\Migrations\Capsule;
use Cycle\Migrations\TableBlueprint;

abstract class BlueprintTest extends BaseTest
{
    public function testCreateButNot(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary');

        //Not created
        $this->assertFalse($blueprint->getSchema()->exists());
    }

    public function testCreate(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')->create();

        //Not created
        $this->assertTrue($blueprint->getSchema()->exists());
    }

    public function testCreateWithColumns(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->create();

        //Not created
        $this->assertTrue($blueprint->getSchema()->exists());
    }

    public function testCreateWithIndexesAndDropIndex(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        $this->assertTrue($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->dropIndex(['value'])->update();

        //Not created
        $this->assertTrue($blueprint->getSchema()->exists());
    }

    public function testCreateWithNamedIndex(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true, 'name' => 'super_index'])
            ->create();

        $this->assertSame('super_index', $this->schema('sample')->index(['value'])->getName());
    }

    public function testCreateWithForeign(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample1');

        $blueprint->addColumn('id', 'primary')->create();

        //Not created
        $this->assertTrue($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->addColumn('sample_id', 'int')
            ->addForeignKey(['sample_id'], 'sample1', ['id'], [
                'onDelete' => ForeignKeyInterface::CASCADE,
                'onUpdate' => ForeignKeyInterface::NO_ACTION,
            ])
            ->create();

        //Not created
        $this->assertTrue($blueprint->getSchema()->exists());
    }

    public function testCreateWithForeignAliased(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample1');

        $blueprint->addColumn('id', 'primary')->create();

        //Not created
        $this->assertTrue($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->addColumn('sample_id', 'int')
            ->addForeignKey(['sample_id'], 'sample1', ['id'], [
                'delete' => ForeignKeyInterface::CASCADE,
                'update' => ForeignKeyInterface::NO_ACTION,
            ])
            ->create();

        //Not created
        $this->assertTrue($blueprint->getSchema()->exists());
    }

    public function testUpdateTableError(): void
    {
        $this->expectException(\Cycle\Migrations\Exception\Operation\TableException::class);
        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        $this->assertTrue($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');

        $blueprint->dropColumn('value')
            ->create(); //wrong

        //Not created
        $this->assertTrue($blueprint->getSchema()->exists());
    }

    public function testUpdateTable(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        $this->assertTrue($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->dropColumn('value')
            ->update();
    }

    public function testUpdateTableError2(): void
    {
        $this->expectException(\Cycle\Migrations\Exception\Operation\ColumnException::class);
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        $this->assertTrue($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');

        $blueprint->addColumn('value', 'int')->update();
    }

    public function testUpdateTableError5(): void
    {
        $this->expectException(\Cycle\Migrations\Exception\Operation\ColumnException::class);
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'enum', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        $this->assertTrue($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');

        $blueprint->addColumn('value', 'int')->update();
    }

    public function testUpdateTableError3(): void
    {
        $this->expectException(\Cycle\Migrations\Exception\Operation\IndexException::class);
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        $this->assertTrue($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');

        $blueprint->addIndex(['value'])->update();
    }

    public function testDropTable(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        $this->assertTrue($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');

        $blueprint->drop();
    }

    public function testRenameTable(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        $this->assertTrue($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->rename('new_name');
    }
}
