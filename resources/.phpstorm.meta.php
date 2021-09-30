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
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\CapsuleInterface instead.
     */
    interface CapsuleInterface extends \Cycle\Migrations\CapsuleInterface
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\MigrationInterface instead.
     */
    interface MigrationInterface extends \Cycle\Migrations\MigrationInterface
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\MigratorInterface instead.
     */
    interface MigratorInterface extends \Cycle\Migrations\MigratorInterface
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\OperationInterface instead.
     */
    interface OperationInterface extends \Cycle\Migrations\OperationInterface
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\RepositoryInterface instead.
     */
    interface RepositoryInterface extends \Cycle\Migrations\RepositoryInterface
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Capsule instead.
     */
    final class Capsule extends \Cycle\Migrations\Capsule
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\FileRepository instead.
     */
    final class FileRepository extends \Cycle\Migrations\FileRepository
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Migrator instead.
     */
    final class Migrator extends \Cycle\Migrations\Migrator
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\State instead.
     */
    final class State extends \Cycle\Migrations\State
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\TableBlueprint instead.
     */
    final class TableBlueprint extends \Cycle\Migrations\TableBlueprint
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Migration instead.
     */
    abstract class Migration extends \Cycle\Migrations\Migration
    {
    }
}

namespace Spiral\Migrations\Operation {

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\AbstractOperation instead.
     */
    abstract class AbstractOperation extends \Cycle\Migrations\Operation\AbstractOperation
    {
    }
}

namespace Spiral\Migrations\Operation\Column {

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Column\Add instead.
     */
    final class Add extends \Cycle\Migrations\Operation\Column\Add
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Column\Alter instead.
     */
    final class Alter extends \Cycle\Migrations\Operation\Column\Alter
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Column\Drop instead.
     */
    final class Drop extends \Cycle\Migrations\Operation\Column\Drop
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Column\Rename instead.
     */
    final class Rename extends \Cycle\Migrations\Operation\Column\Rename
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Column\Column instead.
     */
    abstract class Column extends \Cycle\Migrations\Operation\Column\Column
    {
    }
}

namespace Spiral\Migrations\Operation\ForeignKey {

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\ForeignKey\Add instead.
     */
    final class Add extends \Cycle\Migrations\Operation\ForeignKey\Add
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\ForeignKey\Alter instead.
     */
    final class Alter extends \Cycle\Migrations\Operation\ForeignKey\Alter
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\ForeignKey\Drop instead.
     */
    final class Drop extends \Cycle\Migrations\Operation\ForeignKey\Drop
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\ForeignKey\ForeignKey instead.
     */
    abstract class ForeignKey extends \Cycle\Migrations\Operation\ForeignKey\ForeignKey
    {
    }
}

namespace Spiral\Migrations\Operation\Index {

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Index\Add instead.
     */
    final class Add extends \Cycle\Migrations\Operation\Index\Add
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Index\Alter instead.
     */
    final class Alter extends \Cycle\Migrations\Operation\Index\Alter
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Index\Drop instead.
     */
    final class Drop extends \Cycle\Migrations\Operation\Index\Drop
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Index\Index instead.
     */
    abstract class Index extends \Cycle\Migrations\Operation\Index\Index
    {
    }
}

namespace Spiral\Migrations\Operation\Table {

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Table\Create instead.
     */
    final class Create extends \Cycle\Migrations\Operation\Table\Create
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Table\Drop instead.
     */
    final class Drop extends \Cycle\Migrations\Operation\Table\Drop
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Table\PrimaryKeys instead.
     */
    final class PrimaryKeys extends \Cycle\Migrations\Operation\Table\PrimaryKeys
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Table\Rename instead.
     */
    final class Rename extends \Cycle\Migrations\Operation\Table\Rename
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Table\Update instead.
     */
    final class Update extends \Cycle\Migrations\Operation\Table\Update
    {
    }
}

namespace Spiral\Migrations\Operation\Traits {

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Traits\OptionsTrait instead.
     */
    trait OptionsTrait
    {
        use \Cycle\Migrations\Operation\Traits\OptionsTrait;
    }
}

namespace Spiral\Migrations\Operation\Migrator {

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Migrator\MigrationsTable instead.
     */
    class MigrationsTable extends \Cycle\Migrations\Operation\Migrator\MigrationsTable
    {
    }
}

namespace Spiral\Migrations\Operation\Migration {

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Migration\DefinitionInterface instead.
     */
    interface DefinitionInterface extends \Cycle\Migrations\Operation\Migration\DefinitionInterface
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Migration\ProvidesSyncStateInterface instead.
     */
    interface ProvidesSyncStateInterface extends \Cycle\Migrations\Operation\Migration\ProvidesSyncStateInterface
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Migration\State instead.
     */
    class State extends \Cycle\Migrations\Operation\Migration\State
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Operation\Migration\Status instead.
     */
    final class Status extends \Cycle\Migrations\Operation\Migration\Status
    {
    }
}

namespace Spiral\Migrations\Operation\Exception {

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Exception\AtomizerException instead.
     */
    class AtomizerException extends \Cycle\Migrations\Exception\AtomizerException
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Exception\BlueprintException instead.
     */
    class BlueprintException extends \Cycle\Migrations\Exception\BlueprintException
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Exception\CapsuleException instead.
     */
    class CapsuleException extends \Cycle\Migrations\Exception\CapsuleException
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Exception\ContextException instead.
     */
    class ContextException extends \Cycle\Migrations\Exception\ContextException
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Exception\MigrationException instead.
     */
    class MigrationException extends \Cycle\Migrations\Exception\MigrationException
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Exception\OperationException instead.
     */
    class OperationException extends \Cycle\Migrations\Exception\OperationException
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Exception\RepositoryException instead.
     */
    class RepositoryException extends \Cycle\Migrations\Exception\RepositoryException
    {
    }
}

namespace Spiral\Migrations\Operation\Exception\Operation {

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Exception\Operation\ColumnException instead.
     */
    class ColumnException extends \Cycle\Migrations\Exception\Operation\ColumnException
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Exception\Operation\ForeignKeyException instead.
     */
    class ForeignKeyException extends \Cycle\Migrations\Exception\Operation\ForeignKeyException
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Exception\Operation\IndexException instead.
     */
    class IndexException extends \Cycle\Migrations\Exception\Operation\IndexException
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Exception\Operation\TableException instead.
     */
    class TableException extends \Cycle\Migrations\Exception\Operation\TableException
    {
    }
}

namespace Spiral\Migrations\Config {

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Config\MigrationConfig instead.
     */
    final class MigrationConfig extends \Cycle\Migrations\Config\MigrationConfig
    {
    }
}

namespace Spiral\Migrations\Atomizer {

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Atomizer\RendererInterface instead.
     */
    interface RendererInterface extends \Cycle\Migrations\Atomizer\RendererInterface
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Atomizer\Atomizer instead.
     */
    final class Atomizer extends \Cycle\Migrations\Atomizer\Atomizer
    {
    }

    /**
     * @deprecated Since Cycle ORM 1.0, use Cycle\Migrations\Atomizer\Renderer instead.
     */
    final class Renderer extends \Cycle\Migrations\Atomizer\Renderer
    {
    }
}
