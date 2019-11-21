<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Migrations\Fixtures;

use Spiral\Migrations\Migration;

class AddForeignKeyMigration extends Migration
{
    public function up(): void
    {
        $this->table('sample')
            ->addForeignKey(['column'], 'target', ['id'])
            ->update();
    }

    public function down(): void
    {
    }
}
