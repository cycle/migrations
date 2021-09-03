<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Atomizer;

use Cycle\Database\Schema\AbstractColumn;
use Cycle\Database\Schema\AbstractForeignKey;
use Cycle\Database\Schema\AbstractIndex;
use Cycle\Database\Schema\AbstractTable;
use Cycle\Database\Schema\Comparator;
use Spiral\Reactor\Partial\Source;
use Spiral\Reactor\Serializer;
use Spiral\Reactor\Traits\SerializerTrait;

final class Renderer implements RendererInterface
{
    use SerializerTrait;

    /**
     * Comparator alteration states.
     */
    public const NEW_STATE = 0;
    public const ORIGINAL_STATE = 1;

    /**
     * {@inheritdoc}
     */
    public function createTable(Source $source, AbstractTable $table): void
    {
        $this->render(
            $source,
            '$this->table(%s)',
            $table
        );
        $comparator = $table->getComparator();

        $this->declareColumns($source, $comparator);
        $this->declareIndexes($source, $comparator);
        $this->declareForeignKeys($source, $comparator, $table->getPrefix());

        if (count($table->getPrimaryKeys())) {
            $this->render(
                $source,
                '    ->setPrimaryKeys(%s)',
                $table->getPrimaryKeys()
            );
        }

        //Finalization
        $source->addLine('    ->create();');
    }

    /**
     * {@inheritdoc}
     */
    public function updateTable(Source $source, AbstractTable $table): void
    {
        $this->render(
            $source,
            '$this->table(%s)',
            $table
        );
        $comparator = $table->getComparator();

        if ($comparator->isPrimaryChanged()) {
            $this->render(
                $source,
                '    ->setPrimaryKeys(%s)',
                $table->getPrimaryKeys()
            );
        }

        $this->declareColumns($source, $comparator);
        $this->declareIndexes($source, $comparator);
        $this->declareForeignKeys($source, $comparator, $table->getPrefix());

        //Finalization
        $source->addLine('    ->update();');
    }

    /**
     * {@inheritdoc}
     */
    public function revertTable(Source $source, AbstractTable $table): void
    {
        //Get table blueprint
        $this->render(
            $source,
            '$this->table(%s)',
            $table
        );
        $comparator = $table->getComparator();

        $this->revertForeignKeys($source, $comparator, $table->getPrefix());
        $this->revertIndexes($source, $comparator);
        $this->revertColumns($source, $comparator);

        //Finalization
        $source->addLine('    ->update();');
    }

    /**
     * {@inheritdoc}
     */
    public function dropTable(Source $source, AbstractTable $table): void
    {
        $this->render(
            $source,
            '$this->table(%s)->drop();',
            $table
        );
    }

    /**
     * @param Source     $source
     * @param Comparator $comparator
     */
    private function declareColumns(Source $source, Comparator $comparator): void
    {
        foreach ($comparator->addedColumns() as $column) {
            $this->render(
                $source,
                '    ->addColumn(%s, %s, %s)',
                $column->getName(),
                $column->getDeclaredType() ?? $column->getAbstractType(),
                $column
            );
        }

        foreach ($comparator->alteredColumns() as $pair) {
            $this->alterColumn(
                $source,
                $pair[self::NEW_STATE],
                $pair[self::ORIGINAL_STATE]
            );
        }

        foreach ($comparator->droppedColumns() as $column) {
            $this->render(
                $source,
                '    ->dropColumn(%s)',
                $column->getName()
            );
        }
    }

    /**
     * @param Source     $source
     * @param Comparator $comparator
     */
    private function declareIndexes(Source $source, Comparator $comparator): void
    {
        foreach ($comparator->addedIndexes() as $index) {
            $this->render(
                $source,
                '    ->addIndex(%s, %s)',
                $index->getColumns(),
                $index
            );
        }

        foreach ($comparator->alteredIndexes() as $pair) {
            /** @var AbstractIndex $index */
            $index = $pair[self::NEW_STATE];
            $this->render(
                $source,
                '    ->alterIndex(%s, %s)',
                $index->getColumns(),
                $index
            );
        }

        foreach ($comparator->droppedIndexes() as $index) {
            $this->render(
                $source,
                '    ->dropIndex(%s)',
                $index->getColumns()
            );
        }
    }

