<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Fixtures;

use Spiral\Migrations\Migration;

class AddForeignKeyMigration extends Migration
{
    public function up()
    {
        $this->table('sample')
            ->addForeignKey('column', 'target', 'id')
            ->update();
    }

    public function down()
    {
    }
}