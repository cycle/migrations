<?php

declare(strict_types=1);

namespace Cycle\Migrations\V2;

class ColumnParser extends Column
{
    private Column $column;

    public function __construct(Column $column)
    {
        $this->column = $column;
    }

    public function getType(): string
    {
        return $this->column->type;
    }

    public function getLength(): ?int
    {
        return $this->column->length;
    }

    public function isUnique(): bool
    {
        return $this->column->isUnique;
    }

    public function getDefault(): ?string
    {
        return $this->column->default;
    }

    public function isNotNull(): bool
    {
        return $this->column->isNotNull;
    }

    public function getCheck(): ?string
    {
        return $this->column->check;
    }

    public function getComment(): ?string
    {
        return $this->column->comment;
    }

    public function getOptions(): array
    {
        $options = [];
        $options['unique'] = $this->isUnique();
        $options['nullable'] = !$this->isNotNull();

        if ($this->getDefault() !== null) {
            $options['default'] = $this->getDefault();
        }

        return $options;
    }
}
