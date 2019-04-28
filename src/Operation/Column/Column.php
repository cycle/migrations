<?php
/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Migrations\Operation\Column;

use Spiral\Database\Schema\AbstractColumn;
use Spiral\Database\Schema\AbstractTable;
use Spiral\Migrations\Exception\Operation\ColumnException;
use Spiral\Migrations\Operation\AbstractOperation;
use Spiral\Migrations\Operation\Traits\OptionsTrait;

abstract class Column extends AbstractOperation
{
    use OptionsTrait;

    /**
     * Some options has set of aliases.
     *
     * @var array
     */
    protected $aliases = [
        'size'    => ['length', 'limit'],
        'default' => ['defaultValue'],
        'null'    => ['nullable']
    ];

    /** @var string */
    protected $name = '';

    /** @var string */
    protected $type = '';

    /**
     * @param string $table
     * @param string $column
     * @param string $type
     * @param array  $options
     */
    public function __construct(
        string $table,
        string $column,
        string $type = 'string',
        array $options = []
    ) {
        parent::__construct($table);
        $this->name = $column;
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * @param AbstractTable $schema
     *
     * @return AbstractColumn
     * @throws ColumnException
     */
    protected function declareColumn(AbstractTable $schema): AbstractColumn
    {
        $column = $schema->column($this->name);

        //Type configuring
        if (method_exists($column, $this->type)) {
            $arguments = [];

            $method = new \ReflectionMethod($column, $this->type);
            foreach ($method->getParameters() as $parameter) {
                if ($this->hasOption($parameter->getName())) {
                    $arguments[] = $this->getOption($parameter->getName());
                } elseif (!$parameter->isOptional()) {
                    throw new ColumnException(
                        "Option '{$parameter->getName()}' are required to define column with type '{$this->type}'"
                    );
                } else {
                    $arguments[] = $parameter->getDefaultValue();
                }
            }

            call_user_func_array([$column, $this->type], $arguments);
        } else {
            $column->type($this->type);
        }

        $column->nullable($this->getOption('nullable', false));
        $column->defaultValue($this->getOption('default', null));

        return $column;
    }
}