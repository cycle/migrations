<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests;

use PHPUnit\Framework\TestCase;
use Cycle\Migrations\Config\MigrationConfig;

class ConfigTest extends TestCase
{
    public function testNotSafe(): void
    {
        $c = new MigrationConfig([
            'safe' => false,
        ]);

        $this->assertFalse($c->isSafe());
    }

    public function testSafe(): void
    {
        $c = new MigrationConfig([
            'safe' => true,
        ]);

        $this->assertTrue($c->isSafe());
    }
}
