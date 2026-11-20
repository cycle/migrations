<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\SQLServer;

use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-sqlserver')]
final class TableSorterTest extends \Cycle\Migrations\Tests\TableSorterTest
{
    public const DRIVER = 'sqlserver';
}
