<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Unit;

use Cycle\Database\Schema\AbstractColumn;
use Cycle\Migrations\Atomizer\Renderer;

final class RendererTest
{
    #[\Testo\Test]
    public function testColumnOptionsForEnumColumn(): void
    {
        $column = self::column(nullable: true, default: 'one', attributes: [], type: 'enum', enum: ['one', 'two', 'three']);

        $renderer = new Renderer();

        $method = new \ReflectionMethod($renderer, 'columnOptions');
        $method->setAccessible(true);

        $options = $method->invoke($renderer, $column);

        \Testo\Assert::array($options)->hasKeys('nullable')->hasKeys('defaultValue')->hasKeys('values');
        \Testo\Assert::true($options['nullable']);
        \Testo\Assert::same($options['defaultValue'], 'one');
        \Testo\Assert::same($options['values'], ['one', 'two', 'three']);
    }

    #[\Testo\Test]
    public function testColumnOptionsWithDateTimeNow(): void
    {
        $column = self::column(nullable: false, default: AbstractColumn::DATETIME_NOW, attributes: [], type: 'datetime');

        $renderer = new Renderer();

        $method = new \ReflectionMethod($renderer, 'columnOptions');
        $method->setAccessible(true);

        $options = $method->invoke($renderer, $column);

        \Testo\Assert::array($options)->hasKeys('nullable')->hasKeys('defaultValue');
        \Testo\Assert::false($options['nullable']);
        \Testo\Assert::same($options['defaultValue'], AbstractColumn::DATETIME_NOW);
    }

    #[\Testo\Test]
    public function testColumnOptionsSizeNotZeroIsAccepted(): void
    {
        $column = self::column(nullable: true, default: null, attributes: ['size' => 11], type: 'integer');

        $renderer = new Renderer();

        $method = new \ReflectionMethod($renderer, 'columnOptions');
        $method->setAccessible(true);

        $options = $method->invoke($renderer, $column);

        \Testo\Assert::array($options)->hasKeys('nullable')->hasKeys('defaultValue')->hasKeys('size');
        \Testo\Assert::true($options['nullable']);
        \Testo\Assert::null($options['defaultValue']);
        \Testo\Assert::same($options['size'], 11);
    }

    #[\Testo\Test]
    public function testColumnOptionsSizeZeroIsIgnored(): void
    {
        $column = self::column(nullable: true, default: null, attributes: ['size' => 0], type: 'integer');

        $renderer = new Renderer();

        $method = new \ReflectionMethod($renderer, 'columnOptions');
        $method->setAccessible(true);

        $options = $method->invoke($renderer, $column);

        \Testo\Assert::array($options)->hasKeys('nullable')->hasKeys('defaultValue')->doesNotHaveKeys('size');
        \Testo\Assert::true($options['nullable']);
        \Testo\Assert::null($options['defaultValue']);
    }

    #[\Testo\Test]
    public function testColumnOptionsAfterNotEmptyStringIsAccepted(): void
    {
        $column = self::column(nullable: true, default: null, attributes: ['after' => 'email'], type: 'integer');

        $renderer = new Renderer();

        $method = new \ReflectionMethod($renderer, 'columnOptions');
        $method->setAccessible(true);

        $options = $method->invoke($renderer, $column);

        \Testo\Assert::array($options)->hasKeys('nullable')->hasKeys('defaultValue')->hasKeys('after');
        \Testo\Assert::true($options['nullable']);
        \Testo\Assert::null($options['defaultValue']);
        \Testo\Assert::same($options['after'], 'email');
    }

    #[\Testo\Test]
    public function testColumnOptionsAfterEmptyStringIsIgnored(): void
    {
        $column = self::column(nullable: true, default: null, attributes: ['after' => ''], type: 'integer');

        $renderer = new Renderer();

        $method = new \ReflectionMethod($renderer, 'columnOptions');
        $method->setAccessible(true);

        $options = $method->invoke($renderer, $column);

        \Testo\Assert::array($options)->hasKeys('nullable')->hasKeys('defaultValue')->doesNotHaveKeys('after');
        \Testo\Assert::true($options['nullable']);
        \Testo\Assert::null($options['defaultValue']);
    }

    #[\Testo\Test]
    public function testColumnOptionsFirstNotFalseIsAccepted(): void
    {
        $column = self::column(nullable: true, default: null, attributes: ['first' => true], type: 'integer');

        $renderer = new Renderer();

        $method = new \ReflectionMethod($renderer, 'columnOptions');
        $method->setAccessible(true);

        $options = $method->invoke($renderer, $column);

        \Testo\Assert::array($options)->hasKeys('nullable')->hasKeys('defaultValue')->hasKeys('first');
        \Testo\Assert::true($options['nullable']);
        \Testo\Assert::null($options['defaultValue']);
        \Testo\Assert::true($options['first']);
    }

    #[\Testo\Test]
    public function testColumnOptionsFirstFalseIsIgnored(): void
    {
        $column = self::column(nullable: true, default: null, attributes: ['first' => false], type: 'integer');

        $renderer = new Renderer();

        $method = new \ReflectionMethod($renderer, 'columnOptions');
        $method->setAccessible(true);

        $options = $method->invoke($renderer, $column);

        \Testo\Assert::array($options)->hasKeys('nullable')->hasKeys('defaultValue')->doesNotHaveKeys('first');
        \Testo\Assert::true($options['nullable']);
        \Testo\Assert::null($options['defaultValue']);
    }
    private static function column(bool $nullable, mixed $default, array $attributes, string $type, array $enum = []): AbstractColumn
    {
        return new class ($nullable, $default, $attributes, $type, $enum) extends AbstractColumn {
            public function __construct(
                private readonly bool $fakeNullable,
                private readonly mixed $fakeDefault,
                private readonly array $fakeAttributes,
                private readonly string $fakeType,
                private readonly array $fakeEnum,
            ) {}

            public function isNullable(): bool
            {
                return $this->fakeNullable;
            }

            public function getDefaultValue(): mixed
            {
                return $this->fakeDefault;
            }

            public function getAttributes(): array
            {
                return $this->fakeAttributes;
            }

            public function getAbstractType(): string
            {
                return $this->fakeType;
            }

            public function getEnumValues(): array
            {
                return $this->fakeEnum;
            }
        };
    }
}
