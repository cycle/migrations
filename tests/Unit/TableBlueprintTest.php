<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Unit;

use Cycle\Database\Database;
use Cycle\Database\ForeignKeyInterface;
use Cycle\Migrations\Capsule;
use Cycle\Migrations\Exception\BlueprintException;
use Cycle\Migrations\Exception\CapsuleException;
use Cycle\Migrations\Exception\Operation\ColumnException;
use Cycle\Migrations\TableBlueprint;
use Cycle\Testing\Database\Memory;
use Testo\Assert;
use Testo\Expect;
use Testo\Lifecycle\BeforeTest;
use Testo\Test;

#[Test]
final class TableBlueprintTest
{
    private Database $db;
    private Capsule $capsule;

    #[BeforeTest]
    public function connect(): void
    {
        $this->db = Memory::database();
        $this->capsule = new Capsule($this->db);
    }

    public function blueprintExecutesOnlyOnce(): void
    {
        $blueprint = $this->blueprint('users')->addColumn('id', 'primary');
        $blueprint->create();

        Expect::exception(BlueprintException::class)
            ->withMessage('Only one create/update/rename/drop is allowed per blueprint.');

        $blueprint->update();
    }

    public function foreignKeyTakesActionsFromOptions(): void
    {
        $this->blueprint('users')->addColumn('id', 'primary')->create();

        $this->blueprint('posts')
            ->addColumn('id', 'primary')
            ->addColumn('user_id', 'integer')
            ->addForeignKey(['user_id'], 'users', ['id'], ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();

        $foreignKey = $this->db->table('posts')->getSchema()->foreignKey(['user_id']);
        Assert::same($foreignKey->getForeignTable(), 'users');
        Assert::same($foreignKey->getDeleteRule(), ForeignKeyInterface::CASCADE);
        Assert::same($foreignKey->getUpdateRule(), ForeignKeyInterface::NO_ACTION);
    }

    public function alteredForeignKeyTakesNewActions(): void
    {
        $this->blueprint('users')->addColumn('id', 'primary')->create();
        $this->blueprint('posts')
            ->addColumn('id', 'primary')
            ->addColumn('user_id', 'integer')
            ->addForeignKey(['user_id'], 'users', ['id'], ['delete' => 'CASCADE'])
            ->create();

        $this->blueprint('posts')
            ->alterForeignKey(['user_id'], 'users', ['id'], ['update' => 'CASCADE'])
            ->update();

        $foreignKey = $this->db->table('posts')->getSchema()->foreignKey(['user_id']);
        Assert::same($foreignKey->getDeleteRule(), ForeignKeyInterface::NO_ACTION);
        Assert::same($foreignKey->getUpdateRule(), ForeignKeyInterface::CASCADE);
    }

    public function columnCannotBeRenamedToExistingOne(): void
    {
        $this->blueprint('users')
            ->addColumn('id', 'primary')
            ->addColumn('name', 'string')
            ->addColumn('email', 'string')
            ->create();

        Expect::exception(ColumnException::class)
            ->withMessage("Unable to rename column 'users'.'name', column 'email' already exists");

        $this->blueprint('users')->renameColumn('name', 'email')->update();
    }

    public function capsuleRejectsNonOperations(): void
    {
        Expect::exception(CapsuleException::class)
            ->withMessage('Migration operation expected to be an instance of `OperationInterface`, `stdClass` given');

        $this->capsule->execute([new \stdClass()]);
    }

    private function blueprint(string $table): TableBlueprint
    {
        return new TableBlueprint($this->capsule, $table);
    }
}
