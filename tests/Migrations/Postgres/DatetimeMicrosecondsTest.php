<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Postgres;

use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-postgres')]
class DatetimeMicrosecondsTest extends \Cycle\Migrations\Tests\DatetimeMicrosecondsTest
{
    public const DRIVER = 'postgres';
}
