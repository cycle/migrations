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
 * @table(
 *     indexes={@index(columns={email},unique=true)}
 * )
 */
class User
{
    /**
     * @column(type=primary)
     * @var int
     */
    protected $id;

    /**
     * @column(type=string)
     * @var string
     */
    protected $email;
}
