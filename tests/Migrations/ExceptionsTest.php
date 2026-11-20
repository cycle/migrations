<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests;

use Cycle\Migrations\Fixtures\AddForeignKeyMigration;
use Cycle\Migrations\Fixtures\AlterForeignKeyMigration;
use Cycle\Migrations\Fixtures\AlterNonExistedColumnMigration;
use Cycle\Migrations\Fixtures\AlterNonExistedIndexMigration;
use Cycle\Migrations\Fixtures\CreateEmptyMigration;
use Cycle\Migrations\Fixtures\CreateSampleMigration;
use Cycle\Migrations\Fixtures\DropForeignKeyMigration;
use Cycle\Migrations\Fixtures\DropNonExistedIndexMigration;
use Cycle\Migrations\Fixtures\DropNonExistedMigration;
use Cycle\Migrations\Fixtures\DuplicateColumnMigration;
use Cycle\Migrations\Fixtures\RenameColumnMigration;
use Cycle\Migrations\Fixtures\RenameTableMigration;
use Cycle\Migrations\Exception\MigrationException;

abstract class ExceptionsTest extends BaseTest
{
    private const MIGRATION_EXCEPTION_PREFIX_REGEX = "/Error in the migration \([0-9a-z_\-]+ \(\d{4}-\d{2}-\d{2} "
    . "\d{2}:\d{2}:\d{2}\)\) occurred: ";

    #[\Testo\Test]
    public function testDropNonExisted(): void
    {
        //Create thought migration
        $this->migrator->configure();
        $this->repository->registerMigration('m', DropNonExistedMigration::class);

        \Testo\Expect::exception(MigrationException::class)->withMessageMatchingRegex(self::MIGRATION_EXCEPTION_PREFIX_REGEX . "Unable to drop table \'.+\'\.\'.+\', table does not exists/");

        $this->migrator->run();
    }

    #[\Testo\Test]
    public function testCreateEmpty(): void
    {
        //Create thought migration
        $this->migrator->configure();
        $this->repository->registerMigration('m', CreateEmptyMigration::class);

        \Testo\Expect::exception(MigrationException::class)->withMessageMatchingRegex(self::MIGRATION_EXCEPTION_PREFIX_REGEX . "Unable to create table \'.+\'\.\'.+\', no columns were added/");

        $this->migrator->run();
    }

    #[\Testo\Test]
    public function testCreateDuplicate(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('idi');
        $s->save();

        $this->repository->registerMigration('m', CreateSampleMigration::class);

        \Testo\Expect::exception(MigrationException::class)->withMessageMatchingRegex(self::MIGRATION_EXCEPTION_PREFIX_REGEX . "Unable to create table '.+'\.'.+', table already exists/");

        $this->migrator->run();
    }

    #[\Testo\Test]
    public function testUpdateNonExisted(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $this->repository->registerMigration('m', DuplicateColumnMigration::class);

        \Testo\Expect::exception(MigrationException::class)->withMessageMatchingRegex(self::MIGRATION_EXCEPTION_PREFIX_REGEX . "Unable to update table '.+'\.'.+', no table exists/");

        $this->migrator->run();
    }

    #[\Testo\Test]
    public function testRenameNonExisted(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $this->repository->registerMigration('m', RenameTableMigration::class);

        \Testo\Expect::exception(MigrationException::class)->withMessageMatchingRegex(self::MIGRATION_EXCEPTION_PREFIX_REGEX . "Unable to rename table '.+'\.'.+', table does not exists/");

        $this->migrator->run();
    }

    #[\Testo\Test]
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

        \Testo\Expect::exception(MigrationException::class)->withMessageMatchingRegex(self::MIGRATION_EXCEPTION_PREFIX_REGEX . "Unable to rename table '.+'\.'.+', table '.+' already exists/");

