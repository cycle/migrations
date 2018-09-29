<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations;

use Spiral\Migrations\Exception\MigrationException;

interface MigrationInterface
{
    /**
     * Target migration database. Each migration must be specific to one database only.
     *
     * @return null|string
     */
    public function getDatabase(): ?string;

    /**
     * Lock migration into specific migration capsule.
     *
     * @param CapsuleInterface $capsule
     * @return self
     */
    public function withCapsule(CapsuleInterface $capsule): MigrationInterface;

    /**
     * Alter associated migration state (new migration instance to be created).
     *
     * @param State $state
     * @return self
     */
    public function withState(State $state): MigrationInterface;

    /**
     * Get migration state.
     *
     * @return State
     *
     * @throws MigrationException When no state is presented.
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