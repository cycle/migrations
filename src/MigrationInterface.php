<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations;

use Cycle\Migrations\Migration\DefinitionInterface;
use Cycle\Migrations\Migration\ProvidesSyncStateInterface;

interface MigrationInterface extends ProvidesSyncStateInterface, DefinitionInterface
{
}
