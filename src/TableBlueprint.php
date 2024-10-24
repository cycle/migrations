<?php

declare(strict_types=1);

namespace Cycle\Migrations;

use Cycle\Database\Schema\AbstractTable;
use Cycle\Migrations\Exception\BlueprintException;

/**
 * TableBlueprint is abstraction wrapper at top of AbstractTable which converts command based
 * definitions into declarative.
 */
final class TableBlueprint
{
    private bool $executed = false;
    private array $operations = [];

    public function __construct(private CapsuleInterface $capsule, private string $table) {}

    /**
     * Get associated table schema.
     */
    public function getSchema(): AbstractTable
    {
        return $this->capsule->getSchema($this->table);
    }

    /**
     * Example:
     * $table->addColumn('name', 'string', ['length' => 64]);
     * $table->addColumn('status', 'enum', [
     *      'values' => ['active', 'disabled']
     * ]);
     */
    public function addColumn(string $name, string $type, array $options = []): self
    {
        return $this->addOperation(
            new Operation\Column\Add($this->table, $name, $type, $options),
        );
    }

    /**
     * Example:
     * $table->alterColumn('name', 'string', ['length' => 128]);
     */
    public function alterColumn(string $name, string $type, array $options = []): self
    {
        return $this->addOperation(
            new Operation\Column\Alter($this->table, $name, $type, $options),
        );
    }

    /**
     * Example:
     * $table->renameColumn('column', 'new_name');
     */
    public function renameColumn(string $name, string $newName): self
    {
        return $this->addOperation(
            new Operation\Column\Rename($this->table, $name, $newName),
        );
    }

    /**
     * Example:
     * $table->dropColumn('email');
     */
    public function dropColumn(string $name): self
    {
        return $this->addOperation(
            new Operation\Column\Drop($this->table, $name),
        );
    }

    /**
     * Example:
     * $table->addIndex(['email'], ['unique' => true]);
     */
    public function addIndex(array $columns, array $options = []): self
    {
        return $this->addOperation(
            new Operation\Index\Add($this->table, $columns, $options),
        );
    }

    /**
     * Example:
     * $table->alterIndex(['email'], ['unique' => false]);
     */
    public function alterIndex(array $columns, array $options): self
    {
        return $this->addOperation(
            new Operation\Index\Alter($this->table, $columns, $options),
        );
    }

    /**
     * Example:
     * $table->dropIndex(['email']);
     */
    public function dropIndex(array $columns): self
    {
        return $this->addOperation(
            new Operation\Index\Drop($this->table, $columns),
        );
    }

    /**
     * Example:
     * $table->addForeignKey(['user_id'], 'users', ['id'], ['delete' => 'CASCADE']);
     *
     * @param string $foreignTable Database isolation prefix will be automatically added.
     */
    public function addForeignKey(
        array $columns,
        string $foreignTable,
        array $foreignKeys,
        array $options = [],
    ): self {
        return $this->addOperation(
            new Operation\ForeignKey\Add(
                $this->table,
                $columns,
                $foreignTable,
                $foreignKeys,
                $options,
            ),
        );
    }

    /**
     * Example:
     * $table->alterForeignKey(['user_id'], 'users', ['id'], ['delete' => 'NO ACTION']);
     */
    public function alterForeignKey(
        array $columns,
        string $foreignTable,
        array $foreignKeys,
        array $options = [],
    ): self {
        return $this->addOperation(
            new Operation\ForeignKey\Alter(
                $this->table,
                $columns,
                $foreignTable,
                $foreignKeys,
                $options,
            ),
        );
    }

    /**
     * Example:
     * $table->dropForeignKey(['user_id']);
     */
    public function dropForeignKey(array $columns): self
    {
        return $this->addOperation(
            new Operation\ForeignKey\Drop($this->table, $columns),
        );
    }

    /**
     * Set table primary keys index. Attention, you can only call it when table being created.
     */
    public function setPrimaryKeys(array $keys): self
    {
        return $this->addOperation(
            new Operation\Table\PrimaryKeys($this->table, $keys),
        );
    }

    /**
     * Create table schema. Must be last operation in the sequence.
     */
    public function create(): void
    {
        $this->addOperation(
            new Operation\Table\Create($this->table),
        );

        $this->execute();
    }

    /**
     * Update table schema. Must be last operation in the sequence.
     */
    public function update(): void
    {
        $this->addOperation(
            new Operation\Table\Update($this->table),
        );

        $this->execute();
    }

    /**
     * Drop table. Must be last operation in the sequence.
     */
    public function drop(): void
    {
        $this->addOperation(
            new Operation\Table\Drop($this->table),
        );

        $this->execute();
    }

    /**
     * Rename table. Must be last operation in the sequence.
     */
    public function rename(string $newName): void
    {
        $this->addOperation(
            new Operation\Table\Rename($this->table, $newName),
        );

        $this->execute();
    }

    /**
     * Register new operation.
     */
    public function addOperation(OperationInterface $operation): self
    {
        $this->operations[] = $operation;

        return $this;
    }

    /**
     * Execute blueprint operations.
     */
    private function execute(): void
    {
        if ($this->executed) {
            throw new BlueprintException('Only one create/update/rename/drop is allowed per blueprint.');
        }

        $this->capsule->execute($this->operations);
        $this->executed = true;
    }
}
