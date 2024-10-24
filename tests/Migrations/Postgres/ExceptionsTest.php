<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Postgres;

/**
 * @group driver
 * @group driver-postgres
 */
class ExceptionsTest extends \Cycle\Migrations\Tests\ExceptionsTest
{
    public const DRIVER = 'postgres';
}
