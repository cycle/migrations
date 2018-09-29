<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Fixtures;

use Spiral\Migrations\Migration;

class RenameTableMigration extends Migration
{
    public function up()
    {
        $this->table('sample')->rename('new_name');
    }

    public function down()
    {
    }
}