<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Migrations\Tests;

abstract class ReflectTest extends BaseTest
{
    public function testInit()
    {
        $tables = $this->migrate(__DIR__ . '/Fixtures/Init');

        $this->assertCount(2, $this->migrator->getMigrations());
        foreach ($this->migrator->getMigrations() as $m) {
            $this->migrator->run();
        }

        foreach ($tables as $t) {
            $this->assertSameAsInDB($t);
        }
    }

    public function testAlter()
    {
        $tables = $this->migrate(__DIR__ . '/Fixtures/Init');

        $this->assertCount(2, $this->migrator->getMigrations());
        foreach ($this->migrator->getMigrations() as $m) {
            $this->migrator->run();
        }

        foreach ($tables as $t) {
            $this->assertSameAsInDB($t);
        }

        $tables = $this->migrate(__DIR__ . '/Fixtures/Alter');

        $this->assertCount(4, $this->migrator->getMigrations());
        foreach ($this->migrator->getMigrations() as $m) {
            $this->migrator->run();
        }

        foreach ($tables as $t) {
            $this->assertSameAsInDB($t);
        }
    }
}
