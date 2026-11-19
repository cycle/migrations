<div align="center">
    <a href="https://cycle-orm.dev" target="_blank">
        <picture>
            <source media="(prefers-color-scheme: dark)" srcset="https://github.com/cycle/.github/blob/main/logo/words-vector-dark.svg?raw=true">
            <img width="50%" align="center" src="https://github.com/cycle/.github/blob/main/logo/words-vector-light.svg?raw=true" alt="CycleORM Logo">
        </picture>
    </a>
</div>

<p align="center">Database migrations and migration scaffolding for Cycle ORM</p>

<div align="center">

[![Documentation](https://img.shields.io/badge/Documentation-blue?style=for-the-badge&logo=gitbook&logoColor=white)](https://cycle-orm.dev/docs/database-migrations)
[![Sponsor](https://img.shields.io/static/v1?style=for-the-badge&label=&message=Sponsor&logo=githubsponsors&logoColor=white&color=%23EA4AAA)](https://github.com/sponsors/cycle)
[![Discord](https://img.shields.io/discord/538114875570913290?style=for-the-badge&logo=discord&labelColor=7289d9&logoColor=white&color=39456d)](https://discord.gg/spiralphp)

[![Psalm Level](https://shepherd.dev/github/cycle/migrations/level.svg)](https://shepherd.dev/github/cycle/migrations)
[![Type Coverage](https://shepherd.dev/github/cycle/migrations/coverage.svg)](https://shepherd.dev/github/cycle/migrations)
[![Codecov](https://img.shields.io/codecov/c/github/cycle/migrations/4.x?style=flat-square&logo=codecov)](https://codecov.io/gh/cycle/migrations/tree/4.x)

</div>

<br />

Migrations are a convenient way for you to alter your database in a structured and organized manner. This package adds
additional functionality for versioning your database schema and easily deploying changes to it. It is a very easy to
use and a powerful tool.

## Get Started

### Installation

Requires PHP 8.2+.

```bash
composer require cycle/migrations
```

[![PHP](https://img.shields.io/packagist/php-v/cycle/migrations.svg?style=flat-square&logo=php)](https://packagist.org/packages/cycle/migrations)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/cycle/migrations.svg?style=flat-square&logo=packagist)](https://packagist.org/packages/cycle/migrations)
[![License](https://img.shields.io/packagist/l/cycle/migrations.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/cycle/migrations.svg?style=flat-square)](https://packagist.org/packages/cycle/migrations/stats)

### Configuration

```php
use Cycle\Migrations;
use Cycle\Database;
use Cycle\Database\Config;

$dbal = new Database\DatabaseManager(new Config\DatabaseConfig([
    'default' => 'default',
    'databases' => [
        'default' => [
            'connection' => 'sqlite'
        ]
    ],
    'connections' => [
        'sqlite' => new Config\SQLiteDriverConfig(
            connection: new Config\SQLite\MemoryConnectionConfig(),
            queryCache: true,
        ),
    ]
]));

$config = new Migrations\Config\MigrationConfig([
    'directory' => __DIR__ . '/../migrations/',    // where to store migrations
    'vendorDirectories' => [                       // Where to look for vendor package migrations
        __DIR__ . '/../vendor/vendorName/packageName/migrations/'
    ],
    'table' => 'migrations',                      // database table to store migration status
    'safe' => true,                               // When set to true no confirmation will be requested on migration run. 
]);

$migrator = new Migrations\Migrator(
    $config, 
    $dbal, 
    new Migrations\FileRepository($config)
);

// Init migration table
$migrator->configure();
```

### Running

```php
while (($migration = $migrator->run()) !== null) {
    echo 'Migrate ' . $migration->getState()->getName();
}
```

## Generate Migrations

You can automatically generate a set of migration files during schema compilation. In this case, you have the freedom to
alter such migrations manually before running them. To achieve that you must install
the [Schema migrations generator extension](https://github.com/cycle/schema-migrations-generator).
