<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations;

use Spiral\Migrations\Exception\OperationException;

/**
 * Represents simple table operation. Operation is a bridge between command and declarative
 * migrations.
 */
interface OperationInterface
{
    /**
     * Table operation related to.
     *
     * @return string
     */
    public function getTable(): string;

    /**
     * Execute operation in a given capsule.
     *
     * @param CapsuleInterface $capsule
     *
     * @throws OperationException
     */
    public function execute(CapsuleInterface $capsule);
}