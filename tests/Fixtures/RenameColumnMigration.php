<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Fixtures;

use Spiral\Migrations\Migration;

class RenameColumnMigration extends Migration
{
    public function up()
    {
        $this->table('sample')
            ->renameColumn('column', 'new_name')
            ->create();
    }

    public function down()
    {
    }
}