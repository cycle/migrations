<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
namespace Spiral\Migrations;

use Psr\Log\LoggerAwareInterface;
use Spiral\Core\Component;
use Spiral\Database\DatabaseManager;
use Spiral\Debug\Traits\LoggerTrait;

/**
 * Isolates set of table specific operations and schemas into one place. Kinda repository.
 */
class MigrationContext extends Component implements LoggerAwareInterface, ContextInterface
{
    use LoggerTrait;

    /**
     * Cached set of table schemas.
     *
     * @var array
     */
    private $schemas = [];

    /**
     * @invisible
     * @var DatabaseManager
     */
    protected $dbal = null;

    /**
     * @param DatabaseManager $dbal
     */
    public function __construct(DatabaseManager $dbal)
    {
        $this->dbal = $dbal;
    }

    /**
     * {@inheritdoc}
     */
    public function getDatabase($database)
    {
        return $this->dbal->database($database);
    }

    /**
     * {@inheritdoc}
     */
    public function getTable($database, $table)
    {
        return $this->dbal->database($database)->table($table);
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema($database, $table)
    {
        if (!isset($this->schemas[$database . '.' . $table])) {
            $schema = $this->getTable($database, $table)->schema();

            //We have to declare existed to prevent dropping existed schema
            $this->schemas[$database . '.' . $table] = $schema->declareExisted();
        }

        return $this->schemas[$database . '.' . $table];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Throwable
     */
    public function execute(array $operations)
    {
        /*
         * Executing operation per operation.
         */
        foreach ($operations as $operation) {
            if ($operation instanceof OperationInterface) {
                $operation->execute($this);
            }
        }
    }
}