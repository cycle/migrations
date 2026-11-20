<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\MySQL;

use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-mysql')]
final class TableSorterTest extends \Cycle\Migrations\Tests\TableSorterTest
{
    public const DRIVER = 'mysql';
}
