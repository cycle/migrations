<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Fixtures;

use Spiral\Migrations\Migration;

class AlterNonExistedIndexMigration extends Migration
{
    public function up()
    {
        $this->table('sample')
            ->alterIndex(['column'], [
                'unique' => true
            ])
            ->create();
    }

    public function down()
    {
    }
}