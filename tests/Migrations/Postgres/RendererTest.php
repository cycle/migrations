<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Postgres;

/**
 * @group driver
 * @group driver-postgres
 */
class RendererTest extends \Cycle\Migrations\Tests\RendererTest
{
    public const DRIVER = 'postgres';
}
