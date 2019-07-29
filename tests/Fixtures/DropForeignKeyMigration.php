<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Fixtures;

use Spiral\Migrations\Migration;

class DropForeignKeyMigration extends Migration
{
    public function up()
    {
        $this->table('sample')
            ->dropForeignKey(['column'])
            ->update();
    }

    public function down()
    {
    }
}