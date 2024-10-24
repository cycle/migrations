<?php

declare(strict_types=1);

namespace Cycle\Migrations\Operation\ForeignKey;

use Cycle\Database\ForeignKeyInterface;
use Cycle\Migrations\CapsuleInterface;
use Cycle\Migrations\Exception\Operation\ForeignKeyException;
use Cycle\Migrations\Operation\Traits\OptionsTrait;

final class Alter extends ForeignKey
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

        if (!$schema->hasForeignKey($this->columns)) {
            throw new ForeignKeyException(
                "Unable to alter foreign key '{$schema->getName()}'.({$this->columnNames()}), "
                . 'key does not exists',
            );
        }

        $outerSchema = $capsule->getSchema($this->foreignTable);

        if ($this->foreignTable != $this->table && !$outerSchema->exists()) {
            throw new ForeignKeyException(
                "Unable to alter foreign key '{$schema->getName()}'.'{$this->columnNames()}', "
                . "foreign table '{$this->foreignTable}' does not exists",
            );
        }


        foreach ($this->foreignKeys as $fk) {
            if ($this->foreignTable != $this->table && !$outerSchema->hasColumn($fk)) {
                throw new ForeignKeyException(
                    "Unable to alter foreign key '{$schema->getName()}'.'{$this->columnNames()}',"
                    . " foreign column '{$this->foreignTable}'.'{$fk}' does not exists",
                );
            }
        }

        $foreignKey = $schema->foreignKey($this->columns)->references(
            $this->foreignTable,
            $this->foreignKeys,
        );

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
