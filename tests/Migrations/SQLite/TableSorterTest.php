<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\SQLite;

use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-sqlite')]
final class TableSorterTest extends \Cycle\Migrations\Tests\TableSorterTest
{
    public const DRIVER = 'sqlite';
}
