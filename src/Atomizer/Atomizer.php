<?php

declare(strict_types=1);

namespace Cycle\Migrations\Atomizer;

use Cycle\Database\Schema\AbstractTable;
use Cycle\Database\Schema\Reflector;
use Spiral\Reactor\Partial\Source;

/**
 * Atomizer provides ability to convert given AbstractTables and their changes into set of
 * migration commands.
 */
final class Atomizer
{
    /** @var AbstractTable[] */
    protected array $tables = [];

    public function __construct(private RendererInterface $renderer)
    {
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
    public function declareChanges(Source $source): void
    {
        foreach ($this->sortedTables() as $table) {
            if (!$table->getComparator()->hasChanges()) {
                continue;
            }

            //New operations block
            $this->declareBlock($source);

            if (!$table->exists()) {
                $this->renderer->createTable($source, $table);
            } else {
                $this->renderer->updateTable($source, $table);
            }
        }

        foreach ($this->sortedTables() as $table) {
            $this->renderer->declareForeignKeys($source, $table);
        }
    }

    /**
     * Generate set of lines needed to rollback migration (down command).
     */
    public function revertChanges(Source $source): void
    {
        foreach ($this->sortedTables() as $table) {
            $this->renderer->revertForeignKeys($source, $table);
        }

        foreach ($this->sortedTables(true) as $table) {
            if (!$table->getComparator()->hasChanges()) {
                continue;
            }

            //New operations block
            $this->declareBlock($source);

            if (!$table->exists()) {
                $this->renderer->dropTable($source, $table);
            } else {
                $this->renderer->revertTable($source, $table);
            }
        }
    }

    /**
     * Tables sorted in order of their dependencies.
     *
     * @param bool $reverse
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
            return array_reverse($sorted);
        }

        return $sorted;
    }

    /**
     * Add spacing between commands, only if required.
     */
    private function declareBlock(Source $source): void
    {
        if ($source->getLines() !== []) {
            $source->addLine('');
        }
    }
}
