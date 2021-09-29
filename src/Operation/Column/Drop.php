<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Operation\Column;

use Cycle\Migrations\CapsuleInterface;
use Cycle\Migrations\Exception\Operation\ColumnException;
use Cycle\Migrations\Operation\AbstractOperation;

final class Drop extends AbstractOperation
{
    /**
     * Column name.
     *
     * @var string
     */
    private $name;

    /**
     * @param string $table
     * @param string $name
     */
    public function __construct(string $table, string $name)
    {
        parent::__construct($table);
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(CapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());

        if (!$schema->hasColumn($this->name)) {
            throw new ColumnException(
                "Unable to drop column '{$schema->getName()}'.'{$this->name}', column does not exists"
            );
        }

        //Declaring column
        $schema->dropColumn($this->name);
    }
}
