Cycle Database Migrations
========
[![Latest Stable Version](https://poser.pugx.org/cycle/migrations/v/stable)](https://packagist.org/packages/cycle/migrations)
[![Build Status](https://github.com/cycle/migrations/workflows/build/badge.svg)](https://github.com/cycle/migrations/actions)
[![Codecov](https://codecov.io/gh/cycle/migrations/branch/3.x/graph/badge.svg)](https://codecov.io/gh/cycle/migrations/)

Migrations are a convenient way for you to alter your database in a structured and organized manner. This package adds
additional functionality for versioning your database schema and easily deploying changes to it. It is a very easy to
use and a powerful tool.

## Installation

```bash
composer require cycle/migrations ^3.0
```

## Configuration

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
    'table'     => 'migrations'                    // database table to store migration status
    'safe'      => true                            // When set to true no confirmation will be requested on migration run. 
]);
$migrator = new Migrations\Migrator(
    $config, 
    $dbal, 
    new Migrations\FileRepository($config)
);
// Init migration table
$migrator->configure();
```

## Running

```php
while (($migration = $migrator->run()) !== null) {
    echo 'Migrate ' . $migration->getState()->getName();
}
```

## Generate Migrations

You can automatically generate a set of migration files during schema compilation. In this case, you have the freedom to
alter such migrations manually before running them. To achieve that you must install
the [Cycle migrations extension](https://github.com/cycle/schema-migrations-generator).