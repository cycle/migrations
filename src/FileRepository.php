<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations;

use Doctrine\Common\Inflector\Inflector;
use Spiral\Core\FactoryInterface;
use Spiral\Files\Files;
use Spiral\Files\FilesInterface;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\Exception\RepositoryException;
use Spiral\Tokenizer\TokenizerInterface;

/**
 * Stores migrations as files.
 */
class FileRepository implements RepositoryInterface
{
    // Migrations file name format. This format will be used when requesting new migration filename.
    private const FILENAME_FORMAT = '%s_%s_%s.php';

    // Timestamp format for files.
    private const TIMESTAMP_FORMAT = 'Ymd.His';

    /** @var MigrationConfig */
    private $config = null;

    /**
     * Required when multiple migrations added at once.
     *
     * @var int
     */
    private $chunkID = 0;

    /** @var FactoryInterface */
    private $factory;

    /** @var TokenizerInterface */
    private $tokenizer;

    /** @var FilesInterface */
    private $files;

    /**
     * @param MigrationConfig    $config
     * @param FactoryInterface   $factory
     * @param TokenizerInterface $tokenizer
     */
    public function __construct(
        MigrationConfig $config,
        FactoryInterface $factory,
        TokenizerInterface $tokenizer
    ) {
        $this->config = $config;

        $this->tokenizer = $tokenizer;
        $this->files = new Files();
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrations(): array
    {
        $migrations = [];

        foreach ($this->getFiles() as $f) {
            if (!class_exists($f['class'], false)) {
                //Attempting to load migration class (we can not relay on autoloading here)
                require_once($f['filename']);
            }

            if (!$f['created'] instanceof \DateTime) {
                throw new RepositoryException("Invalid migration filename '{$f['filename']}'");
            }

            /** @var MigrationInterface $migration */
            $migration = $this->factory->make($f['class']);

            $migrations[$f['created']->getTimestamp() . $f['chunk']] = $migration->withState(
                new State($f['name'], $f['created'])
            );
        }

        ksort($migrations);

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

        foreach ($this->getMigrations() as $migration) {
            if (get_class($migration) == $class) {
                throw new RepositoryException(
                    "Unable to register migration '{$class}', migration already exists"
                );
            }

            if ($migration->getState()->getName() == $name) {
                throw new RepositoryException(
                    "Unable to register migration '{$name}', migration under the same name already exists"
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
            $reflection = $this->tokenizer->fileReflection($filename);
            $definition = explode('_', basename($filename));

            yield [
                'filename' => $filename,
                'class'    => $reflection->getClasses()[0],
                'created'  => \DateTime::createFromFormat(
                    self::TIMESTAMP_FORMAT,
                    $definition[0]
                ),
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
        $name = Inflector::tableize($name);

        $filename = sprintf(self::FILENAME_FORMAT,
            date(self::TIMESTAMP_FORMAT),
            $this->chunkID++,
            $name
        );

        return $this->files->normalizePath(
            $this->config->getDirectory() . FilesInterface::SEPARATOR . $filename
        );
    }
}