    /**
     * @param Source     $source
     * @param Comparator $comparator
     * @param string     $prefix Database isolation prefix
     */
    private function declareForeignKeys(Source $source, Comparator $comparator, string $prefix = ''): void
    {
        foreach ($comparator->addedForeignKeys() as $key) {
            $this->render(
                $source,
                '    ->addForeignKey(%s, %s, %s, %s)',
                $key->getColumns(),
                substr($key->getForeignTable(), strlen($prefix)),
                $key->getForeignKeys(),
                $key
            );
        }

        foreach ($comparator->alteredForeignKeys() as $pair) {
            /** @var AbstractForeignKey $key */
            $key = $pair[self::NEW_STATE];
            $this->render(
                $source,
                '    ->alterForeignKey(%s, %s, %s, %s)',
                $key->getColumns(),
                substr($key->getForeignTable(), strlen($prefix)),
                $key->getForeignKeys(),
                $key
            );
        }

        foreach ($comparator->droppedForeignKeys() as $key) {
            $this->render(
                $source,
                '    ->dropForeignKey(%s)',
                $key->getColumns()
            );
        }
    }

    /**
     * @param Source     $source
     * @param Comparator $comparator
     */
    private function revertColumns(Source $source, Comparator $comparator): void
    {
        foreach ($comparator->droppedColumns() as $column) {
            $this->render(
                $source,
                '    ->addColumn(%s, %s, %s)',
                $column->getName(),
                $column->getDeclaredType() ?? $column->getAbstractType(),
                $column
            );
        }

        foreach ($comparator->alteredColumns() as $pair) {
            $this->alterColumn(
                $source,
                $pair[self::ORIGINAL_STATE],
                $pair[self::NEW_STATE]
            );
        }

        foreach ($comparator->addedColumns() as $column) {
            $this->render(
                $source,
                '    ->dropColumn(%s)',
                $column->getName()
            );
        }
    }

    /**
     * @param Source     $source
     * @param Comparator $comparator
     */
    private function revertIndexes(Source $source, Comparator $comparator): void
    {
        foreach ($comparator->droppedIndexes() as $index) {
            $this->render(
                $source,
                '    ->addIndex(%s, %s)',
                $index->getColumns(),
                $index
            );
        }

        foreach ($comparator->alteredIndexes() as $pair) {
            /** @var AbstractIndex $index */
            $index = $pair[self::ORIGINAL_STATE];
            $this->render(
                $source,
                '    ->alterIndex(%s, %s)',
                $index->getColumns(),
                $index
            );
        }

        foreach ($comparator->addedIndexes() as $index) {
            $this->render(
                $source,
                '    ->dropIndex(%s)',
                $index->getColumns()
            );
        }
    }

    /**
     * @param Source     $source
     * @param Comparator $comparator
     * @param string     $prefix Database isolation prefix.
     */
    private function revertForeignKeys(Source $source, Comparator $comparator, string $prefix = ''): void
    {
        foreach ($comparator->droppedForeignKeys() as $key) {
            $this->render(
                $source,
                '    ->addForeignKey(%s, %s, %s, %s)',
                $key->getColumns(),
                substr($key->getForeignTable(), strlen($prefix)),
                $key->getForeignKeys(),
                $key
            );
        }

        foreach ($comparator->alteredForeignKeys() as $pair) {
            /** @var AbstractForeignKey $key */
            $key = $pair[self::ORIGINAL_STATE];
            $this->render(
                $source,
                '    ->alterForeignKey(%s, %s, %s, %s)',
                $key->getColumns(),
                substr($key->getForeignTable(), strlen($prefix)),
                $key->getForeignKeys(),
                $key
            );
        }

        foreach ($comparator->addedForeignKeys() as $key) {
            $this->render($source, '    ->dropForeignKey(%s)', $key->getColumns());
        }
    }

