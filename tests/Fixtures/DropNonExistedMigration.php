<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Fixtures;

use Spiral\Migrations\Migration;

class DropNonExistedMigration extends Migration
{
    public function up()
    {
        $this->table('sample')->drop();
    }

    public function down()
    {
    }
}