<?php

declare(strict_types=1);

namespace Cycle\Migrations\Atomizer;

use Cycle\Database\Schema\AbstractColumn;
use Cycle\Database\Schema\AbstractForeignKey;
use Cycle\Database\Schema\AbstractIndex;
use Cycle\Database\Schema\AbstractTable;
use Cycle\Database\Schema\Comparator;
use Spiral\Reactor\Partial\Method;

final class Renderer implements RendererInterface
{
    /**
     * Comparator alteration states.
     */
    public const NEW_STATE = 0;

    public const ORIGINAL_STATE = 1;

    public function createTable(Method $method, AbstractTable $table): void
    {
        $method->addBody('$this->table(?)', [$this->getTableName($table)]);

        $comparator = $table->getComparator();

        $this->declareColumns($method, $comparator);
        $this->declareIndexes($method, $comparator);
        $this->declareForeignKeys($method, $comparator, $table->getPrefix());

        if (\count($table->getPrimaryKeys())) {
            $method->addBody('->setPrimaryKeys(?)', [$table->getPrimaryKeys()]);
        }

        //Finalization
        $method->addBody('->create();');
    }

    public function updateTable(Method $method, AbstractTable $table): void
    {
        $method->addBody('$this->table(?)', [$this->getTableName($table)]);
        $comparator = $table->getComparator();

        if ($comparator->isPrimaryChanged()) {
            $method->addBody('->setPrimaryKeys(?)', [$table->getPrimaryKeys()]);
        }

        $this->declareColumns($method, $comparator);
        $this->declareIndexes($method, $comparator);
        $this->declareForeignKeys($method, $comparator, $table->getPrefix());

        //Finalization
        $method->addBody('->update();');
    }

    public function revertTable(Method $method, AbstractTable $table): void
    {
        //Get table blueprint
        $method->addBody('$this->table(?)', [$this->getTableName($table)]);
        $comparator = $table->getComparator();

        $this->revertForeignKeys($method, $comparator, $table->getPrefix());
        $this->revertIndexes($method, $comparator);
        $this->revertColumns($method, $comparator);

        //Finalization
        $method->addBody('->update();');
    }

    public function dropTable(Method $method, AbstractTable $table): void
    {
        $method->addBody('$this->table(?)->drop();', [$this->getTableName($table)]);
    }

    protected function alterColumn(
        Method $method,
        AbstractColumn $column,
        AbstractColumn $original,
    ): void {
        if ($column->getName() !== $original->getName()) {
            $name = $original->getName();
        } else {
            $name = $column->getName();
        }

        $method->addBody('->alterColumn(?, ?, ?)', [
            $name,
            $column->getDeclaredType() ?? $column->getAbstractType(),
            $this->columnOptions($column),
        ]);

        if ($column->getName() !== $original->getName()) {
            $method->addBody('->renameColumn(?, ?)', [
                $name,
                $column->getName(),
            ]);
        }
    }

    private function declareColumns(Method $method, Comparator $comparator): void
    {
        foreach ($comparator->addedColumns() as $column) {
            $method->addBody('->addColumn(?, ?, ?)', [
                $column->getName(),
                $column->getDeclaredType() ?? $column->getAbstractType(),
                $this->columnOptions($column),
            ]);
        }

        foreach ($comparator->alteredColumns() as $pair) {
            $this->alterColumn(
                $method,
                $pair[self::NEW_STATE],
                $pair[self::ORIGINAL_STATE],
            );
        }

        foreach ($comparator->droppedColumns() as $column) {
            $method->addBody('->dropColumn(?)', [$column->getName()]);
        }
    }

    private function declareIndexes(Method $method, Comparator $comparator): void
    {
        foreach ($comparator->addedIndexes() as $index) {
            $method->addBody('->addIndex(?, ?)', [$index->getColumns(), $this->indexOptions($index)]);
        }

        foreach ($comparator->alteredIndexes() as $pair) {
            /** @var AbstractIndex $index */
            $index = $pair[self::NEW_STATE];
            $method->addBody('->alterIndex(?, ?)', [$index->getColumns(), $this->indexOptions($index)]);
        }

        foreach ($comparator->droppedIndexes() as $index) {
            $method->addBody('->dropIndex(?)', [$index->getColumns()]);
        }
    }

