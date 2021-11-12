<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Operation\Index;

use Cycle\Migrations\Exception\Operation\IndexException;
use Cycle\Migrations\Operation\Traits\OptionsTrait;
use Spiral\Migrations\CapsuleInterface as SpiralCapsuleInterface;
use Spiral\Migrations\Operation\Index\Add as SpiralAdd;

\interface_exists(SpiralCapsuleInterface::class);

final class Add extends Index
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
     * {@inheritDoc}
     */
    public function execute(SpiralCapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());

        if ($schema->hasIndex($this->columns)) {
            $columns = implode(',', $this->columns);
            throw new IndexException(
                "Unable to create index '{$schema->getName()}'.({$columns}), index already exists"
            );
        }

        $schema->index($this->columns)->unique(
            $this->getOption('unique', false)
        );

        if ($this->hasOption('name')) {
            $schema->index($this->columns)->setName($this->getOption('name'));
        }
    }
}
\class_alias(Add::class, SpiralAdd::class, false);
