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

class RenameColumnMigration extends Migration
{
    public function up(): void
    {
        $this->table('sample')
            ->renameColumn('column', 'new_name')
            ->create();
    }

    public function down(): void
    {
    }
}
