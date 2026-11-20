<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Unit;

use Cycle\Migrations\Atomizer\Atomizer;
use Cycle\Migrations\Atomizer\Renderer;
use Cycle\Testing\Database\Memory;
use Spiral\Reactor\Partial\Method;
use Testo\Assert;
use Testo\Test;

#[Test]
final class AtomizerTest
{
    public function newTableIsCreatedAndDroppedBack(): void
    {
        $users = Memory::database()->table('users')->getSchema();
        $users->primary('id');
        $users->string('email');

        $atomizer = (new Atomizer(new Renderer()))->addTable($users);
        $atomizer->declareChanges($up = new Method('up'));
        $atomizer->revertChanges($down = new Method('down'));

        Assert::same($atomizer->getTables(), [$users]);
        Assert::string($up->getBody())
            ->contains("\$this->table('users')")
            ->contains("->addColumn('email', 'string'")
            ->contains('->create();');
        Assert::same($down->getBody(), "\$this->table('users')->drop();\n");
    }
}
