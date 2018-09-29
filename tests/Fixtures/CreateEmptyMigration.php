<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Fixtures;

use Spiral\Migrations\Migration;

class CreateEmptyMigration extends Migration
{
    public function up()
    {
        $this->table('sample')
            ->create();
    }

    public function down()
    {
    }
}