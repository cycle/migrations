<?php

declare(strict_types=1);

namespace Cycle\Migrations\V2;

use Cycle\Database\Schema\AbstractTable;
use Cycle\Migrations\CapsuleInterface;
use Cycle\Migrations\Exception\BlueprintException;
use Cycle\Migrations\OperationInterface;
use Cycle\Migrations\Operation;

final class TableBlueprint
{
    protected string $tableName = '';
    private CapsuleInterface $capsule;
    private array $operations = [];
    private bool $executed = false;

    public function __construct(string $tableName, CapsuleInterface $capsule)
    {
        $this->tableName = $tableName;
        $this->capsule = $capsule;
    }

    public function getSchema(): AbstractTable
    {
        return $this->capsule->getSchema($this->tableName);
    }

    public function addColumns(array $columns): self
    {
        foreach ($columns as $name => $column) {
            $columnParser = new ColumnParser($column);
            $this->addOperation(
                new Operation\Column\Add(
                    $this->tableName,
                    $name,
                    $columnParser->getType(),
                    $columnParser->getOptions()
                )
            );
        }

        return $this;
    }

    public function alterColumns(array $columns): self
    {
        foreach ($columns as $name => $column) {
            $columnParser = new ColumnParser($column);
            $this->addOperation(
                new Operation\Column\Alter(
                    $this->tableName,
                    $name,
                    $columnParser->getType(),
                    $columnParser->getOptions()
                )
            );
        }

        return $this;
    }

    public function renameColumn(string $name, string $newName): self
    {
        return $this->addOperation(
            new Operation\Column\Rename($this->tableName, $name, $newName)
        );
    }

    public function dropColumns(array $columnsName = []): self
    {
        foreach ($columnsName as $columnName) {
            $this->addOperation(
                new Operation\Column\Drop($this->tableName, $columnName)
            );
        }

        return $this;
    }

    public function addIndexes(array $indexes): self
    {
        foreach ($indexes as $index) {
            $indexParser = new IndexParser($index, $this->tableName);

            $this->addOperation(
                new Operation\Index\Add(
                    $this->tableName,
                    $indexParser->getField(),
                    $indexParser->getOptions()
                )
            );
        }

        return $this;
    }

    public function alterIndexes(array $indexes): self
    {
        foreach ($indexes as $index) {
            $indexParser = new IndexParser($index, $this->tableName);

            $this->addOperation(
                new Operation\Index\Alter(
                    $this->tableName,
                    $indexParser->getField(),
                    $indexParser->getOptions()
                )
            );
        }

        return $this;
    }

    /**
     * @example [['email'], ['phone', 'created_at']] - drop two indexes
     * @param array $indexes
     * @return $this
     */
    public function dropIndexesByColumns(array $indexes): self
    {
        foreach ($indexes as $columns) {
            $this->addOperation(
                new Operation\Index\Drop($this->tableName, $columns)
            );
        }

        return $this;
    }

    public function addForeignKeys(array $foreignKeys): self
    {
        foreach ($foreignKeys as $foreignKey) {
            $fkParser = new ForeignKeyParser($foreignKey, $this->tableName);

            $this->addOperation(
                new Operation\ForeignKey\Add(
                    $this->tableName,
                    $fkParser->getInnerKeys(),
                    $fkParser->getOuterTable(),
                    $fkParser->getOuterKeys(),
                    $fkParser->getOptions()
                )
            );
        }

        return $this;
    }

    /**
     * @example [['email'], ['phone', 'created_at']] - drop two foreignKeys
     * @param array $foreignKeys
     * @return $this
     */
    public function dropForeignKeysByColumns(array $foreignKeys): self
    {
        foreach ($foreignKeys as $columns) {
            $this->addOperation(
                new Operation\ForeignKey\Drop($this->tableName, $columns)
            );
        }

        return $this;
    }

    public function setPrimaryKeys(array $keys): self
    {
        return $this->addOperation(
            new Operation\Table\PrimaryKeys($this->tableName, $keys)
        );
    }

    public function create(): void
    {
        $this->addOperation(
            new Operation\Table\Create($this->tableName)
        );

        $this->execute();
    }

    public function update(): void
    {
        $this->addOperation(
            new Operation\Table\Update($this->tableName)
        );

        $this->execute();
    }

    public function rename(string $newName): void
    {
        $this->addOperation(
            new Operation\Table\Rename($this->tableName, $newName)
        );

        $this->execute();
    }

    public function drop(): void
    {
        $this->addOperation(
            new Operation\Table\Drop($this->tableName)
        );

        $this->execute();
    }

    public function addOperation(OperationInterface $operation): self
    {
        $this->operations[] = $operation;

        return $this;
    }

    private function execute(): void
    {
        if ($this->executed) {
            throw new BlueprintException('Only one create/update/rename/drop is allowed per blueprint.');
        }

        $this->capsule->execute($this->operations);
        $this->executed = true;
    }
}
