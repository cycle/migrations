<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Exception\Operation;

use Cycle\Migrations\Exception\OperationException;
use Spiral\Migrations\Operation\Exception\Operation\TableException as SpiralTableException;

class TableException extends OperationException
{
}
\class_alias(TableException::class, SpiralTableException::class, false);
