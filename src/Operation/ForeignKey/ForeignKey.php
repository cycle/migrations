<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Operation\ForeignKey;

use Spiral\Migrations\Operation\AbstractOperation;

abstract class ForeignKey extends AbstractOperation
{
    /**
     * Some options has set of aliases.
     *
     * @var array
     */
    protected $aliases = [
        'onDelete' => ['delete'],
        'onUpdate' => ['update']
    ];

    /**
     * Column foreign key associated to.
     *
     * @var string
     */
    protected $column = '';

    /**
     * @param string $table
     * @param string $column
     */
    public function __construct(string $table, string $column)
    {
        parent::__construct($table);
        $this->column = $column;
    }
}