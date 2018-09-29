<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */


namespace Spiral\Migrations\Tests;

use Spiral\Migrations\Capsule;
use Spiral\Migrations\Migration;
use Spiral\Migrations\State;

abstract class MigratorTest extends BaseTest
{
    public function testConfigure()
    {
        $this->assertFalse($this->migrator->isConfigured());

        $this->migrator->configure();
        $this->assertTrue($this->db->hasTable('migrations'));
    }

    //no errors expected
    public function testConfigureTwice()
    {
        $this->assertFalse($this->migrator->isConfigured());

        $this->migrator->configure();
        $this->assertTrue($this->db->hasTable('migrations'));

        $this->migrator->configure();
    }

    public function testGetEmptyMigrations()
    {
        $this->migrator->configure();
        $this->assertSame([], $this->migrator->getMigrations());
    }

    public function testRepository()
    {
        $this->assertSame($this->repository, $this->migrator->getRepository());
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\MigrationException
     */
    public function testRunUnconfigured()
    {
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\MigrationException
     */
    public function testRollbackUnconfigured()
    {
        $this->migrator->rollback();
    }

    public function testCapsule()
    {
        $capsule = new Capsule($this->db);

        $s = $this->schema('test');
        $s->primary('id');
        $s->save();

        $this->assertTrue($capsule->getTable('test')->exists());
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\CapsuleException
     */
    public function testCapsuleException()
    {
        $capsule = new Capsule($this->db);

        $capsule->execute([
            $this
        ]);
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\MigrationException
     */
    public function testNoState()
    {
        $x = new TestMigration();
        $x->up();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\MigrationException
     */
    public function testNoCapsule()
    {
        $x = new TestMigration();
        $x->getTable();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\MigrationException
     */
    public function testNoCapsule2()
    {
        $x = new TestMigration();
        $x->down();
    }

    public function testDatabase()
    {
        $x = new TestMigration();
        $this->assertSame($this->db, $x->withCapsule(new Capsule($this->db))->down());
    }

    public function testState()
    {
        $x = new TestMigration();

        $s = new State("name", new \DateTime());
        $this->assertSame($s, $x->withState($s)->up());
    }
}

class TestMigration extends Migration
{
    public function up()
    {
        return $this->getState();
    }

    public function down()
    {
        return $this->database();
    }

    public function getTable()
    {
        return $this->table('table');
    }
}