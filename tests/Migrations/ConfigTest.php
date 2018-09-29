<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Migrations\Config\MigrationConfig;

class ConfigTest extends TestCase
{

    public function testNotSafe()
    {
        $c = new MigrationConfig([
            'safe' => false
        ]);

        $this->assertFalse($c->isSafe());
    }

    public function testSafe()
    {
        $c = new MigrationConfig([
            'safe' => true
        ]);

        $this->assertTrue($c->isSafe());
    }
}