<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Tests;

use Spiral\Migrations\Fixtures\CreateEmptyMigration;
use Spiral\Migrations\Fixtures\CreateSampleMigration;
use Spiral\Migrations\Fixtures\DropNonExistedMigration;
use Spiral\Migrations\Fixtures\DuplicateColumnMigration;
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
}