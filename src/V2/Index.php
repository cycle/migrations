<?php


declare(strict_types=1);

namespace Cycle\Migrations\V2;

class Index
{
    protected array $fields;
    protected ?string $name;
    protected bool $unique = false;

    public function __construct(array $fields, ?string $name = null)
    {
        $this->fields = $fields;
        $this->name = $name;
    }

    public function unique(): self
    {
        $this->unique = true;

        return $this;
    }

    public function notUnique(): self
    {
        $this->unique = false;

        return $this;
    }
}
