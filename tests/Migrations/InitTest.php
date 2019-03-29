<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Migrations\Tests;

use Cycle\Annotated\Columns;
use Cycle\Annotated\Entities;
use Cycle\Annotated\Generator;
use Cycle\Annotated\Indexes;
use Cycle\Migrations\GenerateMigrations;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\CleanTables;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class InitTest extends BaseTest
{
    public function testInvalidRelation()
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/Fixtures/Init'],
            'exclude'     => [],
        ]));

        $locator = $tokenizer->classLocator();

        $p = Generator::defaultParser();
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($locator, $p),
            new CleanTables(),
            new Columns($p),
            GenerateRelations::defaultGenerator(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new Indexes($p),
            new GenerateTypecast(),
            new GenerateMigrations($this->migrator->getRepository())
        ]);

        $this->assertCount(2, $this->migrator->getMigrations());
        foreach ($this->migrator->getMigrations() as $m) {
            $this->migrator->run();
        }

        // verify the state
        foreach ($r as $e) {
            $this->assertSameAsInDB($r->getTableSchema($e));
        }
    }
}