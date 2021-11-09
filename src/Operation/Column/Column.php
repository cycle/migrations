<?php

/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Migrations\Operation\Column;

use Cycle\Database\Schema\AbstractColumn;
use Cycle\Database\Schema\AbstractTable;
use Cycle\Migrations\Exception\Operation\ColumnException;
use Cycle\Migrations\Operation\AbstractOperation;
use Cycle\Migrations\Operation\Traits\OptionsTrait;

abstract class Column extends AbstractOperation
{
    use OptionsTrait;

    /**
     * Some options has set of aliases.
     */
    protected array $aliases = [
        'size' => ['length', 'limit'],
        'default' => ['defaultValue'],
        'null' => ['nullable'],
    ];

    public function __construct(
        string $table,
        protected string $name,
        protected string $type = 'string',
        array $options = []
    ) {
        $this->options = $options;
        parent::__construct($table);
    }

    /**
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

        if ($this->hasOption('default')) {
            $column->defaultValue($this->getOption('default', null));
        }

        return $column;
    }
}
