<?php

declare(strict_types=1);

namespace Cycle\Migrations\V2;

class FKAction
{
    public const CASCADE = 'CASCADE';
    public const SET_NULL = 'SET NULL';
    public const SET_DEFAULT = 'SET DEFAULT';
    public const RESTRICT = 'RESTRICT';
    public const NO_ACTION = 'NO ACTION';

    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function Cascade(): self
    {
        return new self(self::CASCADE);
    }

    public static function SetNull(): self
    {
        return new self(self::SET_NULL);
    }

    public static function SetDefault(): self
    {
        return new self(self::SET_DEFAULT);
    }

    public static function Restrict(): self
    {
        return new self(self::RESTRICT);
    }

    public static function NoAction(): self
    {
        return new self(self::NO_ACTION);
    }

    public function value(): string
    {
        return $this->value;
    }
}
