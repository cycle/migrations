<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Migrations\Operation\Column;

use Spiral\Migrations\CapsuleInterface;
use Spiral\Migrations\Exception\Operation\ColumnException;
use Spiral\Migrations\Operation\AbstractOperation;

final class Drop extends AbstractOperation
{
    /**
     * Column name.
     *
     * @var string
     */
    private $name = '';

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
