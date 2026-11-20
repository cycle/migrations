<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Postgres;

use PHPUnit\Framework\Attributes\Group;

#[\Testo\Filter\Group('driver', 'driver-postgres')]
class ComplexAtomizerTest extends \Cycle\Migrations\Tests\ComplexAtomizerTest
{
    public const DRIVER = 'postgres';
    #[\Testo\Test]
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

        \Testo\Assert::true($this->db->hasTable('messages'));
        \Testo\Assert::true($this->db->table('messages')->hasForeignKey(['thread_id']));
        \Testo\Assert::true($this->db->table('messages')->hasForeignKey(['user_id']));

        \Testo\Assert::true($this->db->hasTable('users'));
        \Testo\Assert::true($this->db->hasTable('threads'));

        $this->migrator->rollback();

        \Testo\Assert::false($this->db->hasTable('messages'));
        \Testo\Assert::false($this->db->hasTable('users'));
        \Testo\Assert::false($this->db->hasTable('threads'));
    }
}
