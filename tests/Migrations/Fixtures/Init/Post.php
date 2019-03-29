<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Migrations\Tests\Fixtures\Init;

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
     * @belongsTo(target=User,nullable=false)
     * @var User
     */
    protected $user;
}