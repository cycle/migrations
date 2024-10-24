<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\MySQL;

/**
 * @group driver
 * @group driver-mysql
 */
class ExceptionsTest extends \Cycle\Migrations\Tests\ExceptionsTest
{
    public const DRIVER = 'mysql';
}
