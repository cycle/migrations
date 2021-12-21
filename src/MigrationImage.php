<?php

declare(strict_types=1);

namespace Cycle\Migrations;

use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\Migration;
use Spiral\Reactor\ClassDeclaration;
use Spiral\Reactor\FileDeclaration;

class MigrationImage
{
    /** @var string */
    public $fileNamePattern = '{database}_{name}';
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

    /**
     * MigrationImage constructor.
     *
     * @param MigrationConfig $config
     * @param string          $database
     */
    public function __construct(MigrationConfig $config, string $database)
    {
        $this->migrationConfig = $config;
        $this->class = new ClassDeclaration('newMigration', 'Migration');

        $this->class->method('up')->setReturn('void')->setPublic();
        $this->class->method('down')->setReturn('void')->setPublic();

        $this->file = new FileDeclaration($config->getNamespace());
        $this->file->addUse(Migration::class);
        $this->file->addElement($this->class);

        $this->setDatabase($database);
    }

    /**
     * @return ClassDeclaration
     */
    public function getClass(): ClassDeclaration
    {
        return $this->class;
    }

    /**
     * @return FileDeclaration
     */
    public function getFile(): FileDeclaration
    {
        return $this->file;
    }

    /**
     * @return MigrationConfig
     */
    public function getMigrationConfig(): MigrationConfig
    {
        return $this->migrationConfig;
    }

    /**
     * @return string
     */
    public function getDatabase(): string
    {
        return $this->database;
    }

    /**
     * @param string $database
     */
    public function setDatabase(string $database): void
    {
        $this->database = $database;

        $className = sprintf(
            'orm_%s_%s',
            $database,
            md5(microtime(true).microtime(false))
        );
        $this->class->setName($className);

        $this->class->constant('DATABASE')->setProtected()->setValue($database);
    }

    /**
     * @return string
     */
    public function buildFileName(): string
    {
        return str_replace(['{database}', '{name}'], [$this->database, $this->name], $this->fileNamePattern);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
