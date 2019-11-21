<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Migrations\Tests;

use Spiral\Migrations\Capsule;
use Spiral\Migrations\State;

abstract class MigratorTest extends BaseTest
{
    public function testConfigure(): void
    {
        $this->assertFalse($this->migrator->isConfigured());

        $this->migrator->configure();
        $this->assertTrue($this->db->hasTable('migrations'));
    }

    //no errors expected
    public function testConfigureTwice(): void
    {
        $this->assertFalse($this->migrator->isConfigured());

        $this->migrator->configure();
        $this->assertTrue($this->db->hasTable('migrations'));

        $this->migrator->configure();
    }

    public function testGetEmptyMigrations(): void
    {
        $this->migrator->configure();
        $this->assertSame([], $this->migrator->getMigrations());
    }

    public function testRepository(): void
    {
        $this->assertSame($this->repository, $this->migrator->getRepository());
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\MigrationException
     */
    public function testRunUnconfigured(): void
    {
        $this->migrator->run();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\MigrationException
     */
    public function testRollbackUnconfigured(): void
    {
        $this->migrator->rollback();
    }

    public function testCapsule(): void
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
    public function testCapsuleException(): void
    {
        $capsule = new Capsule($this->db);

        $capsule->execute([
            $this
        ]);
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\MigrationException
     */
    public function testNoState(): void
    {
        $x = new TestMigration();
        $x->up();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\MigrationException
     */
    public function testNoCapsule(): void
    {
        $x = new TestMigration();
        $x->getTable();
    }

    /**
     * @expectedException \Spiral\Migrations\Exception\MigrationException
     */
    public function testNoCapsule2(): void
    {
        $x = new TestMigration();
        $x->down();
    }

    public function testDatabase(): void
    {
        $x = new TestMigration();
        $this->assertSame($this->db, $x->withCapsule(new Capsule($this->db))->down());
    }

    public function testState(): void
    {
        $x = new TestMigration();

        $s = new State('name', new \DateTime());
        $this->assertSame($s, $x->withState($s)->up());
    }
}
