<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Migrations;

use Spiral\Core\Container;
use Spiral\Core\FactoryInterface;
use Spiral\Files\Files;
use Spiral\Files\FilesInterface;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\Exception\RepositoryException;
use Spiral\Tokenizer\Reflection\ReflectionFile;

/**
 * Stores migrations as files.
 */
final class FileRepository implements RepositoryInterface
{
    // Migrations file name format. This format will be used when requesting new migration filename.
    private const FILENAME_FORMAT = '%s_%s_%s.php';

    // Timestamp format for files.
    private const TIMESTAMP_FORMAT = 'Ymd.His';

    /** @var MigrationConfig */
    private $config;

    /** @var int */
    private $chunkID = 0;

    /** @var FactoryInterface */
    private $factory;

    /** @var FilesInterface */
    private $files;

    /** @var \Doctrine\Inflector\Inflector */
    private $inflector;

    /**
     * @param MigrationConfig       $config
     * @param FactoryInterface|null $factory
     */
    public function __construct(MigrationConfig $config, FactoryInterface $factory = null)
    {
        $this->config = $config;
        $this->files = new Files();
        $this->factory = $factory ?? new Container();
        $this->inflector = (new \Doctrine\Inflector\Rules\English\InflectorFactory())->build();
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrations(): array
    {
        $timestamps = [];
        $chunks = [];
        $migrations = [];

        foreach ($this->getFiles() as $f) {
            if (!class_exists($f['class'], false)) {
                //Attempting to load migration class (we can not relay on autoloading here)
                require_once($f['filename']);
            }

            /** @var MigrationInterface $migration */
            $migration = $this->factory->make($f['class']);

            $timestamps[] = $f['created']->getTimestamp();
            $chunks[] = $f['chunk'];
            $migrations[] = $migration->withState(new State($f['name'], $f['created']));
        }

        array_multisort($timestamps, $chunks, SORT_ASC | SORT_NATURAL, $migrations);

        return $migrations;
    }

    /**
     * {@inheritdoc}
     */
    public function registerMigration(string $name, string $class, string $body = null): string
    {
        if (empty($body) && !class_exists($class)) {
            throw new RepositoryException(
                "Unable to register migration '{$class}', representing class does not exists"
            );
        }

        $currentTimeStamp = date(self::TIMESTAMP_FORMAT);
        $inflectedName = $this->inflector->tableize($name);

        foreach ($this->getMigrations() as $migration) {
            if (get_class($migration) === $class) {
                throw new RepositoryException(
                    "Unable to register migration '{$class}', migration already exists"
                );
            }

            if (
                $migration->getState()->getName() === $inflectedName
                && $migration->getState()->getTimeCreated()->format(self::TIMESTAMP_FORMAT) === $currentTimeStamp
            ) {
                throw new RepositoryException(
                    "Unable to register migration '{$inflectedName}', migration under the same name already exists"
                );
            }
        }

        if (empty($body)) {
            //Let's read body from a given class filename
            $body = $this->files->read((new \ReflectionClass($class))->getFileName());
        }

        $filename = $this->createFilename($name);

        //Copying
        $this->files->write($filename, $body, FilesInterface::READONLY, true);

        return $filename;
    }

    /**
     * Internal method to fetch all migration filenames.
     */
    private function getFiles(): \Generator
    {
        foreach ($this->files->getFiles($this->config->getDirectory(), '*.php') as $filename) {
            $reflection = new ReflectionFile($filename);
            $definition = explode('_', basename($filename));

            if (count($definition) < 3) {
                throw new RepositoryException("Invalid migration filename '{$filename}'");
            }

            $created = \DateTime::createFromFormat(self::TIMESTAMP_FORMAT, $definition[0]);
            if (false === $created) {
                throw new RepositoryException("Invalid migration filename '{$filename}' - corrupted date format");
            }

            yield [
                'filename' => $filename,
                'class'    => $reflection->getClasses()[0],
                'created'  => $created,
                'chunk'    => $definition[1],
                'name'     => str_replace(
                    '.php',
                    '',
                    join('_', array_slice($definition, 2))
                )
            ];
        }
    }

    /**
     * Request new migration filename based on user input and current timestamp.
     *
     * @param string $name
     * @return string
     */
    private function createFilename(string $name): string
    {
        $name = $this->inflector->tableize($name);

        $filename = sprintf(
            self::FILENAME_FORMAT,
            date(self::TIMESTAMP_FORMAT),
            $this->chunkID++,
            $name
        );

        return $this->files->normalizePath(
            $this->config->getDirectory() . FilesInterface::SEPARATOR . $filename
        );
    }
}
