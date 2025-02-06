<?php

declare(strict_types=1);

namespace Cycle\Migrations;

use DateTimeInterface;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\Rules\English\InflectorFactory;
use Spiral\Core\Container;
use Spiral\Core\FactoryInterface;
use Spiral\Files\Files;
use Spiral\Files\FilesInterface;
use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\Exception\RepositoryException;
use Spiral\Tokenizer\Reflection\ReflectionFile;

/**
 * Stores migrations as files.
 *
 * @psalm-type TFileArray = array{
 *     filename: non-empty-string,
 *     class: class-string,
 *     created: DateTimeInterface|null,
 *     chunk: string,
 *     name: non-empty-string
 * }
 */
final class FileRepository implements RepositoryInterface
{
    // Migrations file name format. This format will be used when requesting new migration filename.
    private const FILENAME_FORMAT = '%s_%s_%s.php';

    // Timestamp format for files.
    private const TIMESTAMP_FORMAT = 'Ymd.His';

    private int $chunkID = 0;
    private FactoryInterface $factory;
    private FilesInterface $files;
    private Inflector $inflector;

    public function __construct(private MigrationConfig $config, ?FactoryInterface $factory = null)
    {
        $this->files = new Files();
        $this->factory = $factory ?? new Container();
        $this->inflector = (new InflectorFactory())->build();
    }

    public function getMigrations(): array
    {
        $timestamps = [];
        $chunks = [];
        $migrations = [];

        foreach ($this->getFilesIterator() as $f) {
            if (!\class_exists($f['class'], false)) {
                //Attempting to load migration class (we can not relay on autoloading here)
                require_once($f['filename']);
            }

            /** @var MigrationInterface $migration */
            $migration = $this->factory->make($f['class']);

            $timestamps[] = $f['created']->getTimestamp();
            $chunks[] = $f['chunk'];
            $migrations[] = $migration->withState(new State($f['name'], $f['created']));
        }

        \array_multisort($timestamps, $chunks, SORT_NATURAL, $migrations);

        return $migrations;
    }

    public function registerMigration(string $name, string $class, ?string $body = null): string
    {
        if (empty($body) && !\class_exists($class)) {
            throw new RepositoryException(
                "Unable to register migration '{$class}', representing class does not exists",
            );
        }

        $currentTimeStamp = \date(self::TIMESTAMP_FORMAT);
        $inflectedName = $this->inflector->tableize($name);

        foreach ($this->getMigrations() as $migration) {
            if ($migration::class === $class) {
                throw new RepositoryException(
                    "Unable to register migration '{$class}', migration already exists",
                );
            }

            if (
                $migration->getState()->getName() === $inflectedName
                && $migration->getState()->getTimeCreated()->format(self::TIMESTAMP_FORMAT) === $currentTimeStamp
            ) {
                throw new RepositoryException(
                    "Unable to register migration '{$inflectedName}', migration under the same name already exists",
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
     * @return \Generator<int, TFileArray>
     */
    private function getFilesIterator(): \Generator
    {
        foreach (
            \array_merge(
                [$this->config->getDirectory()],
                $this->config->getVendorDirectories(),
            ) as $directory
        ) {
            $directory === '' and $directory = '.';
            yield from $this->getFiles($directory);
        }
    }

    /**
     * Internal method to fetch all migration filenames.
     *
     * @param non-empty-string $directory
     *
     * @return \Generator<int, TFileArray>
     */
    private function getFiles(string $directory): \Generator
    {
        foreach ($this->files->getFiles($directory, '*.php') as $filename) {
            $reflection = new ReflectionFile($filename);
            $definition = \explode('_', \basename($filename, '.php'), 3);

            if (\count($definition) < 3) {
                throw new RepositoryException("Invalid migration filename '{$filename}'");
            }

            $created = \DateTime::createFromFormat(self::TIMESTAMP_FORMAT, $definition[0]);
            if ($created === false) {
                throw new RepositoryException("Invalid migration filename '{$filename}' - corrupted date format");
            }

            yield [
                'filename' => $filename,
                'class' => $reflection->getClasses()[0],
                'created' => $created,
                'chunk' => $definition[1],
                'name' => $definition[2],
            ];
        }
    }

    /**
     * Request new migration filename based on user input and current timestamp.
     */
    private function createFilename(string $name): string
    {
        $name = $this->inflector->tableize($name);

        $filename = \sprintf(
            self::FILENAME_FORMAT,
            \date(self::TIMESTAMP_FORMAT),
            $this->chunkID++,
            $name,
        );

        return $this->files->normalizePath(
            $this->config->getDirectory() . FilesInterface::SEPARATOR . $filename,
        );
    }
}
