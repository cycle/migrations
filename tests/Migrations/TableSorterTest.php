<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests;

use Cycle\Migrations\Atomizer\TableSorter;

abstract class TableSorterTest extends BaseTest
{
    public function testSortWithoutDeps(): void
    {
        $sorter = new TableSorter();
        $table1 = $this->schema('table1');
        $table1->primary('id');
        $table1->integer('value');

        $table2 = $this->schema('table2');
        $table2->primary('id');
        $table1->integer('value');

        $this->assertSame([$table1, $table2], $sorter->sort([$table1, $table2]));
    }

    public function testSortWithCorrectOrder(): void
    {
        $sorter = new TableSorter();
        $table1 = $this->schema('table1');
        $table1->primary('id');
        $table1->integer('value');

        $table2 = $this->schema('table2');
        $table2->primary('id');
        $table2->integer('sample_id');
        $table2->foreignKey(['sample_id'])->references('table1', ['id']);

        $this->assertSame([$table1, $table2], $sorter->sort([$table1, $table2]));
    }

    public function testSortWithIncorrectOrder(): void
    {
        $sorter = new TableSorter();
        $table1 = $this->schema('table1');
        $table1->primary('id');
        $table1->integer('sample_id');
        $table1->foreignKey(['sample_id'])->references('table2', ['id']);

        $table2 = $this->schema('table2');
        $table2->primary('id');
        $table2->integer('value');

        $this->assertSame([$table2, $table1], $sorter->sort([$table1, $table2]));
    }
}
