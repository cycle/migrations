<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations;

use Cycle\Migrations\Exception\MigrationException;

interface MigrationInterface
{
    /**
     * Target migration database. Each migration must be specific to one database only.
     *
     * @return string|null
     */
    public function getDatabase(): ?string;

    /**
     * Lock migration into specific migration capsule.
     *
     * @param CapsuleInterface $capsule
     *
     * @return self
     */
    public function withCapsule(CapsuleInterface $capsule): self;

    /**
     * Alter associated migration state (new migration instance to be created).
     *
     * @param State $state
     *
     * @return self
     */
    public function withState(State $state): self;

    /**
     * Get migration state.
     *
     * @throws MigrationException When no state is presented.
     *
     * @return State
     */
    public function getState(): State;

    /**
     * Up migration.
     *
     * @throws MigrationException
     */
    public function up();

    /**
     * Rollback migration.
     *
     * @throws MigrationException
     */
    public function down();
}
