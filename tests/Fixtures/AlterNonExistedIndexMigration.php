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

class AlterNonExistedIndexMigration extends Migration
{
    public function up(): void
    {
        $this->table('sample')
            ->alterIndex(['column'], [
                'unique' => true,
            ])
            ->create();
    }

    public function down(): void
    {
    }
}
