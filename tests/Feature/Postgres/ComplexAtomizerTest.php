<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Feature\Postgres;

use Cycle\Migrations\Tests\MigrationsTestCase;
use Testo\Assert;
use Testo\Test;

final class ComplexAtomizerTest extends MigrationsTestCase
{
    #[Test]
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

        Assert::true($this->db->hasTable('messages'));
        Assert::true($this->db->table('messages')->hasForeignKey(['thread_id']));
        Assert::true($this->db->table('messages')->hasForeignKey(['user_id']));

        Assert::true($this->db->hasTable('users'));
        Assert::true($this->db->hasTable('threads'));

        $this->migrator->rollback();

        Assert::false($this->db->hasTable('messages'));
        Assert::false($this->db->hasTable('users'));
        Assert::false($this->db->hasTable('threads'));
    }
}
