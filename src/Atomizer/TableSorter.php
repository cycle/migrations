<?php

declare(strict_types=1);

namespace Cycle\Migrations\Atomizer;

use Cycle\Database\Schema\AbstractTable;
use Cycle\Database\Schema\Reflector;

final class TableSorter
{
    /**
     * Tables sorted in order of their dependencies.
     *
     * @param AbstractTable[] $tables
     * @param mixed $reverse
     *
     * @return AbstractTable[]
     */
    public function sort(array $tables, $reverse = false): array
    {
        $reflector = new Reflector();
        foreach ($tables as $table) {
            $reflector->addTable($table);
        }

        $sorted = $reflector->sortedTables();
        if ($reverse) {
            return \array_reverse($sorted);
        }

        return $sorted;
    }
}
