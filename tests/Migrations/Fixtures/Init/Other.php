<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Fixtures\Init;

/**
 * @entity(database=secondary)
 */
class Other
{
    /**
     * @column(type=primary)
     *
     * @var int
     */
    protected $id;

    /**
     * @column(type="enum(active,disabled)",castDefault=true)
     *
     * @var string
     */
    protected $status;
}
