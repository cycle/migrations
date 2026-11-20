<?php

declare(strict_types=1);

namespace Cycle\Migrations\Tests\Unit;

use Cycle\Migrations\Config\MigrationConfig;
use Cycle\Migrations\FileRepository;
use Testo\Assert;
use Testo\Test;

#[Test]
final class FileRepositoryTest
{
    public function directoriesWithoutPhpFilesHoldNoMigrations(): void
    {
        $files = \dirname(__DIR__) . '/files/';
        $repository = new FileRepository(new MigrationConfig([
            'directory' => $files,
            'vendorDirectories' => [$files . 'vendor/'],
        ]));

        Assert::same($repository->getMigrations(), []);
    }
}
