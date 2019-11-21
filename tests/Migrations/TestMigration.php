<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Migrations\Tests;

use Spiral\Migrations\Migration;

class TestMigration extends Migration
{
    public function up()
    {
        return $this->getState();
    }

    public function down()
    {
        return $this->database();
    }

    public function getTable()
    {
        return $this->table('table');
    }
}
