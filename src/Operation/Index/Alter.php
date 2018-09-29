<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Migrations\Operation\Index;

use Spiral\Migrations\CapsuleInterface;
use Spiral\Migrations\Exception\Operation\IndexException;
use Spiral\Migrations\Operation\Traits\OptionsTrait;

class Alter extends Index
{
    use OptionsTrait;

    /**
     * @param string $table
     * @param array  $columns
     * @param array  $options
     */
    public function __construct(string $table, array $columns, array $options = [])
    {
        parent::__construct($table, $columns);
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(CapsuleInterface $capsule)
    {
        $schema = $capsule->getSchema($this->getTable());

        if (!$schema->hasIndex($this->columns)) {
            $columns = join(',', $this->columns);
            throw new IndexException(
                "Unable to alter index '{$schema->getName()}'.({$columns}), no such index"
            );
        }

        $schema->index($this->columns)->unique(
            $this->getOption('unique', false)
        );
    }
}