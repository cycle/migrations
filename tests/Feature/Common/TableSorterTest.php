<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Feature\Common;

use Cycle\Migrations\Tests\MigrationsTestCase;
use Cycle\Migrations\Atomizer\TableSorter;

final class TableSorterTest extends MigrationsTestCase
{
    #[\Testo\Test]
    public function testSortWithoutDeps(): void
    {
        $sorter = new TableSorter();
        $table1 = $this->schema('table1');
        $table1->primary('id');
        $table1->integer('value');

        $table2 = $this->schema('table2');
        $table2->primary('id');
        $table1->integer('value');

        \Testo\Assert::same($sorter->sort([$table1, $table2]), [$table1, $table2]);
    }

    #[\Testo\Test]
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

        \Testo\Assert::same($sorter->sort([$table1, $table2]), [$table1, $table2]);
    }

    #[\Testo\Test]
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

        \Testo\Assert::same($sorter->sort([$table1, $table2]), [$table2, $table1]);
    }
}
