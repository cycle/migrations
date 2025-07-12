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
    public function testColumnOptionsForEnumColumn(): void
    {
        $column = $this->createMock(AbstractColumn::class);
        $column->method('isNullable')->willReturn(true);
        $column->method('getDefaultValue')->willReturn('one');
        $column->method('getAttributes')->willReturn([]);
        $column->method('getAbstractType')->willReturn('enum');
        $column->method('getEnumValues')->willReturn(['one', 'two', 'three']);

        $renderer = new Renderer();

        $method = new \ReflectionMethod($renderer, 'columnOptions');
        $method->setAccessible(true);

        $options = $method->invoke($renderer, $column);

        $this->assertArrayHasKey('nullable', $options);
        $this->assertArrayHasKey('defaultValue', $options);
        $this->assertArrayHasKey('values', $options);
        $this->assertTrue($options['nullable']);
        $this->assertSame('one', $options['defaultValue']);
        $this->assertSame(['one', 'two', 'three'], $options['values']);
    }

    public function testColumnOptionsWithDateTimeNow(): void
    {
        $column = $this->createMock(AbstractColumn::class);
        $column->method('isNullable')->willReturn(false);
        $column->method('getDefaultValue')->willReturn(AbstractColumn::DATETIME_NOW);
        $column->method('getAttributes')->willReturn([]);
        $column->method('getAbstractType')->willReturn('datetime');

        $renderer = new Renderer();

        $method = new \ReflectionMethod($renderer, 'columnOptions');
        $method->setAccessible(true);

        $options = $method->invoke($renderer, $column);

        $this->assertArrayHasKey('nullable', $options);
        $this->assertArrayHasKey('defaultValue', $options);
        $this->assertFalse($options['nullable']);
        $this->assertSame(AbstractColumn::DATETIME_NOW, $options['defaultValue']);
    }

    public function testColumnOptionsSizeNotZeroIsAccepted(): void
    {
        $column = $this->createMock(AbstractColumn::class);
        $column->method('isNullable')->willReturn(true);
        $column->method('getDefaultValue')->willReturn(null);
        $column->method('getAttributes')->willReturn(['size' => 11]);
        $column->method('getAbstractType')->willReturn('integer');

        $renderer = new Renderer();

        $method = new \ReflectionMethod($renderer, 'columnOptions');
        $method->setAccessible(true);

        $options = $method->invoke($renderer, $column);

        $this->assertArrayHasKey('nullable', $options);
        $this->assertArrayHasKey('defaultValue', $options);
        $this->assertArrayHasKey('size', $options);
        $this->assertTrue($options['nullable']);
        $this->assertNull($options['defaultValue']);
        $this->assertSame(11, $options['size']);
    }

    public function testColumnOptionsSizeZeroIsIgnored(): void
    {
        $column = $this->createMock(AbstractColumn::class);
        $column->method('isNullable')->willReturn(true);
        $column->method('getDefaultValue')->willReturn(null);
        $column->method('getAttributes')->willReturn(['size' => 0]);
        $column->method('getAbstractType')->willReturn('integer');

        $renderer = new Renderer();

        $method = new \ReflectionMethod($renderer, 'columnOptions');
        $method->setAccessible(true);

        $options = $method->invoke($renderer, $column);

        $this->assertArrayHasKey('nullable', $options);
        $this->assertArrayHasKey('defaultValue', $options);
        $this->assertArrayNotHasKey('size', $options);
        $this->assertTrue($options['nullable']);
        $this->assertNull($options['defaultValue']);
    }

    public function testColumnOptionsAfterNotEmptyStringIsAccepted(): void
    {
        $column = $this->createMock(AbstractColumn::class);
        $column->method('isNullable')->willReturn(true);
        $column->method('getDefaultValue')->willReturn(null);
        $column->method('getAttributes')->willReturn(['after' => 'email']);
        $column->method('getAbstractType')->willReturn('integer');

        $renderer = new Renderer();

        $method = new \ReflectionMethod($renderer, 'columnOptions');
        $method->setAccessible(true);

        $options = $method->invoke($renderer, $column);

        $this->assertArrayHasKey('nullable', $options);
        $this->assertArrayHasKey('defaultValue', $options);
        $this->assertArrayHasKey('after', $options);
        $this->assertTrue($options['nullable']);
        $this->assertNull($options['defaultValue']);
        $this->assertSame('email', $options['after']);
    }

    public function testColumnOptionsAfterEmptyStringIsIgnored(): void
    {
        $column = $this->createMock(AbstractColumn::class);
        $column->method('isNullable')->willReturn(true);
        $column->method('getDefaultValue')->willReturn(null);
        $column->method('getAttributes')->willReturn(['after' => '']);
        $column->method('getAbstractType')->willReturn('integer');

        $renderer = new Renderer();

        $method = new \ReflectionMethod($renderer, 'columnOptions');
        $method->setAccessible(true);

        $options = $method->invoke($renderer, $column);

        $this->assertArrayHasKey('nullable', $options);
        $this->assertArrayHasKey('defaultValue', $options);
        $this->assertArrayNotHasKey('after', $options);
        $this->assertTrue($options['nullable']);
        $this->assertNull($options['defaultValue']);
    }

    public function testColumnOptionsFirstNotFalseIsAccepted(): void
    {
        $column = $this->createMock(AbstractColumn::class);
        $column->method('isNullable')->willReturn(true);
        $column->method('getDefaultValue')->willReturn(null);
        $column->method('getAttributes')->willReturn(['first' => true]);
        $column->method('getAbstractType')->willReturn('integer');

        $renderer = new Renderer();

        $method = new \ReflectionMethod($renderer, 'columnOptions');
        $method->setAccessible(true);

        $options = $method->invoke($renderer, $column);

        $this->assertArrayHasKey('nullable', $options);
        $this->assertArrayHasKey('defaultValue', $options);
        $this->assertArrayHasKey('first', $options);
        $this->assertTrue($options['nullable']);
        $this->assertNull($options['defaultValue']);
        $this->assertTrue($options['first']);
    }

    public function testColumnOptionsFirstFalseIsIgnored(): void
    {
        $column = $this->createMock(AbstractColumn::class);
        $column->method('isNullable')->willReturn(true);
        $column->method('getDefaultValue')->willReturn(null);
        $column->method('getAttributes')->willReturn(['first' => false]);
        $column->method('getAbstractType')->willReturn('integer');

        $renderer = new Renderer();

        $method = new \ReflectionMethod($renderer, 'columnOptions');
        $method->setAccessible(true);

        $options = $method->invoke($renderer, $column);

        $this->assertArrayHasKey('nullable', $options);
        $this->assertArrayHasKey('defaultValue', $options);
        $this->assertArrayNotHasKey('first', $options);
        $this->assertTrue($options['nullable']);
        $this->assertNull($options['defaultValue']);
    }
}
