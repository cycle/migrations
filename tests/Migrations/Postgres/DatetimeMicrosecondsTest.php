<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Postgres;

/**
 * @group driver
 * @group driver-postgres
 */
class DatetimeMicrosecondsTest extends \Cycle\Migrations\Tests\DatetimeMicrosecondsTest
{
    public const DRIVER = 'postgres';
}
