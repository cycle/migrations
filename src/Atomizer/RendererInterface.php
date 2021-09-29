<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     *
     * @param Source        $source
     * @param AbstractTable $table
     */
    public function createTable(Source $source, AbstractTable $table);

    /**
     * Migration engine specific table update syntax.
     *
     * @param Source        $source
     * @param AbstractTable $table
     */
    public function updateTable(Source $source, AbstractTable $table);

    /**
     * Migration engine specific table revert syntax.
     *
     * @param Source        $source
     * @param AbstractTable $table
     */
    public function revertTable(Source $source, AbstractTable $table);

    /**
     * Migration engine specific table drop syntax.
     *
     * @param Source        $source
     * @param AbstractTable $table
     */
    public function dropTable(Source $source, AbstractTable $table);
}
