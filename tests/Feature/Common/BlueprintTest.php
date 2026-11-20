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
use Cycle\Database\ForeignKeyInterface;
use Cycle\Migrations\Capsule;
use Cycle\Migrations\TableBlueprint;
use Cycle\Testing\Database\Attribute\ExceptDriver;
use Cycle\Testing\Database\Driver;

final class BlueprintTest extends MigrationsTestCase
{
    #[\Testo\Test]
    public function testCreateButNot(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary');

        //Not created
        \Testo\Assert::false($blueprint->getSchema()->exists());
    }

    #[\Testo\Test]
    public function testCreate(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')->create();

        //Not created
        \Testo\Assert::true($blueprint->getSchema()->exists());
    }

    #[\Testo\Test]
    public function testCreateWithColumns(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->create();

        //Not created
        \Testo\Assert::true($blueprint->getSchema()->exists());
    }

    #[\Testo\Test]
    public function testCreateWithIndexesAndDropIndex(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        \Testo\Assert::true($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->dropIndex(['value'])->update();

        //Not created
        \Testo\Assert::true($blueprint->getSchema()->exists());
    }

    #[\Testo\Test]
    public function testCreateWithNamedIndex(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true, 'name' => 'super_index'])
            ->create();

        \Testo\Assert::same($this->schema('sample')->index(['value'])->getName(), 'super_index');
    }

    #[\Testo\Test]
    public function testCreateWithForeign(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample1');

        $blueprint->addColumn('id', 'primary')->create();

        //Not created
        \Testo\Assert::true($blueprint->getSchema()->exists());

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
        \Testo\Assert::true($blueprint->getSchema()->exists());
    }

    #[\Testo\Test]
    public function testCreateWithForeignAliased(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample1');

        $blueprint->addColumn('id', 'primary')->create();

        //Not created
        \Testo\Assert::true($blueprint->getSchema()->exists());

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
        \Testo\Assert::true($blueprint->getSchema()->exists());
    }

    #[\Testo\Test]
    public function testCreateForeignWithIndex(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample1');

        $blueprint->addColumn('id', 'primary')->create();
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('sample_id', 'int')
            ->addForeignKey(['sample_id'], 'sample1', ['id'])
            ->create();

        \Testo\Assert::true($blueprint->getSchema()->hasIndex(['sample_id']));
    }

    #[\Testo\Test]
    public function testUpdateTableError(): void
    {
        \Testo\Expect::exception(\Cycle\Migrations\Exception\Operation\TableException::class);
        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        \Testo\Assert::true($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');

        $blueprint->dropColumn('value')
            ->create(); //wrong

        //Not created
        \Testo\Assert::true($blueprint->getSchema()->exists());
    }

    #[\Testo\Test]
    public function testUpdateTable(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        \Testo\Assert::true($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->dropColumn('value')
            ->update();
    }

    #[\Testo\Test]
    public function testUpdateTableError2(): void
    {
        \Testo\Expect::exception(\Cycle\Migrations\Exception\Operation\ColumnException::class);
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        \Testo\Assert::true($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');

        $blueprint->addColumn('value', 'int')->update();
    }

    #[\Testo\Test]
    public function testUpdateTableError5(): void
    {
        \Testo\Expect::exception(\Cycle\Migrations\Exception\Operation\ColumnException::class);
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'enum', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        \Testo\Assert::true($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');

        $blueprint->addColumn('value', 'int')->update();
    }

    #[\Testo\Test]
    public function testUpdateTableError3(): void
    {
        \Testo\Expect::exception(\Cycle\Migrations\Exception\Operation\IndexException::class);
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        \Testo\Assert::true($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');

        $blueprint->addIndex(['value'])->update();
    }

    #[\Testo\Test]
    public function testDropTable(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        \Testo\Assert::true($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');

        $blueprint->drop();
    }

    #[\Testo\Test]
    public function testRenameTable(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('value', 'float', ['default' => 1])
            ->addIndex(['value'], ['unique' => true])
            ->create();

        //Not created
        \Testo\Assert::true($blueprint->getSchema()->exists());

        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->rename('new_name');
    }

    #[\Testo\Test]
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

        \Testo\Assert::false($blueprint->getSchema()->hasIndex(['sample_id']));
    }
}
