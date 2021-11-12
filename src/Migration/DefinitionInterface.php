<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Migration;

use Cycle\Migrations\CapsuleInterface;
use Cycle\Migrations\Exception\MigrationException;
use Spiral\Migrations\CapsuleInterface as SpiralCapsuleInterface;
use Spiral\Migrations\Migration\DefinitionInterface as SpiralDefinitionInterface;

interface_exists(SpiralCapsuleInterface::class);

interface DefinitionInterface
{
    /**
     * Lock migration into specific migration capsule.
     *
     * @param CapsuleInterface $capsule
     *
     * @return self
     */
    public function withCapsule(SpiralCapsuleInterface $capsule): self;

    /**
     * Target migration database. Each migration must be specific to one
     * database only.
     *
     * @return string|null
     */
    public function getDatabase(): ?string;

    /**
     * Up migration.
     *
     * @throws MigrationException
     *
     * @return mixed
     */
    public function up();

    /**
     * Rollback migration.
     *
     * @throws MigrationException
     *
     * @return mixed
     */
    public function down();
}
\class_alias(DefinitionInterface::class, SpiralDefinitionInterface::class, false);
