<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Migrations\Tests;

use DateTime;
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
    public function testDropNonExisted(): void
    {
        //Create thought migration
        $this->migrator->configure();
        $this->repository->registerMigration('m', DropNonExistedMigration::class);

        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\TableException
     */
    public function testCreateEmpty(): void
    {
        //Create thought migration
        $this->migrator->configure();
        $this->repository->registerMigration('m', CreateEmptyMigration::class);

        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\TableException
     */
    public function testCreateDuplicate(): void
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
    public function testUpdateNonExisted(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $this->repository->registerMigration('m', DuplicateColumnMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\TableException
     */
    public function testRenameNonExisted(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $this->repository->registerMigration('m', RenameTableMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\TableException
     */
    public function testRenameButBusy(): void
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
    public function testDuplicateColumn(): void
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
    public function testDropNonExistedIndex(): void
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
    public function testAlterNonExistedIndex(): void
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
    public function testAlterNonExistedColumn(): void
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
    public function testRenameNonExistedColumn(): void
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
    public function testRenameDuplicateExistedColumn(): void
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
    public function testAddForeignNoTarget(): void
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
    public function testAddForeignNoTargetColumn(): void
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
    public function testAlterForeignNoFK(): void
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
    public function testAlterForeignNoTable(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $s2 = $this->db->table('target')->getSchema();
        $s2->primary('id');
        $s2->save();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->integer('column');
        $s->foreignKey(['column'])->references('target', ['id']);
        $s->save();

        $this->repository->registerMigration('m', AlterForeignKeyMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\ForeignKeyException
     */
    public function testAlterForeignNoColumn(): void
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
        $s->foreignKey(['column'])->references('target', ['id']);
        $s->save();

        $this->repository->registerMigration('m', AlterForeignKeyMigration::class);
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\Operation\ForeignKeyException
     */
    public function testDropNonExistedFK(): void
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
    public function testAddExisted(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $s2 = $this->db->table('target')->getSchema();
        $s2->primary('id');
        $s2->save();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->integer('column');
        $s->foreignKey(['column'])->references('target', ['id']);
        $s->save();

        $this->repository->registerMigration('m', AddForeignKeyMigration::class);
        $this->migrator->run();
    }

    public function testBadMigrationFile(): void
    {
        file_put_contents(__DIR__ . '/../files/mmm.php', 'test');

        $this->expectException(\Spiral\Migrations\Exception\RepositoryException::class);
        $this->expectExceptionMessageMatches("/Invalid migration filename '.+'/");
        $this->repository->getMigrations();
    }

    public function testBadDateFormatMigrationFile(): void
    {
        $fileName = (new \DateTime())->format('dmY-his') . '_0_test.php';
        file_put_contents(__DIR__ . "/../files/{$fileName}", 'test');

        $this->expectException(\Spiral\Migrations\Exception\RepositoryException::class);
        $this->expectExceptionMessageMatches("/Invalid migration filename '.+' - corrupted date format/");
        $this->repository->getMigrations();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\RepositoryException
     */
    public function testDuplicateClassMigration(): void
    {
        $this->repository->registerMigration('unique_name_1', DuplicateColumnMigration::class);
        $this->repository->registerMigration('unique_name_2', DuplicateColumnMigration::class);
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\RepositoryException
     */
    public function testDuplicateFileNameMigration(): void
    {
        $this->repository->registerMigration('camel_case_duplicate', DuplicateColumnMigration::class);
        $this->repository->registerMigration('camelCaseDuplicate', CreateEmptyMigration::class);
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\RepositoryException
     */
    public function testInvalidMigration(): void
    {
        $this->repository->registerMigration('m', 'invalid');
    }
}
