<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\MySQL;

class AtomizerTest extends \Cycle\Migrations\Tests\AtomizerTest
{
    public const DRIVER = 'mysql';

    public function testChangeBinaryColumnSize(): void
    {
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->binary('value', size: 16);
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        $this->assertSame(16, $this->schema('sample')->column('value')->getSize());

        $schema = $this->schema('sample');
        $schema->binary('value', size: 255);
        $this->atomize('migration2', [$schema]);

        $this->migrator->run();

        $this->assertSame(255, $this->schema('sample')->column('value')->getSize());

        $this->assertTrue($this->db->hasTable('sample'));

        $this->migrator->rollback();
        $this->assertSame(16, $this->schema('sample')->column('value')->getSize());

        $this->assertTrue($this->db->hasTable('sample'));

        $this->migrator->rollback();
        $this->assertFalse($this->db->hasTable('sample'));
    }

    public function testUnsigned(): void
    {
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('int');
        $schema->integer('int_unsigned', unsigned: true);
        $schema->tinyInteger('tiny_integer_unsigned', unsigned: true);
        $schema->smallInteger('small_integer_unsigned', unsigned: true);
        $schema->smallInteger('big_integer_unsigned', unsigned: true);
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        $this->assertFalse($this->schema('sample')->column('int')->isUnsigned());
        $this->assertTrue($this->schema('sample')->column('int_unsigned')->isUnsigned());
        $this->assertTrue($this->schema('sample')->column('tiny_integer_unsigned')->isUnsigned());
        $this->assertTrue($this->schema('sample')->column('small_integer_unsigned')->isUnsigned());
        $this->assertTrue($this->schema('sample')->column('big_integer_unsigned')->isUnsigned());

        $schema = $this->schema('sample');
        $schema->integer('int', unsigned: true);
        $schema->integer('int_unsigned', unsigned: false);
        $schema->tinyInteger('tiny_integer_unsigned', unsigned: false);
        $schema->smallInteger('small_integer_unsigned', unsigned: false);
        $schema->smallInteger('big_integer_unsigned', unsigned: false);
        $this->atomize('migration2', [$schema]);

        $this->migrator->run();

        $this->assertTrue($this->schema('sample')->column('int')->isUnsigned());
        $this->assertFalse($this->schema('sample')->column('int_unsigned')->isUnsigned());
        $this->assertFalse($this->schema('sample')->column('tiny_integer_unsigned')->isUnsigned());
        $this->assertFalse($this->schema('sample')->column('small_integer_unsigned')->isUnsigned());
        $this->assertFalse($this->schema('sample')->column('big_integer_unsigned')->isUnsigned());

        $this->migrator->rollback();

        $this->assertFalse($this->schema('sample')->column('int')->isUnsigned());
        $this->assertTrue($this->schema('sample')->column('int_unsigned')->isUnsigned());
        $this->assertTrue($this->schema('sample')->column('tiny_integer_unsigned')->isUnsigned());
        $this->assertTrue($this->schema('sample')->column('small_integer_unsigned')->isUnsigned());
        $this->assertTrue($this->schema('sample')->column('big_integer_unsigned')->isUnsigned());

        $this->migrator->rollback();
        $this->assertFalse($this->db->hasTable('sample'));
    }

    public function testZerofill(): void
    {
        $this->migrator->configure();

        $schema = $this->schema('sample');
        $schema->primary('id');
        $schema->integer('int');
        $schema->integer('int_zerofill', zerofill: true);
        $schema->tinyInteger('tiny_integer_zerofill', zerofill: true);
        $schema->smallInteger('small_integer_zerofill', zerofill: true);
        $schema->smallInteger('big_integer_zerofill', zerofill: true);
        $this->atomize('migration1', [$schema]);

        $this->migrator->run();
        $this->assertFalse($this->schema('sample')->column('int')->isZerofill());
        $this->assertTrue($this->schema('sample')->column('int_zerofill')->isZerofill());
        $this->assertTrue($this->schema('sample')->column('tiny_integer_zerofill')->isZerofill());
        $this->assertTrue($this->schema('sample')->column('small_integer_zerofill')->isZerofill());
        $this->assertTrue($this->schema('sample')->column('big_integer_zerofill')->isZerofill());

        $schema = $this->schema('sample');
        $schema->integer('int', zerofill: true);
        $schema->integer('int_zerofill', zerofill: false);
        $schema->tinyInteger('tiny_integer_zerofill', zerofill: false);
        $schema->smallInteger('small_integer_zerofill', zerofill: false);
        $schema->smallInteger('big_integer_zerofill', zerofill: false);
        $this->atomize('migration2', [$schema]);

        $this->migrator->run();

        $this->assertTrue($this->schema('sample')->column('int')->isZerofill());
        $this->assertFalse($this->schema('sample')->column('int_zerofill')->isZerofill());
        $this->assertFalse($this->schema('sample')->column('tiny_integer_zerofill')->isZerofill());
        $this->assertFalse($this->schema('sample')->column('small_integer_zerofill')->isZerofill());
        $this->assertFalse($this->schema('sample')->column('big_integer_zerofill')->isZerofill());

        $this->migrator->rollback();

        $this->assertFalse($this->schema('sample')->column('int')->isZerofill());
        $this->assertTrue($this->schema('sample')->column('int_zerofill')->isZerofill());
        $this->assertTrue($this->schema('sample')->column('tiny_integer_zerofill')->isZerofill());
        $this->assertTrue($this->schema('sample')->column('small_integer_zerofill')->isZerofill());
        $this->assertTrue($this->schema('sample')->column('big_integer_zerofill')->isZerofill());

        $this->migrator->rollback();
        $this->assertFalse($this->db->hasTable('sample'));
    }
}
