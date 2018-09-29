<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Tests;

use Spiral\Migrations\Fixtures\AddForeignKeyMigration;
use Spiral\Migrations\Fixtures\AlterForeignKeyMigration;
use Spiral\Migrations\Fixtures\AlterNonExistedColumnMigration;
use Spiral\Migrations\Fixtures\AlterNonExistedIndexMigration;
use Spiral\Migrations\Fixtures\CreateEmptyMigration;
use Spiral\Migrations\Fixtures\CreateSampleMigration;
use Spiral\Migrations\Fixtures\DropForeignKeyMigration;
use Spiral\Migrations\Fixtures\DropNonExistedIndexMigration;
use Spiral\Migrations\Fixtures\DropNonExistedMigration;
use Spiral\Migrations\Fixtures\DuplicateColumnMigration;
use Spiral\Migrations\Fixtures\RenameColumnMigration;
use Spiral\Migrations\Fixtures\RenameTableMigration;

abstract class ExceptionsTest extends BaseTest
{
    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\TableException
     */
    public function testDropNonExisted()
    {
        //Create thought migration
        $this->migrator->configure();
        $this->repository->registerMigration('m', DropNonExistedMigration::class);

        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\TableException
     */
    public function testCreateEmpty()
    {
        //Create thought migration
        $this->migrator->configure();
        $this->repository->registerMigration('m', CreateEmptyMigration::class);

        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\TableException
     */
    public function testCreateDuplicate()
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('idi');
        $s->save();

        $this->repository->registerMigration('m', CreateSampleMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\TableException
     */
    public function testUpdateNonExisted()
    {
        //Create thought migration
        $this->migrator->configure();

        $this->repository->registerMigration('m', DuplicateColumnMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\TableException
     */
    public function testRenameNonExisted()
    {
        //Create thought migration
        $this->migrator->configure();

        $this->repository->registerMigration('m', RenameTableMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\TableException
     */
    public function testRenameButBusy()
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('idi');
        $s->save();

        $s = $this->db->table('new_name')->getSchema();
        $s->primary('idi');
        $s->save();

        $this->repository->registerMigration('m', RenameTableMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\ColumnException
     */
    public function testDuplicateColumn()
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->string('column');
        $s->save();

        $this->repository->registerMigration('m', DuplicateColumnMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\IndexException
     */
    public function testDropNonExistedIndex()
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->string('column');
        $s->save();

        $this->repository->registerMigration('m', DropNonExistedIndexMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\IndexException
     */
    public function testAlterNonExistedIndex()
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->string('column');
        $s->save();

        $this->repository->registerMigration('m', AlterNonExistedIndexMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\ColumnException
     */
    public function testAlterNonExistedColumn()
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->save();

        $this->repository->registerMigration('m', AlterNonExistedColumnMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\ColumnException
     */
    public function testRenameNonExistedColumn()
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->save();

        $this->repository->registerMigration('m', RenameColumnMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\ColumnException
     */
    public function testRenameDuplicateExistedColumn()
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->string('column');
        $s->float('new_name');
        $s->save();

        $this->repository->registerMigration('m', RenameColumnMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\ForeignKeyException
     */
    public function testAddForeignNoTarget()
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->integer('column');
        $s->save();

        $this->repository->registerMigration('m', AddForeignKeyMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\ForeignKeyException
     */
    public function testAddForeignNoTargetColumn()
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->integer('column');
        $s->save();

        $s2 = $this->db->table('target')->getSchema();
        $s2->primary('id2');
        $s2->save();

        $this->repository->registerMigration('m', AddForeignKeyMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\ForeignKeyException
     */
    public function testAlterForeignNoFK()
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->integer('column');
        $s->save();

        $this->repository->registerMigration('m', AlterForeignKeyMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\ForeignKeyException
     */
    public function testAlterForeignNoTable()
    {
        //Create thought migration
        $this->migrator->configure();

        $s2 = $this->db->table('target')->getSchema();
        $s2->primary('id');
        $s2->save();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->integer('column');
        $s->foreignKey('column')->references('target', 'id');
        $s->save();

        $this->repository->registerMigration('m', AlterForeignKeyMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\ForeignKeyException
     */
    public function testAlterForeignNoColumn()
    {
        //Create thought migration
        $this->migrator->configure();

        $s2 = $this->db->table('target')->getSchema();
        $s2->primary('id');
        $s2->save();

        $s2 = $this->db->table('target2')->getSchema();
        $s2->primary('id');
        $s2->save();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->integer('column');
        $s->foreignKey('column')->references('target', 'id');
        $s->save();

        $this->repository->registerMigration('m', AlterForeignKeyMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\ForeignKeyException
     */
    public function testDropNonExistedFK()
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->integer('column');
        $s->save();

        $this->repository->registerMigration('m', DropForeignKeyMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\ForeignKeyException
     */
    public function testAddExisted()
    {
        //Create thought migration
        $this->migrator->configure();

        $s2 = $this->db->table('target')->getSchema();
        $s2->primary('id');
        $s2->save();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->integer('column');
        $s->foreignKey('column')->references('target', 'id');
        $s->save();

        $this->repository->registerMigration('m', AddForeignKeyMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\RepositoryException
     */
    public function testBadMigrationFile()
    {
        file_put_contents(__DIR__ . '/../files/mmm.php', 'test');
        $this->repository->getMigrations();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\RepositoryException
     */
    public function testDuplicateMigration()
    {
        $this->repository->registerMigration('m', DuplicateColumnMigration::class);
        $this->repository->registerMigration('m', DuplicateColumnMigration::class);
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\RepositoryException
     */
    public function testInvalidMigration()
    {
        $this->repository->registerMigration('m', 'invalid');
    }
}