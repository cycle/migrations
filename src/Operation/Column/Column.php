<?php

/**
 * This file is part of Cycle ORM package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cycle\Migrations\Operation\Column;

use Cycle\Database\Schema\AbstractColumn;
use Cycle\Database\Schema\AbstractTable;
use Spiral\Database\Schema\AbstractTable as SpiralAbstractTable;
use Cycle\Migrations\Exception\Operation\ColumnException;
use Cycle\Migrations\Operation\AbstractOperation;
use Cycle\Migrations\Operation\Traits\OptionsTrait;

abstract class Column extends AbstractOperation
{
    use OptionsTrait;

    /**
     * Some options has set of aliases.
     *
     * @var array
     */
    protected $aliases = [
        'size' => ['length', 'limit'],
        'default' => ['defaultValue'],
        'null' => ['nullable'],
    ];

    /** @var string */
    protected $name;

    /** @var string */
    protected $type;

    /**
     * @param string $table
     * @param string $columns
     * @param string $type
     * @param array  $options
     */
    public function __construct(
        string $table,
        string $columns,
        string $type = 'string',
        array $options = []
    ) {
        parent::__construct($table);
        $this->name = $columns;
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * @param AbstractTable|SpiralAbstractTable $schema The signature of this
     *        argument will be changed to {@see AbstractTable} in future release.
     *
     * @throws \ReflectionException
     *
     * @return AbstractColumn
     */
    protected function declareColumn(SpiralAbstractTable $schema): AbstractColumn
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
