<?php

namespace Cycle\Migrations\Tests;

use Cycle\Migrations\MigrationImage;
use PHPUnit\Framework\TestCase;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Reactor\ClassDeclaration;
use Spiral\Reactor\FileDeclaration;

class MigrationImageTest extends TestCase
{
    /** @var MigrationImage */
    protected $migrationImage;
    /** @var MigrationConfig */
    protected static $defaultMigrationConfig;

    protected const DATABASE_DEFAULT = 'defaultDatabaseName';

    public static function setUpBeforeClass(): void
    {
        static::$defaultMigrationConfig = new MigrationConfig();
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->migrationImage = new MigrationImage(static::$defaultMigrationConfig, static::DATABASE_DEFAULT);
    }

    public function databaseData()
    {
        return [
            ['Default'],
            ['#$%^&*('],
            [98764310],
            [''],
        ];
    }

    public function migrationNameData()
    {
        return [
            ['simple'],
            ['camelCase'],
            [123],
            [''],
        ];
    }

    public function testRequires()
    {
        $class = $this->migrationImage->getClass();
        $constValue = $class->constant('DATABASE')->getValue();
        $this->assertEquals(static::DATABASE_DEFAULT, $constValue, 'Check the DATABASE constant');
        $this->assertNotEmpty($class->getName(), 'Class name is not empty');

        // check `up` and `down` methods
        $methods = $class->getMethods()->getIterator();
        $names = [];
        foreach ($methods as $method) {
            $names[] = $method->getName();
        }
        $this->assertContains('up', $names, 'Method up() exists');
        $this->assertContains('down', $names, 'Method down() exists');

        $file = $this->migrationImage->getFile();
        $elements = $file->getElements();
        $this->assertContains($class, $elements, 'The ClassDefinition exists in the FileDefinition');
    }

    public function testGetDatabase($database = MigrationImageTest::DATABASE_DEFAULT)
    {
        $this->assertEquals($database, $this->migrationImage->getDatabase(), 'Test the database getter');
    }

    /**
     * @dataProvider databaseData
     */
    public function testSetDatabase($database)
    {
        $this->migrationImage->setDatabase($database);

        $this->testGetDatabase($database);

        $constValue = $this->migrationImage->getClass()->constant('DATABASE')->getValue();
        $this->assertEquals($database, $constValue, 'DATABASE constant changed in the class declaration');
    }

    public function testGetMigrationConfig()
    {
        $this->assertEquals(static::$defaultMigrationConfig, $this->migrationImage->getMigrationConfig());
    }

    public function testGetClass()
    {
        $this->assertInstanceOf(ClassDeclaration::class, $this->migrationImage->getClass());
    }

    public function testGetFile()
    {
        $this->assertInstanceOf(FileDeclaration::class, $this->migrationImage->getFile());
    }

    public function testBuildFileName()
    {
        $this->substringInFileName(static::DATABASE_DEFAULT, '{database}', 'Default database name in the filename');

        $this->migrationImage->fileNamePattern = '';
        $this->assertEquals('', $this->migrationImage->buildFileName(), 'Empty pattern');
    }

    protected function substringInFileName(string $substr, ?string $pattern = null, string $message = '')
    {
        if (is_string($pattern)) {
            $this->migrationImage->fileNamePattern = $pattern;
        }
        $fileName = $this->migrationImage->buildFileName();
        $this->assertStringContainsStringIgnoringCase($substr, $fileName, $message);
    }

    /**
     * @param string $name Empty string by default because the migration name in the MigrationImage instance
     *                     is also empty string
     */
    public function testGetName(string $name = '')
    {
        $this->assertEquals($name, $this->migrationImage->getName(), 'Test the name getter');
    }

    /**
     * @dataProvider migrationNameData
     */
    public function testSetName($name)
    {
        $this->migrationImage->setName($name);

        $this->testGetName($name);

        if (strlen($name)) {
            $this->substringInFileName($name, '{name}', 'Migration name in the filename');
        }
    }
}
