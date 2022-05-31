<?php

declare(strict_types=1);

namespace Cycle\Migrations\Atomizer;

use Cycle\Database\Schema\AbstractTable;
use Cycle\Database\Schema\Reflector;
use Spiral\Reactor\Partial\Method;

/**
 * Atomizer provides ability to convert given AbstractTables and their changes into set of
 * migration commands.
 */
final class Atomizer
{
    /** @var AbstractTable[] */
    protected array $tables = [];

    public function __construct(
        private readonly RendererInterface $renderer
    ) {
    }

    /**
     * Add new table into atomizer.
     */
    public function addTable(AbstractTable $table): self
    {
        $this->tables[] = $table;

        return $this;
    }

    /**
     * Get all atomizer tables.
     *
     * @return AbstractTable[]
     */
    public function getTables(): array
    {
        return $this->tables;
    }

    /**
     * Generate set of commands needed to describe migration (up command).
     */
    public function declareChanges(Method $method): void
    {
        foreach ($this->sortedTables() as $table) {
            if (!$table->getComparator()->hasChanges()) {
                continue;
            }

            if (!$table->exists()) {
                $this->renderer->createTable($method, $table);
            } else {
                $this->renderer->updateTable($method, $table);
            }
        }
    }

    /**
     * Generate set of lines needed to rollback migration (down command).
     */
    public function revertChanges(Method $method): void
    {
        foreach ($this->sortedTables(true) as $table) {
            if (!$table->getComparator()->hasChanges()) {
                continue;
            }

            if (!$table->exists()) {
                $this->renderer->dropTable($method, $table);
            } else {
                $this->renderer->revertTable($method, $table);
            }
        }
    }

    /**
     * Tables sorted in order of their dependencies.
     *
     * @return AbstractTable[]
     */
    protected function sortedTables($reverse = false): array
    {
        $reflector = new Reflector();
        foreach ($this->tables as $table) {
            $reflector->addTable($table);
        }

        $sorted = $reflector->sortedTables();
        if ($reverse) {
            return \array_reverse($sorted);
        }

        return $sorted;
    }
}
