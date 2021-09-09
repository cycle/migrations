<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Migration;

use Cycle\Migrations\Exception\MigrationException;
use Cycle\Migrations\Migration\State;

/**
 * An interface for migrations providing information about the migration status.
 */
interface ProvidesSyncStateInterface
{
    /**
     * Alter associated migration state (new migration instance to be created).
     *
     * @param State $state
     * @return static
     */
    public function withState(State $state): self;

    /**
     * Get migration state.
     *
     * @return State
     * @throws MigrationException When no state is presented.
     */
    public function getState(): State;
}
