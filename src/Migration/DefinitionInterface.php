<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Migration;

use Cycle\Migrations\CapsuleInterface;
use Cycle\Migrations\Exception\MigrationException;

interface DefinitionInterface
{
    /**
     * Lock migration into specific migration capsule.
     *
     * @param CapsuleInterface $capsule
     * @return self
     */
    public function withCapsule(CapsuleInterface $capsule): self;

    /**
     * Target migration database. Each migration must be specific to one
     * database only.
     *
     * @return null|string
     */
    public function getDatabase(): ?string;

    /**
     * Up migration.
     *
     * @throws MigrationException
     * @return mixed
     */
    public function up();

    /**
     * Rollback migration.
     *
     * @throws MigrationException
     * @return mixed
     */
    public function down();
}
