<?php

declare(strict_types=1);

namespace Cycle\Migrations\V2;

class ForeignKey
{
    protected array $innerKeys = [];
    protected string $outerTable;
    protected array $outerKeys = [];
    protected FKAction $onDelete;
    protected FKAction $onUpdate;
    protected ?string $name = null;

    public function __construct(array $innerKey, string $table, array $outerKey)
    {
        $this->innerKeys = $innerKey;
        $this->outerTable = $table;
        $this->outerKeys = $outerKey;

        $this->onDelete = FKAction::cascade();
        $this->onUpdate = FKAction::cascade();
    }

    public function name(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function onDelete(FKAction $action): self
    {
        $this->onDelete = $action;

        return $this;
    }

    public function onUpdate(FKAction $action): self
    {
        $this->onUpdate = $action;

        return $this;
    }
}
