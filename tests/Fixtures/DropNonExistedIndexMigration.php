<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Fixtures;

use Spiral\Migrations\Migration;

class DropNonExistedIndexMigration extends Migration
{
    public function up()
    {
        $this->table('sample')
            ->dropIndex(['column'])
            ->create();
    }

    public function down()
    {
    }
}