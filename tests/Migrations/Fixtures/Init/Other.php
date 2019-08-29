<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Migrations\Tests\Fixtures\Init;

/**
 * @entity(database=secondary)
 */
class Other
{
    /**
     * @column(type=primary)
     * @var int
     */
    protected $id;

    /**
     * @column(type="enum(active,disabled)",castDefault=true)
     * @var string
     */
    protected $status;
}
