<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Postgres;

class AtomizerTest extends \Cycle\Migrations\Tests\AtomizerTest
{
    public const DRIVER = 'postgres';

    public function testChangeBinaryColumnSize(): void
    {
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->bit('value', size: 16);
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        $this->assertSame(16, $this->schema('sample')->column('value')->getSize());

        $schema = $this->schema('sample');
        $schema->bit('value', size: 255);
        $this->atomize('migration2', [$schema]);

        $this->migrator->run();

        $this->assertSame(255, $this->schema('sample')->column('value')->getSize());

        $this->assertTrue($this->db->hasTable('sample'));

        $this->migrator->rollback();
        $this->assertSame(16, $this->schema('sample')->column('value')->getSize());

        $this->assertTrue($this->db->hasTable('sample'));

        $this->migrator->rollback();
        $this->assertFalse($this->db->hasTable('sample'));
    }
}
