<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\MySQL;

use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-mysql')]
class MigratorTest extends \Cycle\Migrations\Tests\MigratorTest
{
    public const DRIVER = 'mysql';
}
