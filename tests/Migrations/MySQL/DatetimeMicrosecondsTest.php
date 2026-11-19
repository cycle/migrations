<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\MySQL;

use PHPUnit\Framework\Attributes\Group;

#[Group('driver')]
#[Group('driver-mysql')]
class DatetimeMicrosecondsTest extends \Cycle\Migrations\Tests\DatetimeMicrosecondsTest
{
    public const DRIVER = 'mysql';
}
