<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Atomizer;

use Spiral\Database\Schema\AbstractTable;
use Spiral\Database\Schema\Reflector;
use Spiral\Reactor\Partial\Source;


/**
 * Atomizer provides ability to convert given AbstractTables and their changes into set of
 * migration commands.
 */
final class Atomizer
{
    /** @var RendererInterface */
    private $renderer;

    /** @var AbstractTable[] */
    protected $tables = [];

    /**
     * @param RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Add new table into atomizer.
     *
     * @param AbstractTable $table
     * @return Atomizer
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
     *
     * @param Source $source
     */
    public function declareChanges(Source $source)
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
    public function revertChanges(Source $source)
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
    private function declareBlock(Source $source)
    {
        if (!empty($source->getLines())) {
            $source->addLine("");
        }
    }
}