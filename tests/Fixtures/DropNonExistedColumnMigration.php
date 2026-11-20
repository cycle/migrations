<?php

declare(strict_types=1);

namespace Cycle\Migrations\Fixtures;

use Cycle\Migrations\Migration;

class DropNonExistedColumnMigration extends Migration
{
    public function up(): void
    {
        $this->table('sample')
            ->dropColumn('column')
            ->update();
    }

    public function down(): void
    {
    }
}
