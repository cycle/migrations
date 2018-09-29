<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Fixtures;

use Spiral\Migrations\Migration;

class AlterNonExistedColumnMigration extends Migration
{
    public function up()
    {
        $this->table('sample')
            ->alterColumn('column', 'string')
            ->create();
    }

    public function down()
    {
    }
}