    /**
     * @param string $prefix Database isolation prefix
     */
    private function declareForeignKeys(Method $method, Comparator $comparator, string $prefix = ''): void
    {
        foreach ($comparator->addedForeignKeys() as $key) {
            $method->addBody('->addForeignKey(?, ?, ?, ?)', [
                $key->getColumns(),
                \substr($key->getForeignTable(), \strlen($prefix)),
                $key->getForeignKeys(),
                $this->foreignKeyOptions($key),
            ]);
        }

        foreach ($comparator->alteredForeignKeys() as $pair) {
            /** @var AbstractForeignKey $key */
            $key = $pair[self::NEW_STATE];
            $method->addBody('->alterForeignKey(?, ?, ?, ?)', [
                $key->getColumns(),
                \substr($key->getForeignTable(), \strlen($prefix)),
                $key->getForeignKeys(),
                $this->foreignKeyOptions($key),
            ]);
        }

        foreach ($comparator->droppedForeignKeys() as $key) {
            $method->addBody('->dropForeignKey(?)', [$key->getColumns()]);
        }
    }

    private function revertColumns(Method $method, Comparator $comparator): void
    {
        foreach ($comparator->droppedColumns() as $column) {
            $method->addBody('->addColumn(?, ?, ?)', [
                $column->getName(),
                $column->getDeclaredType() ?? $column->getAbstractType(),
                $this->columnOptions($column),
            ]);
        }

        foreach ($comparator->alteredColumns() as $pair) {
            $this->alterColumn(
                $method,
                $pair[self::ORIGINAL_STATE],
                $pair[self::NEW_STATE],
            );
        }

        foreach ($comparator->addedColumns() as $column) {
            $method->addBody('->dropColumn(?)', [$column->getName()]);
        }
    }

    private function revertIndexes(Method $method, Comparator $comparator): void
    {
        foreach ($comparator->droppedIndexes() as $index) {
            $method->addBody('->addIndex(?, ?)', [$index->getColumns(), $this->indexOptions($index)]);
        }

        foreach ($comparator->alteredIndexes() as $pair) {
            /** @var AbstractIndex $index */
            $index = $pair[self::ORIGINAL_STATE];
            $method->addBody('->alterIndex(?, ?)', [$index->getColumns(), $this->indexOptions($index)]);
        }

        foreach ($comparator->addedIndexes() as $index) {
            $method->addBody('->dropIndex(?)', [$index->getColumns()]);
        }
    }

    /**
     * @param string $prefix Database isolation prefix.
     */
    private function revertForeignKeys(Method $method, Comparator $comparator, string $prefix = ''): void
    {
        foreach ($comparator->droppedForeignKeys() as $key) {
            $method->addBody('->addForeignKey(?, ?, ?, ?)', [
                $key->getColumns(),
                \substr($key->getForeignTable(), \strlen($prefix)),
                $key->getForeignKeys(),
                $this->foreignKeyOptions($key),
            ]);
        }

        foreach ($comparator->alteredForeignKeys() as $pair) {
            /** @var AbstractForeignKey $key */
            $key = $pair[self::ORIGINAL_STATE];
            $method->addBody('->alterForeignKey(?, ?, ?, ?)', [
                $key->getColumns(),
                \substr($key->getForeignTable(), \strlen($prefix)),
                $key->getForeignKeys(),
                $this->foreignKeyOptions($key),
            ]);
        }

        foreach ($comparator->addedForeignKeys() as $key) {
            $method->addBody('->dropForeignKey(?)', [
                $key->getColumns(),
            ]);
        }
    }

    private function columnOptions(AbstractColumn $column): array
    {
        $options = [
            'nullable' => $column->isNullable(),
            'defaultValue' => $column->getDefaultValue(),
        ];

        if ($column->getAbstractType() === 'enum') {
            $options['values'] = $column->getEnumValues();
        }

        foreach ($column->getAttributes() as $attribute => $value) {
            if ($attribute === 'size' && $value === 0) {
                continue;
            }
            $options[$attribute] = $value;
        }

        $default = $options['defaultValue'];
        if ($column::DATETIME_NOW === ($default instanceof \Stringable ? (string) $default : $default)) {
            $options['defaultValue'] = AbstractColumn::DATETIME_NOW;
        }

        return $options;
    }

    private function indexOptions(AbstractIndex $index): array
    {
        return [
            'name' => $index->getName(),
            'unique' => $index->isUnique(),
        ];
    }

    private function foreignKeyOptions(AbstractForeignKey $reference): array
    {
        return [
            'name' => $reference->getName(),
            'delete' => $reference->getDeleteRule(),
            'update' => $reference->getUpdateRule(),
            'indexCreate' => $reference->hasIndex(),
        ];
    }

    private function getTableName(AbstractTable $table): string
    {
        return \substr($table->getName(), \strlen($table->getPrefix()));
    }
}
