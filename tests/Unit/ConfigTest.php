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
use Testo\Assert;
use Testo\Test;

#[Test]
final class ConfigTest
{
    public function testNotSafe(): void
    {
        $c = new MigrationConfig([
            'safe' => false,
        ]);

        Assert::false($c->isSafe());
    }

    public function testSafe(): void
    {
        $c = new MigrationConfig([
            'safe' => true,
        ]);

        Assert::true($c->isSafe());
    }
}
