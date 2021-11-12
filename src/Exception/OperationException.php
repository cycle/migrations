<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Exception;

use Spiral\Migrations\Exception\OperationException as SpiralOperationException;

class OperationException extends MigrationException
{
}
\class_alias(OperationException::class, SpiralOperationException::class, false);