        $this->migrator->run();
    }

    #[\Testo\Test]
    public function testDuplicateColumn(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->string('column');
        $s->save();

        $this->repository->registerMigration('m', DuplicateColumnMigration::class);

        \Testo\Expect::exception(MigrationException::class)->withMessageMatchingRegex(self::MIGRATION_EXCEPTION_PREFIX_REGEX . "Unable to create column '.+'\.'.+', column already exists/");

        $this->migrator->run();
    }

    #[\Testo\Test]
    public function testDropNonExistedIndex(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->string('column');
        $s->save();

        $this->repository->registerMigration('m', DropNonExistedIndexMigration::class);

        \Testo\Expect::exception(MigrationException::class)->withMessageMatchingRegex(self::MIGRATION_EXCEPTION_PREFIX_REGEX . "Unable to drop index '.+'\.(.+), index does not exists/");

        $this->migrator->run();
    }

    #[\Testo\Test]
    public function testAlterNonExistedIndex(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->string('column');
        $s->save();

        $this->repository->registerMigration('m', AlterNonExistedIndexMigration::class);

        \Testo\Expect::exception(MigrationException::class)->withMessageMatchingRegex(self::MIGRATION_EXCEPTION_PREFIX_REGEX . "Unable to alter index '.+'\.(.+), no such index/");

        $this->migrator->run();
    }

    #[\Testo\Test]
    public function testAlterNonExistedColumn(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->save();

        $this->repository->registerMigration('m', AlterNonExistedColumnMigration::class);

        \Testo\Expect::exception(MigrationException::class)->withMessageMatchingRegex(self::MIGRATION_EXCEPTION_PREFIX_REGEX . "Unable to alter column '.+'\.'.+', column does not exists/");

        $this->migrator->run();
    }

    #[\Testo\Test]
    public function testRenameNonExistedColumn(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->save();

        $this->repository->registerMigration('m', RenameColumnMigration::class);

        \Testo\Expect::exception(MigrationException::class)->withMessageMatchingRegex(self::MIGRATION_EXCEPTION_PREFIX_REGEX . "Unable to rename column '.+'\.'.+', column does not exists/");

        $this->migrator->run();
    }

    #[\Testo\Test]
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

        \Testo\Expect::exception(MigrationException::class)->withMessageMatchingRegex(self::MIGRATION_EXCEPTION_PREFIX_REGEX . "Unable to rename column '.+'\.'.+', column '.+' already exists/");

        $this->migrator->run();
    }

    #[\Testo\Test]
    public function testAddForeignNoTarget(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->integer('column');
        $s->save();

        $this->repository->registerMigration('m', AddForeignKeyMigration::class);

        \Testo\Expect::exception(MigrationException::class)->withMessageMatchingRegex(self::MIGRATION_EXCEPTION_PREFIX_REGEX
        . "Unable to add foreign key 'tests_sample'.'column', foreign table 'target' does not exists/");

        $this->migrator->run();
    }

    #[\Testo\Test]
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

        \Testo\Expect::exception(MigrationException::class)->withMessageMatchingRegex(self::MIGRATION_EXCEPTION_PREFIX_REGEX
        . "Unable to add foreign key '.+'\.'.+', foreign column '.+'\.'.+' does not exists/");

        $this->migrator->run();
    }

    #[\Testo\Test]
    public function testAlterForeignNoFK(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->integer('column');
        $s->save();

        $this->repository->registerMigration('m', AlterForeignKeyMigration::class);

        \Testo\Expect::exception(MigrationException::class)->withMessageMatchingRegex(self::MIGRATION_EXCEPTION_PREFIX_REGEX . "Unable to alter foreign key '.+'\.(.+), key does not exists/");

        $this->migrator->run();
    }

    #[\Testo\Test]
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

        \Testo\Expect::exception(MigrationException::class)->withMessageMatchingRegex(self::MIGRATION_EXCEPTION_PREFIX_REGEX
        . "Unable to alter foreign key '.+'\.'.+', foreign table '.+' does not exists/");

        $this->migrator->run();
    }

    #[\Testo\Test]
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

        \Testo\Expect::exception(MigrationException::class)->withMessageMatchingRegex(self::MIGRATION_EXCEPTION_PREFIX_REGEX
        . "Unable to alter foreign key '.+'\.'.+', foreign column '.+'\.'.+' does not exists/");

        $this->migrator->run();
    }

    #[\Testo\Test]
    public function testDropNonExistedFK(): void
    {
        //Create thought migration
        $this->migrator->configure();

        $s = $this->db->table('sample')->getSchema();
        $s->primary('id');
        $s->integer('column');
        $s->save();

        $this->repository->registerMigration('m', DropForeignKeyMigration::class);

        \Testo\Expect::exception(MigrationException::class)->withMessageMatchingRegex(self::MIGRATION_EXCEPTION_PREFIX_REGEX
        . "Unable to drop foreign key '.+'\.'.+', foreign key does not exists/");

        $this->migrator->run();
    }

    #[\Testo\Test]
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

        \Testo\Expect::exception(MigrationException::class)->withMessageMatchingRegex(self::MIGRATION_EXCEPTION_PREFIX_REGEX . "Unable to add foreign key '.+'\.(.+), foreign key already exists/");

        $this->migrator->run();
    }

    #[\Testo\Test]
    public function testBadMigrationFile(): void
    {
        file_put_contents(__DIR__ . '/../files/mmm.php', 'test');

        \Testo\Expect::exception(\Cycle\Migrations\Exception\RepositoryException::class)->withMessageMatchingRegex("/Invalid migration filename '.+'/");
        $this->repository->getMigrations();
    }

    #[\Testo\Test]
    public function testBadDateFormatMigrationFile(): void
    {
        $fileName = (new \DateTime())->format('dmY-his') . '_0_test.php';
        file_put_contents(__DIR__ . "/../files/{$fileName}", 'test');

        \Testo\Expect::exception(\Cycle\Migrations\Exception\RepositoryException::class)->withMessageMatchingRegex("/Invalid migration filename '.+' - corrupted date format/");
        $this->repository->getMigrations();
    }

    #[\Testo\Test]
    public function testDuplicateClassMigration(): void
    {
        \Testo\Expect::exception(\Cycle\Migrations\Exception\RepositoryException::class);
        $this->repository->registerMigration('unique_name_1', DuplicateColumnMigration::class);
        $this->repository->registerMigration('unique_name_2', DuplicateColumnMigration::class);
    }

    #[\Testo\Test]
    public function testDuplicateFileNameMigration(): void
    {
        \Testo\Expect::exception(\Cycle\Migrations\Exception\RepositoryException::class);
        $this->repository->registerMigration('camel_case_duplicate', DuplicateColumnMigration::class);
        $this->repository->registerMigration('camelCaseDuplicate', CreateEmptyMigration::class);
    }

    #[\Testo\Test]
    public function testInvalidMigration(): void
    {
        \Testo\Expect::exception(\Cycle\Migrations\Exception\RepositoryException::class);
        $this->repository->registerMigration('m', 'invalid');
    }
}
