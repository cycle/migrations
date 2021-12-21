<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Fixtures\Alter;

/**
 * @entity
 */
class User
{
    /**
     * @column(type=primary)
     *
     * @var int
     */
    protected $id;
}
