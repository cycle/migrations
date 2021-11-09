<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests;

use Cycle\Database\DatabaseInterface;
use Cycle\Migrations\Migration;
use Cycle\Migrations\State;
use Cycle\Migrations\TableBlueprint;

class TestMigration extends Migration
{
    public function up(): State
    {
        return $this->getState();
    }

    public function down(): DatabaseInterface
    {
        return $this->database();
    }

    public function getTable(): TableBlueprint
    {
        return $this->table('table');
    }
}
