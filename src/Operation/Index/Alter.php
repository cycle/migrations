<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Operation\Index;

use Cycle\Migrations\CapsuleInterface;
use Cycle\Migrations\Exception\Operation\IndexException;
use Cycle\Migrations\Operation\Traits\OptionsTrait;

final class Alter extends Index
{
    use OptionsTrait;

    /**
     * @param string $table
     * @param array  $columns
     * @param array  $options
     */
    public function __construct(string $table, array $columns, array $options = [])
    {
        parent::__construct($table, $columns);
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(CapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());

        if (!$schema->hasIndex($this->columns)) {
            $columns = implode(',', $this->columns);
            throw new IndexException(
                "Unable to alter index '{$schema->getName()}'.({$columns}), no such index"
            );
        }

        $schema->index($this->columns)->unique(
            $this->getOption('unique', false)
        );
    }
}
