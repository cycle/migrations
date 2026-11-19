<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\SQLite;

use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-sqlite')]
class AtomizerTest extends \Cycle\Migrations\Tests\AtomizerTest
{
    public const DRIVER = 'sqlite';
}
