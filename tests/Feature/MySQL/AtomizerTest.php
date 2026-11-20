<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Feature\MySQL;

use Cycle\Migrations\Tests\Support\MigrationsTestCase;
use Testo\Assert;
use Testo\Test;

final class AtomizerTest extends MigrationsTestCase
{
    #[Test]
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
        Assert::false($this->schema('sample')->column('int')->isUnsigned());
        Assert::true($this->schema('sample')->column('int_unsigned')->isUnsigned());
        Assert::true($this->schema('sample')->column('tiny_integer_unsigned')->isUnsigned());
        Assert::true($this->schema('sample')->column('small_integer_unsigned')->isUnsigned());
        Assert::true($this->schema('sample')->column('big_integer_unsigned')->isUnsigned());

        $schema = $this->schema('sample');
        $schema->integer('int', unsigned: true);
        $schema->integer('int_unsigned', unsigned: false);
        $schema->tinyInteger('tiny_integer_unsigned', unsigned: false);
        $schema->smallInteger('small_integer_unsigned', unsigned: false);
        $schema->smallInteger('big_integer_unsigned', unsigned: false);
        $this->atomize('migration2', [$schema]);

        $this->migrator->run();

        Assert::true($this->schema('sample')->column('int')->isUnsigned());
        Assert::false($this->schema('sample')->column('int_unsigned')->isUnsigned());
        Assert::false($this->schema('sample')->column('tiny_integer_unsigned')->isUnsigned());
        Assert::false($this->schema('sample')->column('small_integer_unsigned')->isUnsigned());
        Assert::false($this->schema('sample')->column('big_integer_unsigned')->isUnsigned());

        $this->migrator->rollback();

        Assert::false($this->schema('sample')->column('int')->isUnsigned());
        Assert::true($this->schema('sample')->column('int_unsigned')->isUnsigned());
        Assert::true($this->schema('sample')->column('tiny_integer_unsigned')->isUnsigned());
        Assert::true($this->schema('sample')->column('small_integer_unsigned')->isUnsigned());
        Assert::true($this->schema('sample')->column('big_integer_unsigned')->isUnsigned());

        $this->migrator->rollback();
        Assert::false($this->db->hasTable('sample'));
    }
    #[Test]
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
        Assert::false($this->schema('sample')->column('int')->isZerofill());
        Assert::true($this->schema('sample')->column('int_zerofill')->isZerofill());
        Assert::true($this->schema('sample')->column('tiny_integer_zerofill')->isZerofill());
        Assert::true($this->schema('sample')->column('small_integer_zerofill')->isZerofill());
        Assert::true($this->schema('sample')->column('big_integer_zerofill')->isZerofill());

        $schema = $this->schema('sample');
        $schema->integer('int', zerofill: true);
        $schema->integer('int_zerofill', zerofill: false);
        $schema->tinyInteger('tiny_integer_zerofill', zerofill: false);
        $schema->smallInteger('small_integer_zerofill', zerofill: false);
        $schema->smallInteger('big_integer_zerofill', zerofill: false);
        $this->atomize('migration2', [$schema]);

        $this->migrator->run();

        Assert::true($this->schema('sample')->column('int')->isZerofill());
        Assert::false($this->schema('sample')->column('int_zerofill')->isZerofill());
        Assert::false($this->schema('sample')->column('tiny_integer_zerofill')->isZerofill());
        Assert::false($this->schema('sample')->column('small_integer_zerofill')->isZerofill());
        Assert::false($this->schema('sample')->column('big_integer_zerofill')->isZerofill());

        $this->migrator->rollback();

        Assert::false($this->schema('sample')->column('int')->isZerofill());
        Assert::true($this->schema('sample')->column('int_zerofill')->isZerofill());
        Assert::true($this->schema('sample')->column('tiny_integer_zerofill')->isZerofill());
        Assert::true($this->schema('sample')->column('small_integer_zerofill')->isZerofill());
        Assert::true($this->schema('sample')->column('big_integer_zerofill')->isZerofill());

        $this->migrator->rollback();
        Assert::false($this->db->hasTable('sample'));
    }
}
