<?php

declare(strict_types=1);

namespace Cycle\Migrations\V2;

class Column
{
    protected string $type;
    protected ?int $length = null;
    protected bool $isUnique = false;
    protected ?string $default = null;
    protected bool $isNotNull = false;
    protected ?string $check = null;
    protected ?string $comment = null;

    public function __construct(string $type, ?int $length = null)
    {
        $this->type = $type;
        $this->length = $length;
    }

    public function notNull(): self
    {
        $this->isNotNull = true;
        return $this;
    }

    public function null(): self
    {
        $this->isNotNull = false;
        return $this;
    }

    public function unique(): self
    {
        $this->isUnique = true;
        return $this;
    }

    public function check($check): self
    {
        $this->check = $check;
        return $this;
    }

    public function defaultValue(?string $default): self
    {
        if ($default === null) {
            $this->null();
        }

        $this->default = $default;
        return $this;
    }

    public function comment(?string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }
}
