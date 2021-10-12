<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Migration;

use Cycle\Migrations\Exception\MigrationException;
use Spiral\Migrations\Migration\State as SpiralState;

/**
 * An interface for migrations providing information about the migration status.
 */
interface ProvidesSyncStateInterface
{
    /**
     * Alter associated migration state (new migration instance to be created).
     *
     * @param SpiralState|State $state The signature of this argument will be
     *        changed to {@see State} in future release.
     *
     * @return static
     */
    public function withState(SpiralState $state): self;

    /**
     * Get migration state.
     *
     * @throws MigrationException When no state is presented.
     *
     * @return State
     */
    public function getState(): State;
}
