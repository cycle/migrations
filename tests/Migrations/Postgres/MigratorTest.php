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
class MigratorTest extends \Cycle\Migrations\Tests\MigratorTest
{
    public const DRIVER = 'postgres';
}
