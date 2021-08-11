<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Fixtures;

use Cycle\Migrations\Migration;

class DropForeignKeyMigration extends Migration
{
    public function up(): void
    {
        $this->table('sample')
            ->dropForeignKey(['column'])
            ->update();
    }

    public function down(): void
    {
    }
}
