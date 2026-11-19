<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Postgres;

use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-postgres')]
final class TableSorterTest extends \Cycle\Migrations\Tests\TableSorterTest
{
    public const DRIVER = 'postgres';
}
