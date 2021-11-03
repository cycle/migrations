<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

use Cycle\Database\Config;

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');
mb_internal_encoding('UTF-8');

//Composer
require dirname(__DIR__) . '/vendor/autoload.php';

\Cycle\Migrations\Tests\BaseTest::$config = [
    'debug' => false,
    'sqlite' => new Config\SQLiteDriverConfig(),
    'mysql' => new Config\MySQLDriverConfig(
        new Config\MySQL\TcpConnectionConfig(
            'spiral',
            '127.0.0.1',
            13306,
            null,
            'root',
            'root'
        )
    ),
    'postgres' => new Config\PostgresDriverConfig(
        new Config\Postgres\TcpConnectionConfig(
            'spiral',
            '127.0.0.1',
            15432,
            'postgres',
            'postgres'
        )
    ),
    'sqlserver' => new Config\SQLServerDriverConfig(
        new Config\SQLServer\TcpConnectionConfig(
            'tempdb',
            '127.0.0.1',
            11433,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            'sa',
            'SSpaSS__1'
        )
    ),
];

if (!empty(getenv('DB'))) {
    switch (getenv('DB')) {
        case 'postgres':
            \Cycle\Migrations\Tests\BaseTest::$config = [
                'debug' => false,
                'postgres' => new Config\PostgresDriverConfig(
                    new Config\Postgres\TcpConnectionConfig(
                        'spiral',
                        '127.0.0.1',
                        5432,
                        'postgres',
                        'postgres'
                    )
                )
            ];
            break;

        case 'mariadb':
            \Cycle\Migrations\Tests\BaseTest::$config = [
                'debug' => false,
                'mysql' => new Config\MySQLDriverConfig(
                    new Config\MySQL\TcpConnectionConfig(
                        'spiral',
                        '127.0.0.1',
                        23306,
                        null,
                        'root',
                        'root'
                    )
                ),
            ];
            break;
    }
}
