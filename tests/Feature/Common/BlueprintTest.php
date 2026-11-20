<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Feature\Common;

use Cycle\Database\ForeignKeyInterface;
use Cycle\Migrations\Capsule;
use Cycle\Migrations\TableBlueprint;
use Cycle\Migrations\Tests\Support\MigrationsTestCase;
use Cycle\Testing\Database\Attribute\ExceptDriver;
use Cycle\Testing\Database\Driver;
use Testo\Assert;
use Testo\Expect;
use Testo\Test;

final class BlueprintTest extends MigrationsTestCase
{
    #[Test]
    public function testCreateButNot(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary');

        //Not created
        Assert::false($blueprint->getSchema()->exists());
    }

    #[Test]
    public function testCreate(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')->create();

        //Not created
        Assert::true($blueprint->getSchema()->exists());
    }

    #[Test]
    public function testCreateWithColumns(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->create();

        //Not created
        Assert::true($blueprint->getSchema()->exists());
        Assert::equals($this->schema('sample')->column('value')->getDefaultValue(), 1);
    }

    #[Test]
    public function testCreateWithIndexesAndDropIndex(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        Assert::true($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->dropIndex(['value'])->update();

        //Not created
        Assert::true($blueprint->getSchema()->exists());
    }

    #[Test]
    public function testCreateWithNamedIndex(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true, 'name' => 'super_index'])
            ->create();

        Assert::same($this->schema('sample')->index(['value'])->getName(), 'super_index');
    }

    #[Test]
    public function testCreateWithForeign(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample1');

        $blueprint->addColumn('id', 'primary')->create();

        //Not created
        Assert::true($blueprint->getSchema()->exists());

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
        Assert::true($blueprint->getSchema()->exists());
    }

    #[Test]
    public function testCreateWithForeignAliased(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample1');

        $blueprint->addColumn('id', 'primary')->create();

        //Not created
        Assert::true($blueprint->getSchema()->exists());

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
        Assert::true($blueprint->getSchema()->exists());
    }

    #[Test]
    public function testCreateForeignWithIndex(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample1');

        $blueprint->addColumn('id', 'primary')->create();
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('sample_id', 'int')
            ->addForeignKey(['sample_id'], 'sample1', ['id'])
            ->create();

        Assert::true($blueprint->getSchema()->hasIndex(['sample_id']));
    }

    #[Test]
    public function testUpdateTableError(): void
    {
        Expect::exception(\Cycle\Migrations\Exception\Operation\TableException::class);
        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        Assert::true($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');

        $blueprint->dropColumn('value')
            ->create(); //wrong

        //Not created
        Assert::true($blueprint->getSchema()->exists());
    }

    #[Test]
    public function testUpdateTable(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        Assert::true($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->dropColumn('value')
            ->update();
    }

    #[Test]
    public function testUpdateTableError2(): void
    {
        Expect::exception(\Cycle\Migrations\Exception\Operation\ColumnException::class);
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        Assert::true($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');

        $blueprint->addColumn('value', 'int')->update();
    }

    #[Test]
    public function testUpdateTableError5(): void
    {
        Expect::exception(\Cycle\Migrations\Exception\Operation\ColumnException::class);
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'enum', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        Assert::true($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');

        $blueprint->addColumn('value', 'int')->update();
    }

    #[Test]
    public function testUpdateTableError3(): void
    {
        Expect::exception(\Cycle\Migrations\Exception\Operation\IndexException::class);
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        Assert::true($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');

        $blueprint->addIndex(['value'])->update();
    }

    #[Test]
    public function testDropTable(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        Assert::true($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');

        $blueprint->drop();
    }

    #[Test]
    public function testRenameTable(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        Assert::true($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->rename('new_name');

        Assert::true($this->db->hasTable('new_name'));
        Assert::false($this->db->hasTable('sample'));
    }

    #[Test]
    #[ExceptDriver(Driver::MySQL)]
    public function testCreateForeignWithoutIndex(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample1');

        $blueprint->addColumn('id', 'primary')->create();
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('sample_id', 'int')
            ->addForeignKey(['sample_id'], 'sample1', ['id'], ['indexCreate' => false])
            ->create();

        Assert::false($blueprint->getSchema()->hasIndex(['sample_id']));
    }
}
