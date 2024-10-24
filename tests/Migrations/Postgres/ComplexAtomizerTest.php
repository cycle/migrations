<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Postgres;

/**
 * @group driver
 * @group driver-postgres
 */
class ComplexAtomizerTest extends \Cycle\Migrations\Tests\ComplexAtomizerTest
{
    public const DRIVER = 'postgres';

    public function testTableSorting(): void
    {
        $this->migrator->configure();

        $schema = $this->schema('messages');
        $schema->primary('id');
        $schema->integer('user_id');
        $schema->integer('thread_id');
        $schema->foreignKey(['thread_id'])->references('threads', ['id']);
        $schema->foreignKey(['user_id'])->references('users', ['id']);

        $schema1 = $this->schema('users');
        $schema1->primary('id');

        $schema2 = $this->schema('threads');
        $schema2->primary('id');

        $this->atomize('migration1', [$schema, $schema1, $schema2]);
        $this->migrator->run();

        $this->assertTrue($this->db->hasTable('messages'));
        $this->assertTrue($this->db->table('messages')->hasForeignKey(['thread_id']));
        $this->assertTrue($this->db->table('messages')->hasForeignKey(['user_id']));

        $this->assertTrue($this->db->hasTable('users'));
        $this->assertTrue($this->db->hasTable('threads'));

        $this->migrator->rollback();

        $this->assertFalse($this->db->hasTable('messages'));
        $this->assertFalse($this->db->hasTable('users'));
        $this->assertFalse($this->db->hasTable('threads'));
    }
}
