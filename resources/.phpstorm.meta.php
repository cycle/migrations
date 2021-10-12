<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Spiral\Migrations {

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\CapsuleInterface instead.
     */
    interface CapsuleInterface extends \Cycle\Migrations\CapsuleInterface
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\MigrationInterface instead.
     */
    interface MigrationInterface extends \Cycle\Migrations\MigrationInterface
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\MigratorInterface instead.
     */
    interface MigratorInterface extends \Cycle\Migrations\MigratorInterface
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\OperationInterface instead.
     */
    interface OperationInterface extends \Cycle\Migrations\OperationInterface
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\RepositoryInterface instead.
     */
    interface RepositoryInterface extends \Cycle\Migrations\RepositoryInterface
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Capsule instead.
     */
    final class Capsule extends \Cycle\Migrations\Capsule
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\FileRepository instead.
     */
    final class FileRepository extends \Cycle\Migrations\FileRepository
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Migrator instead.
     */
    final class Migrator extends \Cycle\Migrations\Migrator
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\State instead.
     */
    final class State extends \Cycle\Migrations\State
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\TableBlueprint instead.
     */
    final class TableBlueprint extends \Cycle\Migrations\TableBlueprint
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Migration instead.
     */
    abstract class Migration extends \Cycle\Migrations\Migration
    {
    }
}

namespace Spiral\Migrations\Operation {

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\AbstractOperation instead.
     */
    abstract class AbstractOperation extends \Cycle\Migrations\Operation\AbstractOperation
    {
    }
}

namespace Spiral\Migrations\Operation\Column {

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\Column\Add instead.
     */
    final class Add extends \Cycle\Migrations\Operation\Column\Add
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\Column\Alter instead.
     */
    final class Alter extends \Cycle\Migrations\Operation\Column\Alter
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\Column\Drop instead.
     */
    final class Drop extends \Cycle\Migrations\Operation\Column\Drop
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\Column\Rename instead.
     */
    final class Rename extends \Cycle\Migrations\Operation\Column\Rename
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\Column\Column instead.
     */
    abstract class Column extends \Cycle\Migrations\Operation\Column\Column
    {
    }
}

namespace Spiral\Migrations\Operation\ForeignKey {

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\ForeignKey\Add instead.
     */
    final class Add extends \Cycle\Migrations\Operation\ForeignKey\Add
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\ForeignKey\Alter instead.
     */
    final class Alter extends \Cycle\Migrations\Operation\ForeignKey\Alter
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\ForeignKey\Drop instead.
     */
    final class Drop extends \Cycle\Migrations\Operation\ForeignKey\Drop
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\ForeignKey\ForeignKey instead.
     */
    abstract class ForeignKey extends \Cycle\Migrations\Operation\ForeignKey\ForeignKey
    {
    }
}

namespace Spiral\Migrations\Operation\Index {

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\Index\Add instead.
     */
    final class Add extends \Cycle\Migrations\Operation\Index\Add
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\Index\Alter instead.
     */
    final class Alter extends \Cycle\Migrations\Operation\Index\Alter
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\Index\Drop instead.
     */
    final class Drop extends \Cycle\Migrations\Operation\Index\Drop
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\Index\Index instead.
     */
    abstract class Index extends \Cycle\Migrations\Operation\Index\Index
    {
    }
}

namespace Spiral\Migrations\Operation\Table {

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\Table\Create instead.
     */
    final class Create extends \Cycle\Migrations\Operation\Table\Create
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\Table\Drop instead.
     */
    final class Drop extends \Cycle\Migrations\Operation\Table\Drop
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\Table\PrimaryKeys instead.
     */
    final class PrimaryKeys extends \Cycle\Migrations\Operation\Table\PrimaryKeys
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\Table\Rename instead.
     */
    final class Rename extends \Cycle\Migrations\Operation\Table\Rename
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\Table\Update instead.
     */
    final class Update extends \Cycle\Migrations\Operation\Table\Update
    {
    }
}

namespace Spiral\Migrations\Operation\Traits {

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Operation\Traits\OptionsTrait instead.
     */
    trait OptionsTrait
    {
        use \Cycle\Migrations\Operation\Traits\OptionsTrait;
    }
}

namespace Spiral\Migrations\Migrator {

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Migrator\MigrationsTable instead.
     */
    class MigrationsTable extends \Cycle\Migrations\Migrator\MigrationsTable
    {
    }
}

namespace Spiral\Migrations\Migration {

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Migration\DefinitionInterface instead.
     */
    interface DefinitionInterface extends \Cycle\Migrations\Migration\DefinitionInterface
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Migration\ProvidesSyncStateInterface instead.
     */
    interface ProvidesSyncStateInterface extends \Cycle\Migrations\Migration\ProvidesSyncStateInterface
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Migration\State instead.
     */
    class State extends \Cycle\Migrations\Migration\State
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Migration\Status instead.
     */
    final class Status extends \Cycle\Migrations\Migration\Status
    {
    }
}

namespace Spiral\Migrations\Exception {

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Exception\BlueprintException instead.
     */
    class BlueprintException extends \Cycle\Migrations\Exception\BlueprintException
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Exception\CapsuleException instead.
     */
    class CapsuleException extends \Cycle\Migrations\Exception\CapsuleException
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Exception\ContextException instead.
     */
    class ContextException extends \Cycle\Migrations\Exception\ContextException
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Exception\MigrationException instead.
     */
    class MigrationException extends \Cycle\Migrations\Exception\MigrationException
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Exception\OperationException instead.
     */
    class OperationException extends \Cycle\Migrations\Exception\OperationException
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Exception\RepositoryException instead.
     */
    class RepositoryException extends \Cycle\Migrations\Exception\RepositoryException
    {
    }
}

namespace Spiral\Migrations\Operation\Exception\Operation {

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Exception\Operation\ColumnException instead.
     */
    class ColumnException extends \Cycle\Migrations\Exception\Operation\ColumnException
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Exception\Operation\ForeignKeyException instead.
     */
    class ForeignKeyException extends \Cycle\Migrations\Exception\Operation\ForeignKeyException
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Exception\Operation\IndexException instead.
     */
    class IndexException extends \Cycle\Migrations\Exception\Operation\IndexException
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Exception\Operation\TableException instead.
     */
    class TableException extends \Cycle\Migrations\Exception\Operation\TableException
    {
    }
}

namespace Spiral\Migrations\Config {

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Config\MigrationConfig instead.
     */
    final class MigrationConfig extends \Cycle\Migrations\Config\MigrationConfig
    {
    }
}

namespace Spiral\Migrations\Atomizer {

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Atomizer\RendererInterface instead.
     */
    interface RendererInterface extends \Cycle\Migrations\Atomizer\RendererInterface
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Atomizer\Atomizer instead.
     */
    final class Atomizer extends \Cycle\Migrations\Atomizer\Atomizer
    {
    }

    /**
     * @deprecated since cycle/migrations 1.0, use Cycle\Migrations\Atomizer\Renderer instead.
     */
    final class Renderer extends \Cycle\Migrations\Atomizer\Renderer
    {
    }
}