    /**
     * @param Source         $source
     * @param AbstractColumn $column
     * @param AbstractColumn $original
     */
    protected function alterColumn(
        Source $source,
        AbstractColumn $column,
        AbstractColumn $original
    ): void {
        if ($column->getName() !== $original->getName()) {
            $name = $original->getName();
        } else {
            $name = $column->getName();
        }

        $this->render(
            $source,
            '    ->alterColumn(%s, %s, %s)',
            $name,
            $column->getDeclaredType() ?? $column->getAbstractType(),
            $column
        );

        if ($column->getName() !== $original->getName()) {
            $this->render(
                $source,
                '    ->renameColumn(%s, %s)',
                $name,
                $column->getName()
            );
        }
    }

    /**
     * Render values and options into source.
     *
     * @param Source $source
     * @param string $format
     * @param array  ...$values
     */
    protected function render(Source $source, string $format, ...$values): void
    {
        $serializer = $this->getSerializer();

        $rendered = [];
        foreach ($values as $value) {
            if ($value instanceof AbstractTable) {
                $rendered[] = $serializer->serialize(
                    substr($value->getName(), strlen($value->getPrefix()))
                );
                continue;
            }

            if ($value instanceof AbstractColumn) {
                $rendered[] = $this->columnOptions($serializer, $value);
                continue;
            }

            if ($value instanceof AbstractIndex) {
                $rendered[] = $this->indexOptions($serializer, $value);
                continue;
            }

            if ($value instanceof AbstractForeignKey) {
                $rendered[] = $this->foreignKeyOptions($serializer, $value);
                continue;
            }

            // numeric array
            if (is_array($value) && count($value) > 0 && is_numeric(array_keys($value)[0])) {
                $rendered[] = '["' . implode('", "', $value) . '"]';
                continue;
            }

            $rendered[] = $serializer->serialize($value);
        }

        $lines = sprintf($format, ...$rendered);
        foreach (explode("\n", $lines) as $line) {
            $source->addLine($line);
        }
    }

    /**
     * @param Serializer     $serializer
     * @param AbstractColumn $column
     *
     * @return string
     */
    private function columnOptions(Serializer $serializer, AbstractColumn $column): string
    {
        $options = [
            'nullable' => $column->isNullable(),
            'default' => $column->getDefaultValue(),
        ];

        if ($column->getAbstractType() === 'enum') {
            $options['values'] = $column->getEnumValues();
        }

        if ($column->getAbstractType() === 'string') {
            $options['size'] = $column->getSize();
        }

        if ($column->getAbstractType() === 'decimal') {
            $options['scale'] = $column->getScale();
            $options['precision'] = $column->getPrecision();
        }

        return $this->mountIndents($serializer->serialize($options));
    }

    /**
     * @param Serializer    $serializer
     * @param AbstractIndex $index
     *
     * @return string
     */
    private function indexOptions(Serializer $serializer, AbstractIndex $index): string
    {
        return $this->mountIndents(
            $serializer->serialize(
                [
                    'name' => $index->getName(),
                    'unique' => $index->isUnique(),
                ]
            )
        );
    }

    /**
     * @param Serializer         $serializer
     * @param AbstractForeignKey $reference
     *
     * @return string
     */
    private function foreignKeyOptions(
        Serializer $serializer,
        AbstractForeignKey $reference
    ): string {
        return $this->mountIndents(
            $serializer->serialize(
                [
                    'name' => $reference->getName(),
                    'delete' => $reference->getDeleteRule(),
                    'update' => $reference->getUpdateRule(),
                ]
            )
        );
    }

    /**
     * Mount indents for column and index options.
     *
     * @param $serialized
     *
     * @return string
     */
    private function mountIndents(string $serialized): string
    {
        $lines = explode("\n", $serialized);
        foreach ($lines as &$line) {
            $line = '    ' . $line;
            unset($line);
        }

        return ltrim(implode("\n", $lines));
    }
}
