<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Operation\ForeignKey;

use Cycle\Database\ForeignKeyInterface;
use Spiral\Migrations\CapsuleInterface as SpiralCapsuleInterface;
use Cycle\Migrations\Exception\Operation\ForeignKeyException;
use Cycle\Migrations\Operation\Traits\OptionsTrait;

final class Alter extends ForeignKey
{
    use OptionsTrait;

    /** @var string */
    protected $foreignTable;

    /** @var array */
    protected $foreignKeys;

    /**
     * @param string $table
     * @param array  $columns
     * @param string $foreignTable
     * @param array  $foreignKeys
     * @param array  $options
     */
    public function __construct(
        string $table,
        array $columns,
        string $foreignTable,
        array $foreignKeys,
        array $options
    ) {
        parent::__construct($table, $columns);
        $this->foreignTable = $foreignTable;
        $this->foreignKeys = $foreignKeys;
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(SpiralCapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());

        if (!$schema->hasForeignKey($this->columns)) {
            throw new ForeignKeyException(
                "Unable to alter foreign key '{$schema->getName()}'.({$this->columnNames()}), "
                . 'key does not exists'
            );
        }

        $outerSchema = $capsule->getSchema($this->foreignTable);

        if ($this->foreignTable != $this->table && !$outerSchema->exists()) {
            throw new ForeignKeyException(
                "Unable to alter foreign key '{$schema->getName()}'.'{$this->columnNames()}', "
                . "foreign table '{$this->foreignTable}' does not exists"
            );
        }


        foreach ($this->foreignKeys as $fk) {
            if ($this->foreignTable != $this->table && !$outerSchema->hasColumn($fk)) {
                throw new ForeignKeyException(
                    "Unable to alter foreign key '{$schema->getName()}'.'{$this->columnNames()}',"
                    . " foreign column '{$this->foreignTable}'.'{$fk}' does not exists"
                );
            }
        }

        $foreignKey = $schema->foreignKey($this->columns)->references(
            $this->foreignTable,
            $this->foreignKeys
        );

        /*
         * We are allowing both formats "NO_ACTION" and "NO ACTION".
         */

        $foreignKey->onDelete(
            str_replace(
                '_',
                ' ',
                $this->getOption('delete', ForeignKeyInterface::NO_ACTION)
            )
        );

        $foreignKey->onUpdate(
            str_replace(
                '_',
                ' ',
                $this->getOption('update', ForeignKeyInterface::NO_ACTION)
            )
        );
    }
}
