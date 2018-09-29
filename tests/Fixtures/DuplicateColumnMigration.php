<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Fixtures;

use Spiral\Migrations\Migration;

class DuplicateColumnMigration extends Migration
{
    public function up()
    {
        $this->table('sample')
            ->addColumn('column', 'string')
            ->update();
    }

    public function down()
    {
    }
}