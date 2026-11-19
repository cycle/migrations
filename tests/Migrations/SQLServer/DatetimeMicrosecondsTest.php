<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\SQLServer;

use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-sqlserver')]
class DatetimeMicrosecondsTest extends \Cycle\Migrations\Tests\DatetimeMicrosecondsTest
{
    public const DRIVER = 'sqlserver';
}
