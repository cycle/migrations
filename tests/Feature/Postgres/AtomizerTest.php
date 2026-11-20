<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Feature\Postgres;

use Cycle\Migrations\Tests\Support\MigrationsTestCase;
use Testo\Assert;
use Testo\Test;

final class AtomizerTest extends MigrationsTestCase
{
    #[Test]
    public function testChangeBitColumnSize(): void
    {
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->bit('value', size: 16);
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        Assert::same($this->schema('sample')->column('value')->getSize(), 16);

        $schema = $this->schema('sample');
        $schema->bit('value', size: 255);
        $this->atomize('migration2', [$schema]);

        $this->migrator->run();

        Assert::same($this->schema('sample')->column('value')->getSize(), 255);

        Assert::true($this->db->hasTable('sample'));

        $this->migrator->rollback();
        Assert::same($this->schema('sample')->column('value')->getSize(), 16);

        Assert::true($this->db->hasTable('sample'));

        $this->migrator->rollback();
        Assert::false($this->db->hasTable('sample'));
    }
}
