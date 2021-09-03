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

class AlterForeignKeyMigration extends Migration
{
    public function up(): void
    {
        $this->table('sample')
            ->alterForeignKey(['column'], 'target2', ['id2'], [
                'delete' => 'CASCADE',
            ])
            ->update();
    }

    public function down(): void
    {
    }
}
