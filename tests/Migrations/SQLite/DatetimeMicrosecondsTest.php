<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\SQLite;

use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-sqlite')]
class DatetimeMicrosecondsTest extends \Cycle\Migrations\Tests\DatetimeMicrosecondsTest
{
    public const DRIVER = 'sqlite';
}
