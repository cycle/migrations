<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Operation;

use Spiral\Migrations\OperationInterface;

abstract class AbstractOperation implements OperationInterface
{
    /** @var string */
    protected $table;

    /**
     * @param string $table
     */
    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * {@inheritdoc}
     */
    public function getTable(): string
    {
        return $this->table;
    }
}