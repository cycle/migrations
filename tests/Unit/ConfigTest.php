<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Unit;

use Cycle\Migrations\Config\MigrationConfig;

final class ConfigTest
{
    #[\Testo\Test]
    public function testNotSafe(): void
    {
        $c = new MigrationConfig([
            'safe' => false,
        ]);

        \Testo\Assert::false($c->isSafe());
    }

    #[\Testo\Test]
    public function testSafe(): void
    {
        $c = new MigrationConfig([
            'safe' => true,
        ]);

        \Testo\Assert::true($c->isSafe());
    }
}
