<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Fixtures;

use Spiral\Migrations\Migration;

class CreateSampleMigration extends Migration
{
    public function up()
    {
        $this->table('sample')
            ->addColumn('id', 'primary')
            ->create();
    }

    public function down()
    {
    }
}