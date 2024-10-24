<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Postgres;

/**
 * @group driver
 * @group driver-postgres
 */
final class TableSorterTest extends \Cycle\Migrations\Tests\TableSorterTest
{
    public const DRIVER = 'postgres';
}
