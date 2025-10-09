<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\MySQL;

use Cycle\Database\Driver\MySQL\Schema\MySQLIndex;
use Cycle\Database\Schema\Comparator;
use Cycle\Database\Schema\State;
use Cycle\Migrations\Atomizer\Renderer;
use Spiral\Reactor\Partial\Method;

/**
 * @group driver
 * @group driver-mysql
 */
class RendererTest extends \Cycle\Migrations\Tests\RendererTest
{
    public const DRIVER = 'mysql';

    public function testDeclareIndexes(): void
    {
        $method = new Method('up');

        $columns = ['email', 'username'];

        $indexA = new MySQLIndex('table', 'idx_email_username');
        $indexA->columns($columns);
        $initial = new State('test_table');
        $initial->registerIndex($indexA);


        $indexB = new MySQLIndex('table', 'idx_email_username');
        $indexB->columns(\array_reverse($columns));
        $current = new State('test_table');
        $current->registerIndex($indexB);

        $comparator = new Comparator($initial, $current);

        $renderer = new Renderer();

        $reflectionMethod = new \ReflectionMethod($renderer, 'declareIndexes');
        $reflectionMethod->setAccessible(true);

        /**
         * Method $method, Comparator $comparator
         */
        $reflectionMethod->invoke($renderer, $method, $comparator);

        self::assertSame('', $method->getBody());
    }
}
