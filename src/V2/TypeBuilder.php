<?php

declare(strict_types=1);

namespace Cycle\Migrations\V2;

use Cycle\Database\DatabaseInterface;
use Cycle\Migrations\Exception\OperationException;

class TypeBuilder
{
    public const ENUM = 'ENUM';
    public const RANGE = 'RANGE';

    private DatabaseInterface $db;
    private string $name;
    private array $values;
    private string $type;

    public function __construct(
        string $name,
        string $type,
        DatabaseInterface $db
    ) {
        $this->db = $db;
        $this->name = $name;
        $this->type = $type;
    }

    public function create(): void
    {
        if (empty($this->values)) {
            throw new OperationException('Values can\'t be empty');
        }

        $values = implode(',', array_map(static fn($v) => "'{$v}'", $this->values));

        $query = sprintf(
            'CREATE TYPE %s AS %s (%s);',
            $this->name,
            $this->type,
            $values
        );

        $this->db->execute($query);
        $this->db->commit();
    }

    public function drop(): void
    {
        $query = sprintf('DROP TYPE %s;', $this->name);
        $this->db->execute($query);
        $this->db->commit();

    }

    public function addValues(array $values): self
    {
        $this->values = $values;

        return $this;
    }
}
