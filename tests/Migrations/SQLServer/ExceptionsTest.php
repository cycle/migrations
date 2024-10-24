<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\SQLServer;

/**
 * @group driver
 * @group driver-sqlserver
 */
class ExceptionsTest extends \Cycle\Migrations\Tests\ExceptionsTest
{
    public const DRIVER = 'sqlserver';
}
