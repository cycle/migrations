<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Operation\ForeignKey;

use Spiral\Database\ForeignKeyInterface;
use Spiral\Migrations\CapsuleInterface;
use Spiral\Migrations\Exception\Operation\ForeignKeyException;
use Spiral\Migrations\Operation\Traits\OptionsTrait;

class Alter extends ForeignKey
{
    use OptionsTrait;

    /** @var string */
    protected $foreignTable = '';

    /** @var string */
    protected $foreignKey = '';

    /**
     * @param string $table
     * @param string $column
     * @param string $foreignTable
     * @param string $foreignKey
     * @param array  $options
     */
    public function __construct(
        string $table,
        string $column,
        string $foreignTable,
        string $foreignKey,
        array $options
    ) {
        parent::__construct($table, $column);
        $this->foreignTable = $foreignTable;
        $this->foreignKey = $foreignKey;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(CapsuleInterface $capsule)
    {
        $schema = $capsule->getSchema($this->getTable());

        if (!$schema->hasForeignKey($this->column)) {
            throw new ForeignKeyException(
                "Unable to alter foreign key '{$schema->getName()}'.({$this->column}), "
                . "key does not exists"
            );
        }

        $outerSchema = $capsule->getSchema($this->foreignTable);

        if ($this->foreignTable != $this->table && !$outerSchema->exists()) {
            throw new ForeignKeyException(
                "Unable to alter foreign key '{$schema->getName()}'.'{$this->column}', "
                . "foreign table '{$this->foreignTable}' does not exists"
            );
        }

        if ($this->foreignTable != $this->table && !$outerSchema->hasColumn($this->foreignKey)) {
            throw new ForeignKeyException(
                "Unable to alter foreign key '{$schema->getName()}'.'{$this->column}',"
                . " foreign column '{$this->foreignTable}'.'{$this->foreignKey}' does not exists"
            );
        }

        $foreignKey = $schema->foreignKey($this->column)->references(
            $this->foreignTable,
            $this->foreignKey
        );

        /*
         * We are allowing both formats "NO_ACTION" and "NO ACTION".
         */

        $foreignKey->onDelete(str_replace(
            '_',
            ' ',
            $this->getOption('delete', ForeignKeyInterface::NO_ACTION)
        ));

        $foreignKey->onUpdate(str_replace(
            '_',
            ' ',
            $this->getOption('update', ForeignKeyInterface::NO_ACTION)
        ));
    }
}