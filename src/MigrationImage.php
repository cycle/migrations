<?php

namespace Cycle\Migrations;

use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\Migration;
use Spiral\Reactor\ClassDeclaration;
use Spiral\Reactor\FileDeclaration;

class MigrationImage
{
    /** @var ClassDeclaration */
    protected $class;
    /** @var FileDeclaration */
    protected $file;
    /** @var MigrationConfig */
    protected $migrationConfig;
    /** @var string */
    protected $database;
    /** @var string */
    protected $name = '';

    public $fileNamePattern = '{database}_{name}';

    public function __construct(MigrationConfig $config, string $database)
    {
        $this->migrationConfig = $config;
        $this->class = new ClassDeclaration('newMigration', 'Migration');

        $this->class->method('up')->setPublic();
        $this->class->method('down')->setPublic();

        $this->file = new FileDeclaration($config->getNamespace());
        $this->file->addUse(Migration::class);
        $this->file->addElement($this->class);

        $this->setDatabase($database);
    }

    public function getClass(): ClassDeclaration
    {
        return $this->class;
    }

    public function getFile(): FileDeclaration
    {
        return $this->file;
    }

    public function getMigrationConfig(): MigrationConfig
    {
        return $this->migrationConfig;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function setDatabase(string $database): void
    {
        $this->database = $database;

        $className = sprintf(
            'orm_%s_%s',
            $database,
            md5(microtime(true) . microtime(false))
        );
        $this->class->setName($className);

        $this->class->constant('DATABASE')->setProtected()->setValue($database);
    }

    public function buildFileName(): string
    {
        return str_replace(['{database}', '{name}'], [$this->database, $this->name], $this->fileNamePattern);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
