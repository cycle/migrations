<?php

declare(strict_types=1);

namespace Cycle\Migrations\Operation\ForeignKey;

use Cycle\Database\ForeignKeyInterface;
use Cycle\Migrations\CapsuleInterface;
use Cycle\Migrations\Exception\Operation\ForeignKeyException;
use Cycle\Migrations\Operation\Traits\OptionsTrait;

final class Add extends ForeignKey
{
    use OptionsTrait;

    public function __construct(
        string $table,
        array $columns,
        private string $foreignTable,
        private array $foreignKeys,
        array $options,
    ) {
        $this->options = $options;
        parent::__construct($table, $columns);
    }

    public function execute(CapsuleInterface $capsule): void
    {
        $schema = $capsule->getSchema($this->getTable());

        if ($schema->hasForeignKey($this->columns)) {
            throw new ForeignKeyException(
                "Unable to add foreign key '{$schema->getName()}'.({$this->columnNames()}), "
                . 'foreign key already exists',
            );
        }

        $foreignSchema = $capsule->getSchema($this->foreignTable);

        if ($this->foreignTable !== $this->table && !$foreignSchema->exists()) {
            throw new ForeignKeyException(
                "Unable to add foreign key '{$schema->getName()}'.'{$this->columnNames()}', "
                . "foreign table '{$this->foreignTable}' does not exists",
            );
        }

        foreach ($this->foreignKeys as $fk) {
            if ($this->foreignTable !== $this->table && !$foreignSchema->hasColumn($fk)) {
                throw new ForeignKeyException(
                    "Unable to add foreign key '{$schema->getName()}'.'{$this->columnNames()}',"
                    . " foreign column '{$this->foreignTable}'.'{$fk}' does not exists",
                );
            }
        }

        $foreignKey = $schema->foreignKey($this->columns, $this->getOption('indexCreate', true))->references(
            $this->foreignTable,
            $this->foreignKeys,
        );

        if ($this->hasOption('name')) {
            $foreignKey->setName($this->getOption('name'));
        }

        /*
         * We are allowing both formats "NO_ACTION" and "NO ACTION".
         */

        $foreignKey->onDelete(
            \str_replace(
                '_',
                ' ',
                $this->getOption('delete', ForeignKeyInterface::NO_ACTION),
            ),
        );

        $foreignKey->onUpdate(
            \str_replace(
                '_',
                ' ',
                $this->getOption('update', ForeignKeyInterface::NO_ACTION),
            ),
        );
    }
}
