<?php

declare(strict_types=1);

namespace Cycle\Migrations\Atomizer;

use Cycle\Database\Schema\AbstractTable;
use Spiral\Reactor\Partial\Source;

/**
 * Renders table differences and create syntaxes into given source.
 */
interface RendererInterface
{
    /**
     * Migration engine specific table creation syntax.
     */
    public function createTable(Source $source, AbstractTable $table);

    /**
     * Migration engine specific table update syntax.
     */
    public function updateTable(Source $source, AbstractTable $table);

    /**
     * Migration engine specific table revert syntax.
     */
    public function revertTable(Source $source, AbstractTable $table);

    /**
     * Migration engine specific table drop syntax.
     */
    public function dropTable(Source $source, AbstractTable $table);
}
