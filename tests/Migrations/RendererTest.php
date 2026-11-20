<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests;

use Cycle\Database\Schema\AbstractColumn;
use Cycle\Migrations\Atomizer\Renderer;

abstract class RendererTest extends BaseTest
{
    #[\Testo\Test]
    public function testColumnOptionsForEnumColumn(): void
    {
        $column = \Mockery::mock(AbstractColumn::class)->shouldIgnoreMissing();
        $column->shouldReceive('isNullable')->andReturn(true);
        $column->shouldReceive('getDefaultValue')->andReturn('one');
        $column->shouldReceive('getAttributes')->andReturn([]);
        $column->shouldReceive('getAbstractType')->andReturn('enum');
        $column->shouldReceive('getEnumValues')->andReturn(['one', 'two', 'three']);

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
        $column = \Mockery::mock(AbstractColumn::class)->shouldIgnoreMissing();
        $column->shouldReceive('isNullable')->andReturn(false);
        $column->shouldReceive('getDefaultValue')->andReturn(AbstractColumn::DATETIME_NOW);
        $column->shouldReceive('getAttributes')->andReturn([]);
        $column->shouldReceive('getAbstractType')->andReturn('datetime');

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
        $column = \Mockery::mock(AbstractColumn::class)->shouldIgnoreMissing();
        $column->shouldReceive('isNullable')->andReturn(true);
        $column->shouldReceive('getDefaultValue')->andReturn(null);
        $column->shouldReceive('getAttributes')->andReturn(['size' => 11]);
        $column->shouldReceive('getAbstractType')->andReturn('integer');

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
        $column = \Mockery::mock(AbstractColumn::class)->shouldIgnoreMissing();
        $column->shouldReceive('isNullable')->andReturn(true);
        $column->shouldReceive('getDefaultValue')->andReturn(null);
        $column->shouldReceive('getAttributes')->andReturn(['size' => 0]);
        $column->shouldReceive('getAbstractType')->andReturn('integer');

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
        $column = \Mockery::mock(AbstractColumn::class)->shouldIgnoreMissing();
        $column->shouldReceive('isNullable')->andReturn(true);
        $column->shouldReceive('getDefaultValue')->andReturn(null);
        $column->shouldReceive('getAttributes')->andReturn(['after' => 'email']);
        $column->shouldReceive('getAbstractType')->andReturn('integer');

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
        $column = \Mockery::mock(AbstractColumn::class)->shouldIgnoreMissing();
        $column->shouldReceive('isNullable')->andReturn(true);
        $column->shouldReceive('getDefaultValue')->andReturn(null);
        $column->shouldReceive('getAttributes')->andReturn(['after' => '']);
        $column->shouldReceive('getAbstractType')->andReturn('integer');

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
        $column = \Mockery::mock(AbstractColumn::class)->shouldIgnoreMissing();
        $column->shouldReceive('isNullable')->andReturn(true);
        $column->shouldReceive('getDefaultValue')->andReturn(null);
        $column->shouldReceive('getAttributes')->andReturn(['first' => true]);
        $column->shouldReceive('getAbstractType')->andReturn('integer');

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
        $column = \Mockery::mock(AbstractColumn::class)->shouldIgnoreMissing();
        $column->shouldReceive('isNullable')->andReturn(true);
        $column->shouldReceive('getDefaultValue')->andReturn(null);
        $column->shouldReceive('getAttributes')->andReturn(['first' => false]);
        $column->shouldReceive('getAbstractType')->andReturn('integer');

        $renderer = new Renderer();

        $method = new \ReflectionMethod($renderer, 'columnOptions');
        $method->setAccessible(true);

        $options = $method->invoke($renderer, $column);

        \Testo\Assert::array($options)->hasKeys('nullable')->hasKeys('defaultValue')->doesNotHaveKeys('first');
        \Testo\Assert::true($options['nullable']);
        \Testo\Assert::null($options['defaultValue']);
    }
}
