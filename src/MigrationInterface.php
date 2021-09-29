<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations;

use Cycle\Migrations\Migration\DefinitionInterface;
use Cycle\Migrations\Migration\ProvidesSyncStateInterface;

interface MigrationInterface extends ProvidesSyncStateInterface, DefinitionInterface
{
}
