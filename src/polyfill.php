<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

spl_autoload_register(static function (string $class) {
    if (strpos($class, 'Spiral\\Migrations\\') === 0) {
        $original = 'Cycle\\Migrations\\' . substr($class, 18);

        @trigger_error(
            "$class has been deprecated since cycle/migrations 1.0 " .
            "and will be removed in further release. Please use class $original instead.",
            E_USER_DEPRECATED
        );

        class_alias($original, $class);
    }
});

// Preload some aliases
class_exists(\Spiral\Database\Database::class);
class_exists(\Spiral\Database\DatabaseManager::class);
class_exists(\Spiral\Database\Schema\AbstractTable::class);

class_exists(\Spiral\Migrations\State::class);
class_exists(\Spiral\Migrations\Config\MigrationConfig::class);

interface_exists(\Spiral\Migrations\CapsuleInterface::class);
interface_exists(\Spiral\Migrations\OperationInterface::class);
interface_exists(\Spiral\Migrations\RepositoryInterface::class);
interface_exists(\Spiral\Migrations\Atomizer\RendererInterface::class);
