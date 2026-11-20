<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\SQLite;

use Cycle\Migrations\Capsule;
use Cycle\Migrations\TableBlueprint;
use PHPUnit\Framework\Attributes\Group;

#[\Testo\Filter\Group('driver', 'driver-sqlite')]
class BlueprintTest extends \Cycle\Migrations\Tests\BlueprintTest
{
    public const DRIVER = 'sqlite';
    #[\Testo\Test]
    public function testCreateForeignWithoutIndex(): void
    {
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample1');

        $blueprint->addColumn('id', 'primary')->create();
        $blueprint = new TableBlueprint(new Capsule($this->db), 'sample');

        $blueprint->addColumn('id', 'primary')
            ->addColumn('sample_id', 'int')
            ->addForeignKey(['sample_id'], 'sample1', ['id'], ['indexCreate' => false])
            ->create();

        \Testo\Assert::false($blueprint->getSchema()->hasIndex(['sample_id']));
    }
}
