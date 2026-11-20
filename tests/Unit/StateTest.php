<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Unit;

use Cycle\Migrations\State;
use Testo\Assert;
use Testo\Test;

#[Test]
final class StateTest
{
    public function newStateIsUndefined(): void
    {
        $created = new \DateTimeImmutable('2024-01-02 03:04:05');

        $state = new State('create_users', $created);

        Assert::same($state->getName(), 'create_users');
        Assert::same($state->getTimeCreated(), $created);
        Assert::same($state->getStatus(), State::STATUS_UNDEFINED);
        Assert::null($state->getTimeExecuted());
    }

    public function statusChangeLeavesOriginalIntact(): void
    {
        $state = new State('create_users', new \DateTimeImmutable());
        $executed = new \DateTimeImmutable('2024-01-02 03:04:05');

        $done = $state->withStatus(State::STATUS_EXECUTED, $executed);

        Assert::same($done->getStatus(), State::STATUS_EXECUTED);
        Assert::same($done->getTimeExecuted(), $executed);
        Assert::same($state->getStatus(), State::STATUS_UNDEFINED);
        Assert::null($state->getTimeExecuted());
    }
}
