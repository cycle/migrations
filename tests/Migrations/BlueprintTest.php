<?php
/**
 * Spiral, Core Components
 *
 * @author Wolfy-J
 */

namespace Spiral\Migrations\Tests;

use Spiral\Database\ForeignKeyInterface;
use Spiral\Migrations\Capsule;
use Spiral\Migrations\TableBlueprint;

abstract class BlueprintTest extends BaseTest
{
//    public function testCreateButNot()
//    {
//        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');
//
//        $blueprint->addColumn('id', 'primary');
//
//        //Not created
//        $this->assertFalse($blueprint->getSchema()->exists());
//    }
//
//    public function testCreate()
//    {
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->addColumn('id', 'primary')->create();
//
//        //Not created
//        $this->assertTrue($blueprint->getSchema()->exists());
//    }
//
//    public function testCreateWithColumns()
//    {
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->addColumn('id', 'primary')
//            ->addColumn('value', 'float', ['default' => 1])
//            ->create();
//
//        //Not created
//        $this->assertTrue($blueprint->getSchema()->exists());
//    }
//
//    public function testCreateWithIndexesAndDropIndex()
//    {
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->addColumn('id', 'primary')
//            ->addColumn('value', 'float', ['default' => 1])
//            ->addIndex(['value'], ['unique' => true])
//            ->create();
//
//        //Not created
//        $this->assertTrue($blueprint->getSchema()->exists());
//
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->dropIndex(['value'])->update();
//
//        //Not created
//        $this->assertTrue($blueprint->getSchema()->exists());
//    }
//
//    public function testCreateWithNamedIndex()
//    {
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->addColumn('id', 'primary')
//            ->addColumn('value', 'float', ['default' => 1])
//            ->addIndex(['value'], ['unique' => true, 'name' => 'super_index'])
//            ->create();
//
//        $this->assertSame('super_index', $this->schema('sample')->index(['value'])->getName());
//    }
//
//    public function testCreateWithForeign()
//    {
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample1');
//
//        $blueprint->addColumn('id', 'primary')->create();
//
//        //Not created
//        $this->assertTrue($blueprint->getSchema()->exists());
//
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->addColumn('id', 'primary')
//            ->addColumn('value', 'float', ['default' => 1])
//            ->addIndex(['value'], ['unique' => true])
//            ->addColumn('sample_id', 'int')
//            ->addForeignKey('sample_id', 'sample1', 'id', [
//                'onDelete' => ForeignKeyInterface::CASCADE,
//                'onUpdate' => ForeignKeyInterface::NO_ACTION,
//            ])
//            ->create();
//
//        //Not created
//        $this->assertTrue($blueprint->getSchema()->exists());
//    }
//
//    public function testCreateWithForeignAliased()
//    {
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample1');
//
//        $blueprint->addColumn('id', 'primary')->create();
//
//        //Not created
//        $this->assertTrue($blueprint->getSchema()->exists());
//
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->addColumn('id', 'primary')
//            ->addColumn('value', 'float', ['default' => 1])
//            ->addIndex(['value'], ['unique' => true])
//            ->addColumn('sample_id', 'int')
//            ->addForeignKey('sample_id', 'sample1', 'id', [
//                'delete' => ForeignKeyInterface::CASCADE,
//                'update' => ForeignKeyInterface::NO_ACTION,
//            ])
//            ->create();
//
//        //Not created
//        $this->assertTrue($blueprint->getSchema()->exists());
//    }
//
//    /**
//     * @expectedException \Spiral\Migrations\Exception\Operation\TableException
//     */
//    public function testUpdateTableError()
//    {
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->addColumn('id', 'primary')
//            ->addColumn('value', 'float', ['default' => 1])
//            ->addIndex(['value'], ['unique' => true])
//            ->create();
//
//        //Not created
//        $this->assertTrue($blueprint->getSchema()->exists());
//
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->dropColumn('value')
//            ->create(); //wrong
//
//        //Not created
//        $this->assertTrue($blueprint->getSchema()->exists());
//    }
//
//    public function testUpdateTable()
//    {
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->addColumn('id', 'primary')
//            ->addColumn('value', 'float', ['default' => 1])
//            ->addIndex(['value'], ['unique' => true])
//            ->create();
//
//        //Not created
//        $this->assertTrue($blueprint->getSchema()->exists());
//
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->dropColumn('value')
//            ->update();
//    }
//
//    /**
//     * @expectedException \Spiral\Migrations\Exception\Operation\ColumnException
//     */
//    public function testUpdateTableError2()
//    {
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->addColumn('id', 'primary')
//            ->addColumn('value', 'float', ['default' => 1])
//            ->addIndex(['value'], ['unique' => true])
//            ->create();
//
//        //Not created
//        $this->assertTrue($blueprint->getSchema()->exists());
//
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->addColumn('value', 'int')->update();
//    }
//
//    /**
//     * @expectedException \Spiral\Migrations\Exception\Operation\ColumnException
//     */
//    public function testUpdateTableError5()
//    {
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->addColumn('id', 'primary')
//            ->addColumn('value', 'enum', ['default' => 1])
//            ->addIndex(['value'], ['unique' => true])
//            ->create();
//
//        //Not created
//        $this->assertTrue($blueprint->getSchema()->exists());
//
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->addColumn('value', 'int')->update();
//    }
//
//    /**
//     * @expectedException \Spiral\Migrations\Exception\Operation\IndexException
//     */
//    public function testUpdateTableError3()
//    {
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->addColumn('id', 'primary')
//            ->addColumn('value', 'float', ['default' => 1])
//            ->addIndex(['value'], ['unique' => true])
//            ->create();
//
//        //Not created
//        $this->assertTrue($blueprint->getSchema()->exists());
//
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->addIndex(['value'])->update();
//    }
//
//    public function testDropTable()
//    {
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->addColumn('id', 'primary')
//            ->addColumn('value', 'float', ['default' => 1])
//            ->addIndex(['value'], ['unique' => true])
//            ->create();
//
//        //Not created
//        $this->assertTrue($blueprint->getSchema()->exists());
//
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->drop();
//    }
//
//    public function testRenameTable()
//    {
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->addColumn('id', 'primary')
//            ->addColumn('value', 'float', ['default' => 1])
//            ->addIndex(['value'], ['unique' => true])
//            ->create();
//
//        //Not created
//        $this->assertTrue($blueprint->getSchema()->exists());
//
//        $blueprint = new TableBlueprint($capsule = new Capsule($this->db), 'sample');
//
//        $blueprint->rename('new_name');
//    }
}
