<?php


declare(strict_types=1);

namespace Cycle\Migrations\V2;

class ForeignKeyParser extends ForeignKey
{
    private ForeignKey $foreignKey;
    private string $tableName;

    public function __construct(ForeignKey $foreignKey, string $tableName)
    {
        $this->foreignKey = $foreignKey;
        $this->tableName = $tableName;
    }

    public function getName(): string
    {
        return $this->index->name ?? sprintf(
                '%s_%s_%s_%s_fk',
                $this->getInnerTable(),
                $this->getInnerKeys()[0],
                $this->getOuterTable(),
                $this->getOuterKeys()[0]
            );
    }

    public function getInnerTable(): string
    {
        return $this->tableName;
    }

    public function getInnerKeys(): array
    {
        return $this->foreignKey->innerKeys;
    }

    public function getOuterTable(): string
    {
        return $this->foreignKey->outerTable;
    }

    public function getOuterKeys(): array
    {
        return $this->foreignKey->outerKeys;
    }

    public function getOnDelete(): string
    {
        return $this->foreignKey->onDelete->value();
    }

    public function getOnUpdate(): string
    {
        return $this->foreignKey->onUpdate->value();
    }

    public function getOptions(): array
    {
        return [
            'name' => $this->getName(),
            'delete' => $this->getOnDelete(),
            'update' => $this->getOnUpdate(),
        ];
    }
}
