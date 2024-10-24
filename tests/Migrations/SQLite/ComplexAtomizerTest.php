<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\SQLite;

/**
 * @group driver
 * @group driver-sqlite
 */
class ComplexAtomizerTest extends \Cycle\Migrations\Tests\ComplexAtomizerTest
{
    public const DRIVER = 'sqlite';
}
