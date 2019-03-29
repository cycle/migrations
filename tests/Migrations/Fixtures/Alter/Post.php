<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Migrations\Tests\Fixtures\Alter;

/**
 * @entity
 */
class Post
{
    /**
     * @column(type=primary)
     * @var int
     */
    protected $id;

    /**
     * @refersTo(target=Other,nullable=true)
     * @var Other
     */
    protected $other;
}