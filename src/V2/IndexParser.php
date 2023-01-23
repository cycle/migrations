<?php


declare(strict_types=1);

namespace Cycle\Migrations\V2;

class IndexParser extends Index
{
    private Index $index;
    private string $tableName;

    public function __construct(Index $index, string $tableName)
    {
        $this->index = $index;
        $this->tableName = $tableName;
    }

    public function getField(): array
    {
        return $this->index->fields;
    }

    public function getName(): string
    {
        return $this->index->name
            ?? sprintf('%s_%s_index', $this->tableName, implode('_', $this->getField()));
    }

    public function isUnique(): bool
    {
        return $this->index->unique;
    }

    public function getOptions(): array
    {
        return [
            'name' => $this->getName(),
            'unique' => $this->isUnique(),
        ];
    }
}
