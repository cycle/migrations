<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\SQLServer;

use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-sqlserver')]
class ComplexAtomizerTest extends \Cycle\Migrations\Tests\ComplexAtomizerTest
{
    public const DRIVER = 'sqlserver';
}
