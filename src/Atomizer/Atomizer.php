<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Atomizer;

use Spiral\Migrations\Atomizer\RendererInterface as SpiralRendererInterface;
use Cycle\Database\Schema\AbstractTable;
use Spiral\Database\Schema\AbstractTable as SpiralAbstractTable;
use Cycle\Database\Schema\Reflector;
use Spiral\Reactor\Partial\Source;

/**
 * Atomizer provides ability to convert given AbstractTables and their changes
 * into set of migration commands.
 */
final class Atomizer
{
    /** @var AbstractTable[] */
    protected $tables = [];

    /** @var RendererInterface */
    private $renderer;

    /**
     * @param RendererInterface|SpiralRendererInterface $renderer This argument
     *        signature will be changed to {@see RendererInterface} in further release.
     */
    public function __construct(SpiralRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Add new table into atomizer.
     *
     * @param AbstractTable|SpiralAbstractTable $table This argument signature
     *        will be changed to {@see AbstractTable} in further release.
     *
     * @return Atomizer
     */
    public function addTable(SpiralAbstractTable $table): self
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
     *
     * @param Source $source
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
    }

    /**
     * Generate set of lines needed to rollback migration (down command).
     *
     * @param Source $source
     */
    public function revertChanges(Source $source): void
    {
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
     *
     * @param Source $source
     */
    private function declareBlock(Source $source): void
    {
        if ($source->getLines() !== []) {
            $source->addLine('');
        }
    }
}
