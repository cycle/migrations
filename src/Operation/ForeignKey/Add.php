<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Migrations\Operation\ForeignKey;

use Spiral\Database\ForeignKeyInterface;
use Spiral\Migrations\CapsuleInterface;
use Spiral\Migrations\Exception\Operation\ForeignKeyException;
use Spiral\Migrations\Operation\Traits\OptionsTrait;

final class Add extends ForeignKey
{
    use OptionsTrait;

    /** @var string */
    protected $foreignTable = '';

    /** @var array */
    protected $foreignKeys = [];

    /**
     * AddReference constructor.
     *
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
     * {@inheritdoc}
     */
    public function execute(CapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());

        if ($schema->hasForeignKey($this->columns)) {
            throw new ForeignKeyException(
                "Unable to add foreign key '{$schema->getName()}'.({$this->columnNames()}), "
                . 'foreign key already exists'
            );
        }

        $foreignSchema = $capsule->getSchema($this->foreignTable);

        if ($this->foreignTable !== $this->table && !$foreignSchema->exists()) {
            throw new ForeignKeyException(
                "Unable to add foreign key '{$schema->getName()}'.'{$this->columnNames()}', "
                . "foreign table '{$this->foreignTable}' does not exists"
            );
        }

        foreach ($this->foreignKeys as $fk) {
            if ($this->foreignTable !== $this->table && !$foreignSchema->hasColumn($fk)) {
                throw new ForeignKeyException(
                    "Unable to add foreign key '{$schema->getName()}'.'{$this->columnNames()}',"
                    . " foreign column '{$this->foreignTable}'.'{$fk}' does not exists"
                );
            }
        }

        $foreignKey = $schema->foreignKey($this->columns)->references(
            $this->foreignTable,
            $this->foreignKeys
        );

        if ($this->hasOption('name')) {
            $foreignKey->setName($this->getOption('name'));
        }

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
