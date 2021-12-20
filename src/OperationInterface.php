<?php

declare(strict_types=1);

namespace Cycle\Migrations;

use Cycle\Migrations\Exception\OperationException;

/**
 * Represents simple table operation. Operation is a bridge between command and declarative
 * migrations.
 */
interface OperationInterface
{
    /**
     * Table operation related to.
     */
    public function getTable(): string;

    /**
     * Execute operation in a given capsule.
     *
     * @throws OperationException
     */
    public function execute(CapsuleInterface $capsule): void;
}
