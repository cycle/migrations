<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Operation\Column;

use Cycle\Migrations\Exception\Operation\ColumnException;
use Cycle\Migrations\Operation\AbstractOperation;
use Spiral\Migrations\CapsuleInterface as SpiralCapsuleInterface;
use Spiral\Migrations\Operation\Column\Rename as SpiralRename;

\interface_exists(SpiralCapsuleInterface::class);

final class Rename extends AbstractOperation
{
    /** @var string */
    private $name = '';

    /** @var string */
    private $newName = '';

    /**
     * @param string $table
     * @param string $name
     * @param string $newName
     */
    public function __construct(string $table, string $name, string $newName)
    {
        parent::__construct($table);
        $this->name = $name;
        $this->newName = $newName;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(SpiralCapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());

        if (!$schema->hasColumn($this->name)) {
            throw new ColumnException(
                "Unable to rename column '{$schema->getName()}'.'{$this->name}', column does not exists"
            );
        }

        if ($schema->hasColumn($this->newName)) {
            throw new ColumnException(
                sprintf(
                    "Unable to rename column '%s'.'%s', column '%s' already exists",
                    $schema->getName(),
                    $this->name,
                    $this->newName
                )
            );
        }

        //Declaring column
        $schema->renameColumn($this->name, $this->newName);
    }
}
\class_alias(Rename::class, SpiralRename::class, false);
