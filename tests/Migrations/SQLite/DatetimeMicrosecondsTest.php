<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\SQLite;

/**
 * @group driver
 * @group driver-sqlite
 */
class DatetimeMicrosecondsTest extends \Cycle\Migrations\Tests\DatetimeMicrosecondsTest
{
    public const DRIVER = 'sqlite';
}
